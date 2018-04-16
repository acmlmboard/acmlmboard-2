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
  
// Convenience functions for converting fields to timestamps and vice versa
const DTF_DATE    = 0b1;
const DTF_TIME    = 0b10;
const DTF_NOLABEL = 0b100;
function datetofields($timestamp, $basename, $flags = DTF_DATE | DTF_NOLABEL){
	global $L;
	if ($timestamp) $val = explode("|", date("n|j|Y|H|i|s", $timestamp));
	else            $val = array_fill(0, 6, "");
	
	if ($flags & DTF_NOLABEL) $fname = array('', '-', '-', ' &nbsp; ', ':', ':');
	else                       $fname = array('Month: ', ' Day: ', ' Year: ', ' Hours: ', ' Minutes: ', ' Seconds: ');
	
	$fields = "";
	if ($flags & DTF_DATE) {
		$fields .= 
		"$fname[0]$L[INPt]='{$basename}month' type='text' maxlength='2' size='2' style='text-align: right' value='$val[0]'>".
		"$fname[1]$L[INPt]='{$basename}day'   type='text' maxlength='2' size='2' style='text-align: right' value='$val[1]'>".
		"$fname[2]$L[INPt]='{$basename}year'  type='text' maxlength='4' size='4' style='text-align: right' value='$val[2]'>";
	}
	if ($flags & DTF_TIME) {
		$fields .= 
		"$fname[3]$L[INPt]='{$basename}hour'  type='text' maxlength='2' size='2' style='text-align: right' value='$val[3]'>".
		"$fname[4]$L[INPt]='{$basename}min'   type='text' maxlength='2' size='2' style='text-align: right' value='$val[4]'>".
		"$fname[5]$L[INPt]='{$basename}sec'   type='text' maxlength='4' size='4' style='text-align: right' value='$val[5]'>";
	}
	return $fields;
}

function fieldstotimestamp($basename, $arrayname = 'GLOBALS'){
	global ${$arrayname}; // Workaround to allow accessing superglobals with 'variable variables'
	
	// Follow the mktime argument order
	$fnames = array('hour', 'min', 'sec', 'month', 'day', 'year');
	
	// Populate the array with the datetofields results (ie: $_POST['testyear'],... with $arrayname = '_POST' and $basename = 'test';
	// The values may or may not exist, so we pass them by reference
	$v = array();
	for ($i = 0; $i < 6; ++$i) {
		$v[$i] = &${$arrayname}[$basename.$fnames[$i]];
		$v[$i] = (int) $v[$i];
	}
	// Don't allow zero timestamps
	if (!$v[0] && !$v[1] && !$v[2] && !$v[3] && !$v[4] && !$v[5]) return NULL;
	
	// Is the date valid? (if one of the values is set, the other two have to be set as well)
	if ($v[3] || $v[4] || $v[5])
		if (!checkdate($v[3], $v[4], $v[5]))
			return NULL;
	
	// Is the time in a valid range?
	if ($v[0] < 0 || $v[0] > 23) $v[0] = 0;
	if ($v[1] < 0 || $v[1] > 59) $v[1] = 0;
	if ($v[2] < 0 || $v[2] > 59) $v[2] = 0;
	
	$res = mktime($v[0],$v[1],$v[2],$v[3],$v[4],$v[5]);
	return ($res !== FALSE ? $res : NULL); // Return NULL so it can directly go in a prepared query
}

?>