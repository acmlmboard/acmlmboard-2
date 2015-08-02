<?php
 require 'gfxlib.php';

	if ($_GET['order']) {
		$orderby	= "`cnt`";
	} else {
		$orderby	= "`u`.`posts`";
	}

	$startdate	= floor((ctime() - (6 * 60 * 60)) / 86400) * 86400 + (6 * 60 * 60);
	$enddate		= $startdate + 86400;

//	print ctime() . " / $startdate / $enddate";

	$users	= $sql -> query("SELECT COUNT(*) as `cnt`, `u`.`name`, `u`.`posts` ".
							"FROM `posts` `p` ".
							"LEFT JOIN `users` `u` ON `p`.`user` = `u`.`id` ".
							"WHERE `p`.`date` >= '$startdate' AND `p`.`date` < '$enddate' ".
							"GROUP BY `p`.`user` ".
							"ORDER BY $orderby DESC ".
							"LIMIT 20");


 $img=ImageCreateTrueColor(512,192);
 $c[bg]    =ImageColorAllocate($img, 40, 40, 90);
 $c[bxb0]  =ImageColorAllocate($img,  0,  0,  0);
 $c[bxb1]  =ImageColorAllocate($img,200,170,140);
 $c[bxb2]  =ImageColorAllocate($img,155,130,105);
 $c[bxb3]  =ImageColorAllocate($img,110, 90, 70);
 for($i=0;$i<100;$i++)
   $c[$i]  =ImageColorAllocate($img, 65+$i/2, 16, 25+$i/4);
 $c[bar][1]=ImageColorAllocate($img,255,198,222);
 $c[bar][2]=ImageColorAllocate($img,255,115,181);
 $c[bar][3]=ImageColorAllocate($img,255,156, 57);
 $c[bar][4]=ImageColorAllocate($img,255,231,165);
 $c[bar][5]=ImageColorAllocate($img,173,231,255);
 $c[bar][6]=ImageColorAllocate($img, 57,189,255);
 $c[bar][7]=ImageColorAllocate($img, 75,222, 75);
// ImageColorTransparent($img,0);

   $c[gridline]  =ImageColorAllocateAlpha($img, 200,110,100, 100);
   $c[alternate]  =ImageColorAllocateAlpha($img,   0,  0,  0, 110);


 box(0,1,64,23); //44*8=352

// $fontY=fontc(255,250,240, 255,240, 80,  0, 0, 0);
// $fontR=fontc(255,230,220, 240,160,150,  0, 0, 0);
// $fontG=fontc(190,255,190,  60,220, 60,  0, 0, 0);
 $fontB=fontc(160,240,255, 120,190,240,  0, 0, 0);
 $fontR=fontc(255,235,200, 255,210,160,  0, 0, 0);
 $fontW=fontc(255,255,255, 210,210,210,  0, 0, 0);

 box(1,0,11,3); //44*8=352
 twrite($fontW,  2,  1, 0,"User");

 box(13,0,28,3); //44*8=352
 twrite($fontW, 14,  1, 0,"Total");

 box(42,0,21,3); //44*8=352
 twrite($fontW, 43,  1, 0,"Today");

/* $sc[1]=   1;
 $sc[2]=  10;
 $sc[3]=  40;
 $sc[4]= 100;
 $sc[5]= 200;
 $sc[6]= 300;
 $sc[7]= 400;
 $sc[8]=99999999;
*/

 // more dramatic, better for lower post ranges
 // doubtful it'll ever go over 100 scale (you'd need 35.2k posts for that anyway)
 $sc[ 1]=       0.1;
 $sc[ 2]=       0.5;
 $sc[ 3]=       1;
 $sc[ 4]=       2;
 $sc[ 5]=       5;
 $sc[ 6]=      10;
 $sc[ 7]=      50;
 $sc[ 8]=99999999;

// for($s=1;($topposts/$sc[$s])>176;$s++);
// if(!$sc[$s]) $sc[$s]=1;

