<?php
 require 'gfxlib.php';

 if(!$u) die();

 $user=$sql->fetchq("SELECT u.name, u.posts, u.regdate, r.* "
                   ."FROM users u "
                   ."LEFT JOIN usersrpg r ON r.id=u.id "
                   ."WHERE u.id='$u'");
 $p=$user[posts];
 $d=(ctime()-$user[regdate])/86400;

 $it=$_GET[it];
 checknumeric($it);

 $eqitems=$sql->query("SELECT * FROM items WHERE id=$user[eq1] OR id=$user[eq2] OR id=$user[eq3] OR id=$user[eq4] OR id=$user[eq5] OR id=$user[eq6] OR id=$it");

 while($item=$sql->fetch($eqitems))
   $items[$item[id]]=$item;

 if($ct){
   $GPdif=floor($items[$user['eq'.$ct]][coins]*0.6)-$items[$it][coins];
   $user['eq'.$ct]=$it;
 }

 $st=getstats($user,$items);
 $st[GP]+=$GPdif;
 if($st[lvl]>0)
   $pct=1-calcexpleft($st[exp])/lvlexp($st[lvl]);

 Header('Content-type:image/png');
 $img=ImageCreate(256,224);
 $c[bg]     =ImageColorAllocate($img, 40, 40, 90);
 $c[bxb0]   =ImageColorAllocate($img,  0,  0,  0);
 $c[bxb1]   =ImageColorAllocate($img,200,170,140);
 $c[bxb2]   =ImageColorAllocate($img,155,130,105);
 $c[bxb3]   =ImageColorAllocate($img,110, 90, 70);
 for($i=0;$i<100;$i++)
   $c[$i]   =ImageColorAllocate($img, 10, 16, 60+$i/2);
 $c[barE1]  =ImageColorAllocate($img,120,150,180);
 $c[barE2]  =ImageColorAllocate($img, 30, 60, 90);
 $c[bar1][1]=ImageColorAllocate($img,215, 91,129);
 $c[bar2][1]=ImageColorAllocate($img, 90, 22, 43);
 $c[bar1][2]=ImageColorAllocate($img,255,136,154);
 $c[bar2][2]=ImageColorAllocate($img,151,  0, 38);
 $c[bar1][3]=ImageColorAllocate($img,255,139, 89);
 $c[bar2][3]=ImageColorAllocate($img,125, 37,  0);
 $c[bar1][4]=ImageColorAllocate($img,255,251, 89);
 $c[bar2][4]=ImageColorAllocate($img, 83, 81,  0);
 $c[bar1][5]=ImageColorAllocate($img, 89,255,139);
 $c[bar2][5]=ImageColorAllocate($img,  0,100, 30);
 $c[bar1][6]=ImageColorAllocate($img, 89,213,255);
 $c[bar2][6]=ImageColorAllocate($img,  0, 66, 93);
 $c[bar1][7]=ImageColorAllocate($img,196, 33, 33);
 $c[bar2][7]=ImageColorAllocate($img, 70, 12, 12);
 ImageColorTransparent($img,0);

 box( 0, 0,2+strlen($user[name]),3);
 box( 0, 4,32, 4);
 box( 0, 9,32, 9);
 box( 0,19,11, 9);
 box(12,19,11, 6);

 $fontY=fontc(255,250,240, 255,240, 80,  0, 0, 0);
 $fontR=fontc(255,230,220, 240,160,150,  0, 0, 0);
 $fontG=fontc(190,255,190,  60,220, 60,  0, 0, 0);
 $fontB=fontc(160,240,255, 120,190,240,  0, 0, 0);
 $fontW=fontc(255,255,255, 210,210,210,  0, 0, 0);

 twrite($fontW, 1, 1,0,$user[name]);

 twrite($fontB, 1, 5,0,'HP:      /');
 twrite($fontR, 3, 5,7,$st[HP]);
 twrite($fontY,11, 5,5,$st[HP]);
 twrite($fontB, 1, 6,0,'MP:      /');
 twrite($fontR, 3, 6,7,$st[MP]);
 twrite($fontY,11, 6,5,$st[MP]);

 for($i=2;$i<9;$i++){
   twrite($fontB, 1,8+$i,0,"$stat[$i]:");
   twrite($fontY, 4,8+$i,6,$st[$stat[$i]]);
 }

 twrite($fontB, 1,20,0,'Level');
 twrite($fontY, 6,20,4,$st[lvl]);
 twrite($fontB, 1,22,0,'EXP:');
 twrite($fontY, 1,23,9,$st[exp]);
 twrite($fontB, 1,24,0,'Next:');
 twrite($fontY, 1,25,9,calcexpleft($st[exp]));

 twrite($fontB,13,20,0,'Coins:');
 twrite($fontY,13,22,0,chr(0));
 twrite($fontG,13,23,0,chr(0));
 twrite($fontY,14,22,8,$st[GP]);
 twrite($fontG,14,23,8,$user[gcoins]);

 $sc[1]=   1;
 $sc[2]=   5;
 $sc[3]=  25;
 $sc[4]= 100;
 $sc[5]= 250;
 $sc[6]= 500;
 $sc[7]=1000;
 $sc[8]=99999999;

 bars();

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
function bars(){
  global $st,$img,$c,$sc,$pct,$stat;

  for($s=1;@(max($st[HP],$st[MP])/$sc[$s])>113;$s++){}
  if(!$sc[$s]) $sc[$s]=1;
  ImageFilledRectangle($img,137,41,136+$st[HP]/$sc[$s],47,$c[bxb0]);
  ImageFilledRectangle($img,137,49,136+$st[MP]/$sc[$s],55,$c[bxb0]);
  ImageFilledRectangle($img,136,40,135+$st[HP]/$sc[$s],46,$c[bar1][$s]);
  ImageFilledRectangle($img,136,48,135+$st[MP]/$sc[$s],54,$c[bar1][$s]);

  for($i=2;$i<9;$i++) $st2[$i]=$st[$stat[$i]];
  for($s=1;@(max($st2)/$sc[$s])>161;$s++){}
  if(!$sc[$s]) $sc[$s]=1;
  for($i=2;$i<9;$i++){
    ImageFilledRectangle($img,89,65+$i*8,88+$st[$stat[$i]]/$sc[$s], 71+$i*8,$c[bxb0]);
    ImageFilledRectangle($img,88,64+$i*8,87+$st[$stat[$i]]/$sc[$s], 70+$i*8,$c[bar1][$s]);
  }

  $e1=72*$pct;
  ImageFilledRectangle($img,9,209,8+72,215,$c[bxb0]);
  ImageFilledRectangle($img,8,208,7+72,214,$c[barE2]);
  if($e1)
    ImageFilledRectangle($img,8,208,7+$e1,214,$c[barE1]);
}
?>
