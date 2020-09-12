<?php
header("Cache-Control: max-age=604800, must-revalidate");
header("Content-Type: image/png");
$r = ((isset($_GET['r']) && is_numeric($_GET['r'])) ? $_GET['r'] : 0);
$g = ((isset($_GET['g']) && is_numeric($_GET['g'])) ? $_GET['g'] : 0);
$b = ((isset($_GET['b']) && is_numeric($_GET['b'])) ? $_GET['b'] : 0);
$a = ((isset($_GET['a']) && is_numeric($_GET['a'])) ? $_GET['a'] : 1);

$im = imagecreatetruecolor(1, 1);

$temp = imagecolorallocate($im, ($r + 1), $g, $b);
imagecolortransparent($im, $temp);
imagefill($im, 0, 0, $temp);

imagefill($im, 0, 0, imagecolorallocatealpha($im, $r, $g, $b, ((1 - $a) * 127)));
imagesavealpha($im, true);

imagepng($im);
imagedestroy($im);
?>