<?php

/* Returns associative array of specified request values, unslashed, regardless
 * of PHP.ini setting
 */

function request_variables($varlist) {
	$quoted = get_magic_quotes_gpc();
	$out = array();
	foreach ($varlist as $key) {
		if (isset($_REQUEST[$key])) {
			$out[$key] = ($quoted) ?
					(stripslashes($_REQUEST[$key])) : ($_REQUEST[$key]);
		}
	}
	return $out;
}

/* Legacy Support */
if (!get_magic_quotes_gpc()) {
	if (is_array($GLOBALS))
		while (list($key, $val) = each($GLOBALS))
			if (is_string($val))
				$GLOBALS[$key] = addslashes($val);
	if (is_array($_POST))
		while (list($key, $val) = each($_POST))
			if (is_string($val))
				$_POST[$key] = addslashes($val);
}


// This function should be used on $_POST input, before either a) using it in a prepared query or b) escaping it with $sql->escape() and using it in a raw query
// It should be removed once the reliance on magic_quotes (and the hack above) have been removed.
function autodeslash($v) {
	return stripslashes($v);
}

function isssl() {
	return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
}

/* function redirect($url,$msg,$delay=2){
  return "You will now be redirected to <a href=$url>$msg</a> ...<META HTTP-EQUIV=REFRESH CONTENT=$delay;URL=$url>";
  } */

function redirect($url, $msg) {
	header("Set-Cookie: pstbon=" . $msg . "; Max-Age=60; Version=1");
	header("Location: " . $url);
	die();
	return 0;
}

/* End Legacy Support */
?>