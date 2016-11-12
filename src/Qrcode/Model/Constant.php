<?php
namespace Qrcode\Model;
/**
 * QRConstant 
 */ 
final class Constant {

    const QR_MODE_NUL = -1;
    const QR_MODE_NUM = 0;
    const QR_MODE_AN = 1;
    const QR_MODE_8 = 2;
    const QR_MODE_KANJI = 3;
    const QR_MODE_STRUCTURE = 4;
    // Levels of error correction.

    const QR_ECLEVEL_L = 0;
    const QR_ECLEVEL_M = 1;
    const QR_ECLEVEL_Q = 2;
    const QR_ECLEVEL_H = 3;
    // Supported output formats

    const QR_FORMAT_TEXT = 0;
    const QR_FORMAT_PNG = 1;

}

