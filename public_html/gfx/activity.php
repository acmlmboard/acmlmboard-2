<?php
 require 'gfxlib.php';

 $u = checknumeric($_GET['u']);
 $user=$sql->fetch($sql->query("SELECT regdate FROM users WHERE id=$u"));
 if(!$user[regdate]) die();

 $vd=date('m-d-y', $user[regdate]);
 $dd=mktime(0,0,0,substr($vd,0,2),substr($vd,3,2),substr($vd,6,2));
 $dd2=mktime(0,0,0,substr($vd,0,2),substr($vd,3,2)+1,substr($vd,6,2));

 $nn=$sql->query("SELECT FROM_UNIXTIME(date,'%Y%m%d') ymd, floor(date/86400) d, count(*) c, max(num) m FROM posts WHERE user=$u GROUP BY ymd ORDER BY ymd");

 while($n=$sql->fetch($nn)){
   $p[$n[$d]]=$n[c];
   $t[$n[$d]]=$n[m];
 }

 for($i=0;$dd+$i*86400<ctime();$i++){
   $ps=$sql->query("SELECT count(*),max(num) FROM posts WHERE user=$u AND date>=$dd+$i*86400 AND date<$dd2+$i*86400");
   $p[$i]=$sql->result($ps,0,0);
   $t[$i]=$sql->result($ps,0,1);
 }

 $days=floor((ctime()-$dd)/86400);
 $m=max($p);

 Header('Content-type:image/png');
 $img=ImageCreate($days,$m);

 $c[bg]= ImageColorAllocate($img,  0,  0,  0);
 $c[bg1]=ImageColorAllocate($img,  0,  0, 80);
 $c[bg2]=ImageColorAllocate($img,  0,  0,130);
 $c[bg3]=ImageColorAllocate($img, 80, 80,250);
 $c[mk1]=ImageColorAllocate($img,110,110,160);
 $c[mk2]=ImageColorAllocate($img, 70, 70,130);
 $c[bar]=ImageColorAllocate($img,250,190, 40);
 $c[pt] =ImageColorAllocate($img,250,250,250);
 for($i=0;$i<$days;$i++){
   $num=date('m',$dd+$i*86400)%2+1;
   if(date('m-d',$dd+$i*86400)=='01-01') $num=3;
   ImageLine($img,$i,$m,$i,0,$c["bg$num"]);
 }
 for($i=0;$i<=5;$i++){
   ImageLine($img,0,$m-$i*100+50,$days,$m-$i*100+50,$c[mk2]);
   ImageLine($img,0,$m-$i*100,$days,$m-$i*100,$c[mk1]);
 }
 for($i=0;$i<$days;$i++){
   ImageLine($img,$i,$m,$i,$m-$p[$i],$c[bar]);
   ImageSetPixel($img,$i,$m-$t[$i]/($i+1),$c[pt]);
 }

 ImagePNG($img);
 ImageDestroy($img);
?>