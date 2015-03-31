<?php

  function usectime(){
    $t=gettimeofday();
    return $t['sec']+$t['usec']/1000000;
  }
  $start=usectime();

  function ctime(){
    return time();
  }

  function timeunits($sec){
    if($sec<    60) return "$sec sec.";
    if($sec<  3600) return floor($sec/60).' min.';
    if($sec< 86400) return floor($sec/3600).' hour'.($sec>=7200?'s':'');
    return floor($sec/86400).' day'.($sec>=172800?'s':'');
  }

  function timeunits2($sec){
    $d=floor($sec/86400);
    $h=floor($sec/3600)%24;
    $m=floor($sec/60)%60;
    $s=$sec%60;
    $ds=($d>1?'s':'');
    $hs=($h>1?'s':'');
    $str=($d?"$d day$ds ":'').($h?"$h hour$hs ":'').($m?"$m min. ":'').($s?"$s sec.":'');
    if(substr($str,-1)==' ') $str=substr_replace($str,'',-1);
    return $str;
  }

  function cdate($format,$date){
    global $loguser;
    return date($format,$date); //+$loguser[tzoff]);
  }

?>