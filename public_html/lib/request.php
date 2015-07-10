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