// imageline($img, 328, 0, 328, 255, $c[bar][7]);

 for ($i = 4; $i <= 23; $i += 2) {
	 imagefilledrectangle($img, 8, $i * 8, 504, $i * 8 + 7, $c[alternate]);
	}

 for ($i = 152; $i <= 332; $i += 10) {
	 imageline($img, $i, 3 * 8, $i, 23 * 8, $c[gridline]);
	}
	imageline($img, 152, 23*8, 332, 23*8, $c[gridline]);

 for ($i = 384; $i <= 504; $i += 10) {
	 imageline($img, $i, 3 * 8, $i, 23 * 8, $c[gridline]);
	}
	imageline($img, 384, 23*8, 504, 23*8, $c[gridline]);

 for ($i = 0; $x = $sql -> fetch($users); $i++) $userdat[$i] = $x;

 if (!$userdat) $userdat = array();

	$userdat2 = $userdat;
	foreach ($userdat2 as $key => $row) {
	   $postcounts[$key]	= $row['posts'];
	   $dailycounts[$key]	= $row['cnt'];
	   $xxx++;
	}

	if ($xxx) {
		$maxp	= max($postcounts);
		$maxd	= max($dailycounts);
	}
	
     for($s=1;($maxp/$sc[$s])>176;$s++);
     if(!$sc[$s]) $sc[$s]=1;

	 for($s2=1;($maxd/$sc[$s2])>120;$s2++);
     if(!$sc[$s2]) $sc[$s2]=1;

//	print "$maxp / $maxd";
 $i	= -1;

 foreach($userdat as $i => $user) {    // ($i=1;$user=$sql->fetch($users);$i++){
//	$i++;
	$name	= $user['name'];
	$posts	= $user['posts'];
	$daily	= $user['cnt'];
	$vline	= $i + 3;

//	print_r($user);

//	print $i ."-";
   twrite($fontR,  1,$vline    , 0,substr($name,0,12));
   twrite($fontW, 13,$vline    , 5,$posts);
//   twrite($fontG, 13,$vline + 1, 5,$daily);

   twrite($fontW, 42,$vline, 5,$daily);

   
   ImageFilledRectangle($img,153,$vline*8+1,152+$posts/$sc[$s],$vline*8+7,$c[bxb0]);
   ImageFilledRectangle($img,152,$vline*8  ,151+$posts/$sc[$s],$vline*8+6,$c[bar][$s]);
   ImageFilledRectangle($img,385,$vline*8+1,385+$daily/$sc[$s2],$vline*8+7,$c[bxb0]);
   ImageFilledRectangle($img,384,$vline*8  ,384+$daily/$sc[$s2],$vline*8+6,$c[bar][$s2]);
 }

 Header('Content-type:image/png');
 ImagePNG($img);
 ImageDestroy($img);

function twrite($font,$x,$y,$l,$text){
  global $img;
  $x*=8;
  $y*=8;
  $text.='';
  if(strlen($text)<$l) $x+=($l-strlen($text))*8;
  for($i=0;$i<strlen($text);$i++)
    ImageCopy($img,$font,$i*8+$x,$y,(ord($text[$i])%16)*8,floor(ord($text[$i])/16)*8,8,8);
}
function fontc($r1,$g1,$b1,$r2,$g2,$b2,$r3,$g3,$b3){
  $font=ImageCreateFromPNG('font.png');
  ImageColorTransparent($font,1);
  ImageColorSet($font,6,$r1,$g1,$b1);
  ImageColorSet($font,5,($r1*2+$r2)/3,($g1*2+$g2)/3,($b1*2+$b2)/3);
  ImageColorSet($font,4,($r1+$r2*2)/3,($g1+$g2*2)/3,($b1+$b2*2)/3);
  ImageColorSet($font,3,$r2,$g2,$b2);
  ImageColorSet($font,0,$r3,$g3,$b3);
  return $font;
}
function box($x,$y,$w,$h){
  global $img,$c;
  $x*=8;
  $y*=8;
  $w*=8;
  $h*=8;
  ImageRectangle($img,$x+0,$y+0,$x+$w-1,$y+$h-1,$c[bxb0]);
  ImageRectangle($img,$x+1,$y+1,$x+$w-2,$y+$h-2,$c[bxb3]);
  ImageRectangle($img,$x+2,$y+2,$x+$w-3,$y+$h-3,$c[bxb1]);
  ImageRectangle($img,$x+3,$y+3,$x+$w-4,$y+$h-4,$c[bxb2]);
  ImageRectangle($img,$x+4,$y+4,$x+$w-5,$y+$h-5,$c[bxb0]);
  for($i=5;$i<$h-5;$i++){
    $n=(1-$i/$h)*100;
    ImageLine($img,$x+5,$y+$i,$x+$w-6,$y+$i,$c[$n]);
  }
}
?>