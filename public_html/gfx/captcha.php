<?php

include 'gfxlib.php';
include 'fontlib.php';

header('Content-type: image/gif');

$str=explode(',',decryptpwd($_GET['a']));
$str=$str[1];

if($_GET[l]==1) $str=substr($str,0,3); else $str=substr($str,3,3);

$im=imagecreatetruecolor(8*strlen($str)+2,10);

$black=imagecolorallocate($im,255,0,255);

imagefilledrectangle($im,0,0,8*strlen($str),8,$black);

$r1=128+rand()%128;
$g1=128+rand()%128;
$b1=128+rand()%128;
$r2=$r1/1.5; $r3=$r2/2;
$g2=$g1/1.5; $g3=$r2/2;
$b2=$b1/1.5; $b3=$b2/2;

$fontW=fontc($r1,$g1,$b1, $r2, $g2, $b2, $r3,$g3,$b3);
frender($im,$fontW, 1, 1,0,$str);

imagecolortransparent($im,$black);

imagegif($im);


?>
