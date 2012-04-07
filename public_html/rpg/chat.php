<?php
  require 'function.php';

  $t=str_replace(' ','',$_GET[t]);
  $r=$_GET[r];
  if(!is_numeric($r)) $r=0;

  if($t!=''){
    $sql->query("INSERT INTO rpgchat (chan, date, user, text) "
               ."VALUES ($r, ".ctime().", $uid, '".addslashes($_GET[t])."')");
  }

  $out='';
  $lines=$sql->query("SELECT r.id, r.date, u.name, r.text "
                    ."FROM rpgchat r "
                    ."LEFT JOIN users u ON r.user=u.id "
                    ."WHERE r.id>$_GET[d] AND chan=$r "
                    ."ORDER BY r.id DESC LIMIT 40");

  $maxid=0;
  $rows=0;
  while($line=$sql->fetch($lines)){
    if(!$maxid)
      $maxid=$line[id];

    $line[text]=wordwrap($line[text], 34, '°br°', true);
    $rows+=substr_count($line[text], '°br°');

    $line[name]=str_replace(' ',' ',$line[name]);
    $line[text]=str_replace(' ',' ',$line[text]);
    $line[text]=str_replace('°br°','     ',$line[text]);

    $out=cdate('H:i',$line[date]).' '.$line[name].' '.$line[text].' '.$out;
    $rows++;
  }
  print "$rows $maxid $out";
?>