<?php
/* META
Theme of the Day
Rotates through every valid theme. Changes theme daily.
*/
$themes=unserialize(file_get_contents("../themes_serial.txt"));
$d=getdate();
$l=count($themes)-1;
$t=floor($d[0]/86400)%$l;
if($themes[$t][0]=="Theme of the Day") $t=35;
if(is_file($themes[$t][1].".css")){ $ext = ".css"; } else { if(is_file($themes[$t][1].".php")){ $ext = ".php"; } else { header("Location: 0.css"); die(); } }
header("Location: ".$themes[$t][1]."$ext");
?>