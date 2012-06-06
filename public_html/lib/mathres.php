<?php

  function mkmath($match) {
    global $sql;
	$in = $match[1];
    if($d=$sql->fetchq("SELECT file FROM mcache WHERE hash='".md5($in)."'")) $pstr="0".$d[file];
    else {
	  $in = escapeshellarg($in);
      $pstr=`cd math/;./texvc ../mathres/ ../mathres/ $in utf-8;cd ..`;
      if(strlen($pstr)<32) $pstr="/invalid";
      $sql->query("INSERT INTO mcache VALUES('".md5($match[1])."','".substr($pstr,1,32)."')");
    }
    return "<img style=vertical-align:middle; src=mathres/".substr($pstr,1,32).".png>";
  }

?>