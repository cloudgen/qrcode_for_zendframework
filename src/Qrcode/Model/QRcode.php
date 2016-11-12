<?php
namespace Qrcode\Model;
use \Qrcode\Model\FrameFiller;
use \Qrcode\Model\Config;
use \Qrcode\Model\Constant;
use \Qrcode\Model\QRencode;
use \Qrcode\Model\QRinput;
use \Qrcode\Model\QRmask;
use \Qrcode\Model\QRrawcode;
use \Qrcode\Model\QRspec;
use \Qrcode\Model\QRsplit;
use \Qrcode\Model\QRtools;
class QRcode {

    public $version;
    public $width;
    public $data;
    protected $config;
    
    public function getConfig(){
        return include  'module/Qrcode/config/module.config.php';
    }
    public function __construct($controller){
        $this->config=$this->getConfig();
        //var_dump($this->config);
        //exit(0);
    }
    //----------------------------------------------------------------------
    public function encodeMask(QRinput $input, $mask) {
        if ($input->getVersion() < 0 || $input->getVersion() > QRSPEC_VERSION_MAX) {
            throw new Exception('wrong version');
        }
        if ($input->getErrorCorrectionLevel() > Constant::QR_ECLEVEL_H) {
            throw new Exception('wrong level');
        }

        $raw = new QRrawcode($input);

        QRtools::markTime('after_raw');

        $version = $raw->version;
        $width = QRspec::getWidth($version);
        $frame = QRspec::newFrame($version);

        $filler = new FrameFiller($width, $frame);
        if (is_null($filler)) {
            return NULL;
        }

        // inteleaved data and ecc codes
        for ($i = 0; $i < $raw->dataLength + $raw->eccLength; $i++) {
            $code = $raw->getCode();
            $bit = 0x80;
            for ($j = 0; $j < 8; $j++) {
                $addr = $filler->next();
                $filler->setFrameAt($addr, 0x02 | (($bit & $code) != 0));
                $bit = $bit >> 1;
            }
        }

        QRtools::markTime('after_filler');

        unset($raw);

        // remainder bits
        $j = QRspec::getRemainder($version);
        for ($i = 0; $i < $j; $i++) {
            $addr = $filler->next();
            $filler->setFrameAt($addr, 0x02);
        }

        $frame = $filler->getFrame();
        unset($filler);


        // masking
        $maskObj = new QRmask();
        if ($mask < 0) {

            if (Config::QR_FIND_BEST_MASK) {
                $masked = $maskObj->mask($width, $frame, $input->getErrorCorrectionLevel());
            } else {
                $masked = $maskObj->makeMask($width, $frame, (intval(Config::QR_DEFAULT_MASK) % 8), $input->getErrorCorrectionLevel());
            }
        } else {
            $masked = $maskObj->makeMask($width, $frame, $mask, $input->getErrorCorrectionLevel());
        }

        if ($masked == NULL) {
            return NULL;
        }

        QRtools::markTime('after_mask');

        $this->version = $version;
        $this->width = $width;
        $this->data = $masked;

        return $this;
    }

    //----------------------------------------------------------------------
    public function encodeInput(QRinput $input) {
        return $this->encodeMask($input, -1);
    }

    //----------------------------------------------------------------------
    public function encodeString8bit($string, $version, $level) {
        if (string == NULL) {
            throw new Exception('empty string!');
            return NULL;
        }

        $input = new QRinput($version, $level);
        if ($input == NULL)
            return NULL;

        $ret = $input->append($input, Constant::QR_MODE_8, strlen($string), str_split($string));
        if ($ret < 0) {
            unset($input);
            return NULL;
        }
        return $this->encodeInput($input);
    }

    //----------------------------------------------------------------------
    public function encodeString($string, $version, $level, $hint, $casesensitive) {

        if ($hint != Constant::QR_MODE_8 && $hint != Constant::QR_MODE_KANJI) {
            throw new Exception('bad hint');
            return NULL;
        }

        $input = new QRinput($version, $level);
        if ($input == NULL)
            return NULL;

        $ret = QRsplit::splitStringToQRinput($string, $input, $hint, $casesensitive);
        if ($ret < 0) {
            return NULL;
        }

        return $this->encodeInput($input);
    }

    //----------------------------------------------------------------------
    public static function png($text, $outfile = false, $level = Constant::QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint = false) {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encodePNG($text, $outfile, $saveandprint = false);
    }

    //----------------------------------------------------------------------
    public static function text($text, $outfile = false, $level = Constant::QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encode($text, $outfile);
    }

    //----------------------------------------------------------------------
    public static function raw($text, $outfile = false, $level = Constant::QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encodeRAW($text, $outfile);
    }

}
