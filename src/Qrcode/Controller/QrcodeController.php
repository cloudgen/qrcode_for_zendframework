<?php

namespace Qrcode\Controller;
use Zend\Mvc\Controller\AbstractActionController;

use Qrcode\Model\QRcode;

class QrcodeController extends AbstractActionController {

    public function indexAction() {
        $url = "https://play.google.com/store/apps/details?id=com.dimcook.app";
        //$png=new QRcode($this);
        QRcode::png($url);
        return false;
    }

}
