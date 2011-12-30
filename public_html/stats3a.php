<?php
 require 'lib/function.php';
// obviously this will have to be fixed since DOES NOT WORK THAT WAY
// other things MAY NEED FIXING UPPING
 $user=array();
 $user['regdate']	= $sql -> resultq("SELECT MIN(`regdate`) FROM users");
 $user['regdate'] = 1171853565;
 $max				= ceil(($sql -> resultq("SELECT COUNT(*) FROM `posts`") + 1) / 5000) * 5000;
 $alen				= 30;
// $max				= 5500;

 $vd=date('m-d-y', $user['regdate']);
 $dd=mktime(0,0,0,substr($vd,0,2),substr($vd,3,2),substr($vd,6,2));
 $dd2=mktime(0,0,0,substr($vd,0,2),substr($vd,3,2)+1,substr($vd,6,2));


 $days=floor((ctime()-$dd)/86400);
 $scalex	= 3;
 $scaley	= 100;
 $m			= $max / $scaley;
 //die("ur: ". $user['regdate'] ." - days: $days");

 $img=ImageCreateTrueColor($days * $scalex,$m);

 $c[bg]= ImageColorAllocate($img,  0,  0,  0);
 $c[bg1]=ImageColorAllocate($img,  0,  0, 60);
 $c[bg2]=ImageColorAllocate($img,  0,  0, 80);
 $c[bg3]=ImageColorAllocate($img, 40, 40,100);
 $c[mk1]=ImageColorAllocate($img, 60, 60,130);
 $c[mk2]=ImageColorAllocate($img, 80, 80,150);
 $c[bar]=ImageColorAllocate($img,250,190, 40);
 $c[pt] =ImageColorAllocate($img,250,250,250);
 for($i=0;$i<$days;$i++){
   $num=date('m',$dd+$i*86400)%2+1;
   if(date('m-d',$dd+$i*86400)=='01-01') $num=3;
   ImageFilledRectangle($img,$i * $scalex,$m,($i + 1) * $scalex - 2,0,$c["bg$num"]);
 }
 for($i=0;$i<=($m / 50);$i++){
   ImageLine($img,0,$m-$i*100+50,($days + 1) * $scalex - 1,$m-$i*100+50,$c[mk1]);
   ImageLine($img,0,$m-$i*100,($days + 1) * $scalex - 1,$m-$i*100,$c[mk2]);
   imagestring($img, 3, 3, $m-$i*100+1, ($i * 100) * $scaley, $c[bg]);
   imagestring($img, 3, 3, $m-$i*100+51, ($i * 100 - 50) * $scaley, $c[bg]);
   imagestring($img, 3, 2, $m-$i*100, ($i * 100) * $scaley, $c[mk2]);
   imagestring($img, 3, 2, $m-$i*100+50, ($i * 100 - 50) * $scaley, $c[mk1]);
 }


	$users	= array(
		  1 => array('name' => "Total posts         ", 'color' =>  imagecolorallocate($img, 255, 255, 255)),
		 -1 => array('name' => "30-day average x 100", 'color' =>  0xFF8888)
);

	$z	= count($users);
	$namespace	= 12;

	imagerectangle(      $img, 61, 11, 174 + 6 * 5, 15 + $z * $namespace, $c[bg]);
	imagefilledrectangle($img, 60, 10, 173 + 6 * 5, 14 + $z * $namespace, $c[bg2]);
	imagerectangle(      $img, 60, 10, 173 + 6 * 5, 14 + $z * $namespace, $c[mk2]);

	$z	= 0;

	foreach($users as $uid => $userx) {
		if ($uid > 0) {
			$data	= getdata($uid);
			drawdata($data, $userx['color']);
		}
		imageline($img, 66, $z * $namespace + 19, 76, $z * $namespace + 19, $c[bg]);
		imageline($img, 65, $z * $namespace + 18, 75, $z * $namespace + 18, $userx['color']);
		imagestring($img, 2, 80 + 1, $z * $namespace + 12, $userx['name'], $c[bg]);
		imagestring($img, 2, 80, $z * $namespace + 11, $userx['name'], $userx['color']);
		$z++;
	}

	foreach($xdata as $k => $v) {
		$xdata2[$k - 13563]= ($v / 1);
	}

	if (false) {
		print "<pre>days = $days \n\n\n";
		print_r($data);
		print "\n\n------------------------\n\n";
		print_r($xdata2);
		die();
	}
	drawdata($xdata2, $users[-1]['color']);
 
 Header('Content-type:image/png');
 ImagePNG($img);
 ImageDestroy($img);


 function drawdata($p, $color) {
	 global $days, $scalex, $m, $img;
	 $oldy	= $m;
	 for($i=0;$i<$days;$i++){
		$y		= $m-$p[$i];
		$x		= $i * $scalex;
		if (!$p[$i]) {
			$y	 = $oldy;
		}
		imageline($img, $x, $oldy, $x + $scalex - 1, $y, $color);
		$oldy	= $y;

	 }
 }

  function getdata($u) {
	 global $sql, $dd, $dd2, $scaley, $days, $xdata, $alen;
	 $nn	= $sql -> query("SELECT FROM_UNIXTIME(date,'%Y%m%d') ymd, floor(date/86400) d, count(*) c, SUM(num) m FROM posts  GROUP BY ymd ORDER BY ymd");

	 while ($n = $sql -> fetch($nn)) {
	   $p[$n['d']]=$n['c'];
	   
	   for ($temp = $n['d']; $temp > $n['d'] - $alen; $temp--) {
		$xdata[$n['d']]	+= $p[$temp];
	   }
	   $xdata[$n['d']]	/= $alen;
	 
	 }

	$dat	= $sql -> query(
		"SELECT count( * ) AS cnt, floor( to_days( now( ) ) ) - floor( to_days( from_unixtime( date ) ) ) AS d "
		."FROM posts "
		."GROUP BY d ORDER BY d DESC");
	while ($z = $sql -> fetch($dat)) {
	   $da = $days - $z['d'];
	   $y	+= $z['cnt'];
	   $p[$da] = $y / $scaley;

	 }
	return $p;
  }


?>