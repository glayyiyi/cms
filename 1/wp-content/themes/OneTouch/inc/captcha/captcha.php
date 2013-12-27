<?php
session_start();

$captchaTextSize = 6;

do {
    $md5Hash = md5(microtime() * mktime());
    preg_replace('([1aeilou0])', "", $md5Hash);
} while(strlen($md5Hash) < $captchaTextSize);

$key = substr($md5Hash, 0, $captchaTextSize);

$_SESSION['key'] = md5($key);

$captchaImage = imagecreatefrompng("images/captcha.png");
$textColor    = imagecolorallocate($captchaImage, 78, 97, 109);
$lineColor    = imagecolorallocate($captchaImage, 132, 151, 164);
$imageInfo    = getimagesize("images/captcha.png");

$linesToDraw = 20;

for($i = 0; $i < $linesToDraw; $i++)  
{
    $xStart = mt_rand( 0, $imageInfo[0]);
    $xEnd = mt_rand( 0, $imageInfo[0]);
    imageline( $captchaImage, $xStart, 0, $xEnd, $imageInfo[1], $lineColor );
}

imagettftext($captchaImage, 20, 0, 22, 27, $textColor, "fonts/SourceSansPro-Light.ttf", $key );

header("Content-type: image/png");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Fri, 19 Jan 1994 05:00:00 GMT");
header("Pragma: no-cache");

imagepng($captchaImage);