<?php

  function mkmath($in) {
    global $sql;
    if($d=$sql->fetchq("SELECT file FROM mcache WHERE hash='".md5($in)."'")) $pstr="0".$d[file];
    else {
      $in=addslashes($in);
      $pstr=`cd math/;./texvc ../mathres/ ../mathres/ "$in" utf-8;cd ..`;
      if(strlen($pstr)<32) $pstr="/invalid";
      $sql->query("INSERT INTO mcache VALUES('".md5($in)."','".substr($pstr,1,32)."')");
    }
    return "<img style=vertical-align:middle; src=mathres/".substr($pstr,1,32).".png>";
  }

?>