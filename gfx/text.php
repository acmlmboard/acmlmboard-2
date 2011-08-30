<?php

include 'fontlib.php';

header('Content-type: image/gif');

$im=imagecreatetruecolor(8*strlen($_GET['str']),8);

$black=imagecolorallocate($im,255,0,255);

imagefilledrectangle($im,0,0,8*strlen($_GET['str']),8,$black);

$fontW=fontc($r1,$g1,$b1, $r2, $g2, $b2,  $r3, $g3, $b3);
frender($im,$fontW, 0, 0,0,$_GET['str']);

imagecolortransparent($im,$black);

imagegif($im);

?>
