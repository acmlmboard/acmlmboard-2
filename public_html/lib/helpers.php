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
	return isset($cache[$tid]) ? $cache[$tid] : $cache[$tid] = $sql->query_result("SELECT forum FROM threads WHERE id='$tid'");
}

function getcategorybyforum($fid) {
	global $sql;
	static $cache;
	return isset($cache[$fid]) ? $cache[$fid] : $cache[$fid] = $sql->query_result("SELECT cat FROM forums WHERE id='$fid'");
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

function generate_sprite_hash($userid, $spriteid) {
	global $spritesalt;
	return md5($spritesalt . $userid . $spriteid . $spritesalt);
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
		while($row = $sql->fetch_assoc($s)) {
			$smilies[$i++] = $row;
		}
		$smilies['num'] = count($smilies);
	}
}

/*********************************************************************
 * Syndrome
 *********************************************************************/
function syndrome($num) {
	$a = '>Affected by';
	$syn = '';
	if ($num >= 75)
		$syn = "83F3A3$a 'Reinfors Syndrome'";
	if ($num >= 100)
		$syn = "FFE323$a 'Reinfors Syndrome' +";
	if ($num >= 150)
		$syn = "FF5353$a 'Reinfors Syndrome' ++";
	if ($num >= 200)
		$syn = "CE53CE$a 'Reinfors Syndrome' +++";
	if ($num >= 250)
		$syn = "8E83EE$a 'Reinfors Syndrome' ++++";
	if ($num >= 300)
		$syn = "BBAAFF$a 'Wooster Syndrome'!!";
	if ($num >= 350)
		$syn = "FFB0FF$a 'Wooster Syndrome' +!!";
	if ($num >= 400)
		$syn = "FFB070$a 'Wooster Syndrome' ++!!";
	if ($num >= 450)
		$syn = "C8C0B8$a 'Wooster Syndrome' +++!!";
	if ($num >= 500)
		$syn = "A0A0A0$a 'Wooster Syndrome' ++++!!";
	if ($num >= 500)
		$syn = "A0A0A0$a 'Wooster Syndrome' ++++!!";
	if ($num >= 600)
		$syn = "C762F2$a 'Anya Syndrome'!!!";
	if ($num >= 800)
		$syn = "D06030$a 'Something higher than Anya Syndrome' +++++!!";
	if (!empty($syn))
		$syn = "<i><font color=$syn</font></i>";
	return $syn;
}

/*********************************************************************
 * Image uploads
 *********************************************************************/

function img_upload($fname, $img_targ, $img_x, $img_y, $img_size) {
	$ftypes = array("png", "jpeg", "jpg", "gif");
	$img_data = getimagesize($fname['tmp_name']);
	$err = 0;
	$oerr = "";
	if ($img_data[0] > $img_x) {
		$oerr.="<br>Too wide.";
		$err = 1;
	}
	if ($img_data[1] > $img_y) {
		$oerr.="<br>Too tall.";
		$err = 1;
	}
	if ($fname['size'] > $img_size) {
		$oerr.="<br>Filesize limit of $img_size bytes exceeded.";
		$err = 1;
	}
	if (!in_array(str_replace("image/", "", $img_data['mime']), $ftypes)) {
		$oerr = "Invalid file type.";
		$err = 1;
	}
	if ($err) {
		return $oerr;
	}
	if (move_uploaded_file($fname['tmp_name'], $img_targ)) {
		return "OK!";
	} else {
		return "<br>Error creating file.";
	}
}