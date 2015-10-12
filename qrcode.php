<?php
$start = microtime(1);
$text = @$_REQUEST['text'];

if(!$text) {
	die('Error param!');
}


require_once 'src/libs/QrCode/src/QrCode.php';
require_once("src/core/util/Encoder.php");


header('content-type:image/png');

$text = \core\util\Encoder::decode($text);
$text = mb_substr($text, 0, 200, 'UTF-8');
$qrCode = new \Endroid\QrCode\QrCode();
$qrCode
	->setText($text)
	->setSize(300)
	->setPadding(10)
	->setErrorCorrection('high')
	->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
	->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
	//->setLabel('My label')
	//->setLabelFontSize(16)
	->render();
