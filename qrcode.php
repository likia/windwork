<?php
$text = @$_REQUEST['text'];

if(!$text) {
	die('Error param!');
}

require_once("src/libs/QRCode/Image/QRCode.php");
require_once("src/core/util/Encoder.php");

$qr = new Image_QRCode();

$options = array(
  "module_size" => 8,
  //"version" => 5
);

$text = \core\util\Encoder::decode($text);
$qr->makeCode($text, $options);

