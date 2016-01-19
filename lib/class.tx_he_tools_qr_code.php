<?php

require_once(t3lib_extMgm::extPath('he_tools').'res/qr/class.qrcode.php');

class tx_he_tools_qr_code  {

  public static function getQrCodeImg($qrCodeText, $size=200, $padding=10, $errCorrection='middle') {
    $qrCode = new Endroid\QrCode\QrCode();
    $qrCode->setText($qrCodeText)
      ->setSize($size)
      ->setPadding($padding)
      ->setErrorCorrection($errCorrection)
      ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
      ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
    ;
    $imageUri = $qrCode->getDataUri();
    return '<img src="' . $imageUri . '" />';
  }

}

?>