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

?>