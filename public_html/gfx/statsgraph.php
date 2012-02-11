<?php

require 'gfxlib.php';
require 'fontlib.php';
require 'ringbuf.php';

$im=imagecreatetruecolor(960,340);

imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

$cblank=imagecolorallocatealpha($im,0,0,0,127);
$cwhite=imagecolorallocatealpha($im,205,205,255,80);
$ch1=imagecolorallocatealpha($im,0,0,0,100);
$ch2=imagecolorallocatealpha($im,255,255,255,120);
$c1=imagecolorallocatealpha($im,50,50,255,76);
$c2=imagecolorallocatealpha($im,100,100,255,0);
$c3=imagecolorallocatealpha($im,100,100,255,96);

imagefilledrectangle($im,0,0,960,340,$cblank);

/* background */
imagealphablending($im,TRUE);
for($i=0;$i<320;$i+=20) {
	imagefilledrectangle($im,0,$i,960,$i+9,$ch1);
	imagettftext($im,7,0, 906, $i+14, $cwhite, "verdana.ttf", (310-$i)*2);
	imagefilledrectangle($im,896,$i+10,900,$i+10,$cwhite);
}
for($i=0;$i<960;$i+=128) imagefilledrectangle($im,$i,0,$i+64,320,$ch1);
imagealphablending($im,FALSE);
imagefilledrectangle($im,0,320,960,340,$ch2);

$stats=$sql->query('SELECT * FROM dailystats WHERE views>9000000 ORDER BY views');
$x=0;
$y=1000; $yold=1000;
$abuf=new ringbuf;
$abuf->size=8;
imagealphablending($im,TRUE);
$posts=101800;
while($day=$sql->fetch($stats)){
  $x++;
  $y=$day[posts]-$posts;
  $abuf->push($y);

  imageline($im,$x,320- $abuf->get()/2,$x,320- $y/2,$c1);
  
  imageline($im,$x-1,320-$yold/2 -1,$x-0,320-$abuf->get()/2 -1,$c3);
  imageline($im,$x-1,320-$yold/2 +1,$x-0,320-$abuf->get()/2 +1,$c3);
  imageline($im,$x-1,320-$yold/2,   $x-0,320-$abuf->get()/2   ,$c2);

  if(!(($x-1)%64)) {
  	imagettftext($im,7,0, $x, 10, $cwhite, "/var/sites/acmlmboard/board/gfx/verdana.ttf", $day[date]);
  }

  $users=$day[users];
  $posts=$day[posts];
  $threads=$day[threads];
  $views=$day[views];
  $yold=$abuf->get();
}

imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

header('Content-type: image/png');

imagepng($im);

?>
