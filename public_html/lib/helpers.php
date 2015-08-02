<?php

function checknumeric(&$var) {
	if (!is_numeric($var)) {
		$var = 0;
		return false;
	}
	return true;
}

function getforumbythread($tid) {
	global $sql;
	static $cache;
	return isset($cache[$tid]) ? $cache[$tid] : $cache[$tid] = $sql->resultq("SELECT forum FROM threads WHERE id='$tid'");
}

function getcategorybyforum($fid) {
	global $sql;
	static $cache;
	return isset($cache[$fid]) ? $cache[$fid] : $cache[$fid] = $sql->resultq("SELECT cat FROM forums WHERE id='$fid'");
}

function getcategorybythread($tid) {
	return getcategorybyforum(getforumbythread($tid));
}

function generate_random_string($length) {
	$output = '';
	$character_list = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$+/~';
	for ($i = 0; $i < $length; ++$i) {
		$output .= $character_list[mt_rand(0, strlen($character_list) - 1)];
	}
	return $output;
}

function request_variables($varlist) {
	$magic_quotes_enabled = get_magic_quotes_gpc();
	$out = array();
	foreach ($varlist as $key) {
		if (isset($_REQUEST[$key])) {
			$out[$key] = $magic_quotes_enabled ? stripslashes($_REQUEST[$key]) : $_REQUEST[$key];
		}
	}
	return $out;
}

function is_ssl() {
	return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
}

function redirect($url, $msg) {
	header("Set-Cookie: pstbon=$msg; Max-Age=60; Version=1");
	header("Location: $url");
	die();
	return 0;
}

/*********************************************************************
 * Time functions
 *********************************************************************/
function usectime() {
	$t = gettimeofday();
	return $t['sec'] + $t['usec'] / 1000000;
}

function ctime() {
	return time();
}

function timeunits($sec) {
	if ($sec < 60)
		return "$sec sec.";
	if ($sec < 3600)
		return floor($sec / 60) . ' min.';
	if ($sec < 86400)
		return floor($sec / 3600) . ' hour' . ($sec >= 7200 ? 's' : '');
	return floor($sec / 86400) . ' day' . ($sec >= 172800 ? 's' : '');
}

function timeunits2($sec) {
	$d = floor($sec / 86400);
	$h = floor($sec / 3600) % 24;
	$m = floor($sec / 60) % 60;
	$s = $sec % 60;
	$ds = ($d > 1 ? 's' : '');
	$hs = ($h > 1 ? 's' : '');
	$str = ($d ? "$d day$ds " : '') . ($h ? "$h hour$hs " : '') . ($m ? "$m min. " : '') . ($s ? "$s sec." : '');
	if (substr($str, -1) == ' ')
		$str = substr_replace($str, '', -1);
	return $str;
}

function cdate($format, $date) {
	global $loguser;
	return date($format, $date); //+$loguser[tzoff]);
}

/*********************************************************************
 * Smilies
 *********************************************************************/
function loadsmilies() {
	global $sql,$smilies;
	$i = 0;
	if($s = $sql->query("SELECT * FROM smilies")) {
		while($row = $sql->fetch($s)) {
			$smilies[$i++] = $row;
		}
		$smilies['num'] = count($smilies);
	}
}

?>