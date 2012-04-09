<?php

include 'fontlib.php';

$newcount = $_GET[num];
$type = $_GET[type];

$stoprender = false;
header('Content-type: image/png');

if ($newcount) $x=18;
else $x=8;
$y = 6*3;


switch ($type) {
	case "n": 
		$fcol="Y";
		$text="NEW";
		break;

	case "N":
	case "hn":
	case "!n":
		$fcol="O";
		$text="NEW";
		break;

	case "h": 
		$fcol="R";
		$text="HOT";
		break;

	case "!": 
		$fcol="R";
		$text=" ! ";
		break;

	case "e": 
		$fcol="G";
		$text="EDT";
		break;

	case "E":
	case "!e": 
		$fcol="R";
		$text="EDT";
		break;

	case "x": 
		$fcol="W";
		$text="OFF";
		break;

	case "X":
	case "xh":
	case "x!":
		$fcol="R";
		$text="OFF";
		break;

	case "xh": 
		$fcol="Y";
		$text="OFF";
		break;

	case "xhn": 
		$fcol="R";
		$text="OFF";
		break;

	case "o":
		Header("Location:../img/status/off.png");
		return;
		break;

	case "O":
	case "ho":  
	case "o!": 
		Header("Location:../img/status/offhot.png");
		return;
		break;

	case "on": 
		Header("Location:../img/status/offnew.png");
		return;
		break;

	case "On":
	case "ohn":
	case "o!n":
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

 $fontcolor[Y]=fontcN(255,250,240, 255,240, 80,  0, 0, 0);
 $fontcolor[R]=fontcN(255,230,220, 240,160,150,  0, 0, 0);
 $fontcolor[G]=fontcN(190,255,190,  60,220, 60,  0, 0, 0);
 $fontcolor[B]=fontcN(160,240,255, 120,190,240,  0, 0, 0);
 $fontcolor[W]=fontcN(255,255,255, 210,210,210,  0, 0, 0);
 $fontcolor[O]=fontcN(255,213,159, 255,158, 33,  0, 0, 0);


if (!$stoprender) frenderN($im,$fontcolor[$fcol], 2, 0,0,$text);

switch (strlen($newcount))	{
	case 1: 
		$z = 7;
		break;
	case 2:
		$z = 4;
		break;
	default:
		$z = 2;
		break;

}
if ($newcount > 999 || $newcount < 0){
	$newcount = "NAN";
	$z = 0;
}

if (!$stoprender) frenderN($im,$fontcolor[W], $z, 9,0,$newcount);

imagecolortransparent($im,$black);

imagepng($im);

?>
