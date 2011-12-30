<?php
$im=imagecreatetruecolor(690,20);

if($n==0) $n=1;

imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

function cap($n)
{
return ($n>255?255:($n<0?0:$n));
}

$cblank=imagecolorallocatealpha($im,0,0,0,127);
$cuhl=imagecolorallocate($im,cap($r+80),cap($g+80),cap($b+80));
$chl=imagecolorallocate($im,cap($r+50),cap($g+50),cap($b+50));
$cstd=imagecolorallocate($im,$r,$g,$b);
$cshadow=imagecolorallocate($im,cap($r-80),cap($g-80),cap($b-80));
$calpha=imagecolorallocatealpha($im,0,0,0,110);
$cwhite=imagecolorallocate($im,255,255,255);

//imagecolortransparent($im,$cblank);

imagefilledrectangle($im,0,0,690,20,$cblank);

imagefilledrectangle($im,0,0,600*$z/$n,20,$cstd);
imagefilledrectangle($im,0,2,600*$z/$n,3,$cuhl);
imagefilledrectangle($im,0,14,600*$z/$n,20,$cshadow);
//imagefilledrectangle($im,600*$z/$n-1,0,600*$z/$n,20,$cshadow);

$str="";
$str=sprintf("%.1f%% ($z)",100*$z/$n);

imagettftext($im,10,0, floor(600*$z/$n)+3, 14, $cwhite, "/var/sites/acmlmboard/board/gfx/verdana.ttf", $str);

imagesavealpha($im,FALSE);
imagealphablending($im,TRUE);
for($i=0;$i<600;$i+=10) {
  imagefilledrectangle($im,$i+0,0,$i+4,20,$calpha);
//  imagefilledrectangle($im,$i+5,5,$i+9,10,$calpha);
//  imagefilledrectangle($im,$i+0,10,$i+4,14,$calpha);
//  imagefilledrectangle($im,$i+5,15,$i+9,20,$calpha);
//  imagefilledrectangle($im,0,$i,600,$i,$calpha);
}
imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

imagepng($im);

?>
