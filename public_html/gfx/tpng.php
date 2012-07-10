<?php

$c=$_GET[c];
$t=$_GET[t];

if(strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")) {
	header('Content-type: image/gif');

	$im=imagecreatetruecolor(1,1);

	$c=imagecolorallocate($im,0,0,0);
	imagefilledrectangle($im,0,0,1,1,$c);
	imagecolortransparent($im,$c);

	imagegif($im);
}

header('Content-type: image/png');

$im=imagecreatetruecolor(1,1);

imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

sscanf($c,"%02X%02X%02X",&$r,&$g,&$b);

$c=imagecolorallocatealpha($im,$r,$g,$b,$t);

imagefilledrectangle($im,0,0,1,1,$c);

imagepng($im);

?>
