<?php
/* META
Theme of the Day (Acmlmboard 1.x mix)
Rotates through a specified set of classic themes
*/
$themes=array("abI","abIdailycycle","arhd","bloodlust","classic","dani","dig","endofff","ff9a","kafuka","mario","megaman","nes","night","oldblue","purple");
$d=getdate();
$l=count($themes);
$t=floor($d[0]/86400)%$l;
if(is_file($themes[$t].".css")){ $ext = ".css"; } else { if(is_file($themes[$t].".php")){ $ext = ".php"; } else { header("Location: 0.css"); die(); } }
header("Location: ".$themes[$t]."$ext");
?>