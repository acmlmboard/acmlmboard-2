<?php

function encryptpwd($pass) {
	global $config;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $config['ckey'], $pass, MCRYPT_MODE_ECB, $iv)));
}

function decryptpwd($pass) {
	global $config;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $config['ckey'], base64_decode($pass), MCRYPT_MODE_ECB, $iv));
}

function packlcookie($pass) {
	$a = func_get_args();
	$exstr = implode(",", array_slice($a, 1));
	if (strlen($exstr))
		$exstr = ",$exstr";
	return encryptpwd($_SERVER['REMOTE_ADDR'] . "," . $pass . $exstr);
}

function ipmatch($mask, $ip) {
	$pos = strpos($mask, "*");
	if ($pos === false) {
		$pos = strlen($mask);
		if (strlen($ip) > $pos)
			return false;
	}
	$mask = substr($mask, 0, strpos($mask, "*"));
	if ($mask == substr($ip, 0, $pos))
		return true;
	return false;
}

function unpacklcookie($pass) {
	$p = decryptpwd($pass);
	$pa = explode(",", $p);
	$p1 = explode(".", $pa[0]);
	$p2 = explode(".", $_SERVER['REMOTE_ADDR']);
	if (!strlen($pa[2]) && (!($p1[0] == $p2[0] && $p1[1] == $p2[1]))) {
		// old-style lcookie, no /16 match
		return "";
	} else if (!strlen($pa[2])) {
		return $pa[1];
	}
	$i = 2;
	while (strlen($pa[$i])) {
		if (ipmatch($pa[$i], $_SERVER['REMOTE_ADDR']))
			return $pa[1];
		++$i;
	}
	return "";
}

function packsafenumeric($i) {
	global $loguser;
	return encryptpwd($i . "," . $loguser['id']);
}

function unpacksafenumeric($s, $fallback = -1) {
	global $loguser;
	$a = explode(",", decryptpwd($s));
	if ($a[1] != $loguser[id])
		return $fallback;
	else
		return $a[0];
}

?>