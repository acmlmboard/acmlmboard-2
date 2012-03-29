<?php

include 'fontlib.php';

$newcount = $_GET[num];
$type = $_GET[type];

header('Content-type: image/png');

if ($newcount) $x=16;
else $x=8;
$y = 8*3;


switch ($type) {
	case "n": 
		$fcol="Y";
		$text="NEW";
		break;

	case "N": 
		$fcol="R";
		$text="NEW";
		break;

	case "h": 
		$fcol="R";
		$text="HOT";
		break;

	case "e": 
		$fcol="G";
		$text="EDT";
		break;

	case "E": 
		$fcol="R";
		$text="EDT";
		break;

	case "o": 
		Header("Location:../img/status/off.png");
		return;
		break;

	case "O": 
		Header("Location:../img/status/offhot.png");
		return;
		break;

	case "on": 
		Header("Location:../img/status/offnew.png");
		return;
		break;

	case "On": 
		Header("Location:../img/status/offhotnew.png");
		return;
		break;


	default:
		$stoprender = true;
		$x = 1;
		$y = 1;
		break;

}

$im=imagecreatetruecolor($y,$x);

$black=imagecolorallocate($im,255,0,255);

imagefilledrectangle($im,0,0,$y,$x,$black);

 $fontcolor[Y]=fontc(255,250,240, 255,240, 80,  0, 0, 0);
 $fontcolor[R]=fontc(255,230,220, 240,160,150,  0, 0, 0);
 $fontcolor[G]=fontc(190,255,190,  60,220, 60,  0, 0, 0);
 $fontcolor[B]=fontc(160,240,255, 120,190,240,  0, 0, 0);
 $fontcolor[W]=fontc(255,255,255, 210,210,210,  0, 0, 0);

if (!$stoprender) frender($im,$fontcolor[$fcol], 0, 0,0,$text);

switch (strlen($newcount))	{
	case 1: 
		$z = 8;
		break;
	case 2:
		$z = 4;
		break;
	default:
		$z = 0;
		break;

}
if ($newcount > 999 || $newcount < 0){
	$newcount = "NAN";
	$z = 0;
}

if (!$stoprender) frender($im,$fontcolor[W], $z, 9,0,$newcount);

imagecolortransparent($im,$black);

imagepng($im);

?>
