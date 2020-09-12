<?php
session_start();

$font = "../cache/fonts/leelawdb.ttf";
$image = imagecreatetruecolor(100, 24);
$wit = imagecolorallocate($image, 255, 255, 255);
$paars = imagecolorallocate($image, 204, 119, 255);

imagefilledrectangle($image, 0, 0, 100, 24, $wit);

imagecolortransparent($image, $wit);

$chars = array_merge(range(0, 9), range('A', 'Z'));

$text = Array('',
	$chars[rand(0, (count($chars) - 1))],
	$chars[rand(0, (count($chars) - 1))],
	$chars[rand(0, (count($chars) - 1))],
	$chars[rand(0, (count($chars) - 1))],
	$chars[rand(0, (count($chars) - 1))],
	$chars[rand(0, (count($chars) - 1))]
);

$_SESSION['captcha'] = $text[1] . $text[2] . $text[3] . $text[4] . $text[5] . $text[6];

foreach($text as $key => $char) {
	imagettftext($image, 14, rand(-15, 15), (($key * 14) - 7), 21, imagecolorallocate($image, rand(100, 255), rand(100, 210), rand(100, 210)), $font, $char);
}

header("Content-Type: image/png");
imagepng($image);
imagedestroy($image);
?>