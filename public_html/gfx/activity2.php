<?php

require 'gfxlib.php';
require 'fontlib.php';
require 'ringbuf.php';

$im=imagecreatetruecolor(1100,340);

imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

$cblank=imagecolorallocatealpha($im,0,0,0,127);
$cwhite=imagecolorallocatealpha($im,205,205,255,80);
$ch1=imagecolorallocatealpha($im,0,0,0,100);
$ch2=imagecolorallocatealpha($im,255,255,255,120);
$c1=imagecolorallocatealpha($im,50,50,255,76);
$c2=imagecolorallocatealpha($im,100,100,255,0);
$c3=imagecolorallocatealpha($im,100,100,255,96);

imagefilledrectangle($im,0,0,1100,340,$cblank);

/* background */
imagealphablending($im,TRUE);
for($i=0;$i<320;$i+=20) {
	imagefilledrectangle($im,0,$i,1100,$i+9,$ch1);
	imagettftext($im,7,0, 1034, $i+14, $cwhite, "verdana.ttf", (310-$i)/5);
	imagefilledrectangle($im,1024,$i+10,1028,$i+10,$cwhite);
}
for($i=0;$i<1100;$i+=128) imagefilledrectangle($im,$i,0,$i+64,320,$ch1);
imagealphablending($im,FALSE);
imagefilledrectangle($im,0,320,1100,340,$ch2);

$u = checknumeric($_GET['u']);
//checknumeric($u);

$n=$sql->resultq("SELECT name FROM users WHERE id=$u");
imagettftext($im,7,0,7,30,$cwhite, "verdana.ttf", "Activity stats for $n\n  bold: 8-day average\n  thin: daily postcount");

$stats=$sql->query('SELECT FROM_UNIXTIME(p.date,"%y-%m-%d") date, (FLOOR(p.date/(24*60*60))) q, COUNT(*) c FROM posts p WHERE p.user='.$u.' GROUP BY FLOOR(p.date/(24*60*60)) UNION SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(),"%y-%m-%d") date, (FLOOR(UNIX_TIMESTAMP()/(24*60*60))+1) q, 0 c');
$x=0;
$y=0; $yold=0;
$abuf=new ringbuf;
$abuf->size=8;
imagealphablending($im,TRUE);
$day=$sql->fetch($stats);
$yold=$day[c];
$q=$day[q]-1;
do{
  $x++;
  $y=$day[c];
  while($q+1 != $day[q]) {
    ++$q; ++$x; $abuf->push(0);
    imageline($im,$x,320- $abuf->get()*5,$x,320,$c1);
    imageline($im,$x-1,320-$yold*5 -1,$x-0,320-$abuf->get()*5 -1,$c3);
    imageline($im,$x-1,320-$yold*5 +1,$x-0,320-$abuf->get()*5 +1,$c3);
    imageline($im,$x-1,320-$yold*5,   $x-0,320-$abuf->get()*5   ,$c2);
    if(!(($x-2)%64)) {
      imagettftext($im,7,0, $x, 10, $cwhite, "verdana.ttf", $day[date]);
    }
    $yold=$abuf->get();
  }
  $q=$day[q];
  $abuf->push($y);

  imageline($im,$x,320- $abuf->get()*5,$x,320- $y*5,$c1);
  
  imageline($im,$x-1,320-$yold*5 -1,$x-0,320-$abuf->get()*5 -1,$c3);
  imageline($im,$x-1,320-$yold*5 +1,$x-0,320-$abuf->get()*5 +1,$c3);
  imageline($im,$x-1,320-$yold*5,   $x-0,320-$abuf->get()*5   ,$c2);

  if(!(($x-1)%64)) {
  	imagettftext($im,7,0, $x, 10, $cwhite, "verdana.ttf", $day[date]);
  }

  $users=$day[users];
  $posts=$day[posts];
  $threads=$day[threads];
  $views=$day[views];
  $yold=$abuf->get();
}while($day=$sql->fetch($stats));

imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

header('Content-type: image/png');

imagepng($im);

?>
