<?php

require 'gfxlib.php';
require 'fontlib.php';
require 'ringbuf.php';

$im=imagecreatetruecolor(280,340);

imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

$cblank=imagecolorallocatealpha($im,0,0,0,127);
$cwhite=imagecolorallocatealpha($im,255,255,255,70);
$ch1=imagecolorallocatealpha($im,0,0,0,100);
$ch2=imagecolorallocatealpha($im,255,255,255,113);
$c1=imagecolorallocatealpha($im,50,50,255,76);
$c2=imagecolorallocatealpha($im,100,100,255,0);
$c3=imagecolorallocatealpha($im,100,100,255,96);

imagefilledrectangle($im,0,0,280,340,$cblank);

/* background */
imagealphablending($im,TRUE);

$count=$sql->resultq("SELECT COUNT(*) FROM forums WHERE id>0 AND private=0");

$y=1; $max=0;
$f=$sql->query("SELECT id,title FROM forums WHERE id>0 AND private=0");
while($d=$sql->fetch($f)) {
	$a=$sql->resultq("SELECT COUNT(*) FROM posts p, threads t WHERE t.id=p.thread AND t.forum=$d[id] AND p.date>".(ctime()-86400));
	imagettftext($im,7,0,1,($y++)*(340/$count)-5,$cwhite, "/var/sites/acmlmboard/board/gfx/verdana.ttf",$d[title]);
	//imagettftext($im,8,0,277-5*strlen((string)$a),($y-1)*(340/$count)-5,$c2,"/var/sites/acmlmboard/board/gfx/verdana.ttf",$a);
	if($a>$max) $max=$a;
}

for($i=0;$i<$max;$i+=20) {
	imagefilledrectangle($im,280-(($i+10)*280/$max),0,280-($i*280/$max),340,$ch1);
}

$y=0;
$f=$sql->query("SELECT id,title FROM forums WHERE id>0 AND private=0");
while($d=$sql->fetch($f)) {
	$a=$sql->resultq("SELECT COUNT(*) FROM posts p, threads t WHERE t.id=p.thread AND t.forum=$d[id] AND p.date>".(ctime()-86400));
	imagefilledrectangle($im,280-($a*280/$max),($y++)*(340/$count),280,$y*(340/$count) -2,$ch2);
}

imagealphablending($im,FALSE);
imagesavealpha($im,TRUE);

header('Content-type: image/png');

imagepng($im);

?>
