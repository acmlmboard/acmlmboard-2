<?php
function frender($img,$font,$x,$y,$l,$text){
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

//Added Second set of functions for new.php. We should merge the two at a later date -Emuz

function frenderN($img,$font,$x,$y,$l,$text){
  $text.='';
  if(strlen($text)<$l) $x+=($l-strlen($text))*8;
  for($i=0;$i<strlen($text);$i++)
    ImageCopy($img,$font,$i*5+$x,$y,(ord($text[$i])%16)*8,floor(ord($text[$i])/16)*8,6,8);
}
function fontcN($r1,$g1,$b1,$r2,$g2,$b2,$r3,$g3,$b3){
  $font=ImageCreateFromPNG('font2.png');
  ImageColorTransparent($font,1);
  ImageColorSet($font,6,$r1,$g1,$b1);
  ImageColorSet($font,5,($r1*2+$r2)/3,($g1*2+$g2)/3,($b1*2+$b2)/3);
  ImageColorSet($font,4,($r1+$r2*2)/3,($g1+$g2*2)/3,($b1+$b2*2)/3);
  ImageColorSet($font,3,$r2,$g2,$b2);
  ImageColorSet($font,0,$r3,$g3,$b3);
  return $font;
}
?>
