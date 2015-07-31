<?php

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

/* End Legacy Support */
?>