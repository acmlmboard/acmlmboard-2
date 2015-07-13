<?php

/* This file should contain all functions and setup code for badge.
 * Right now I have a basic Proof of concept function. When we go 'feature complete'
 * we'll have a more defined library.  
 */

function perms_for_badges($userid) {
	global $sql;
	$badgepermset = array();

	$res = $sql->prepare("SELECT effect, effect_variable FROM badges RIGHT JOIN user_badges ON badges.id = user_badges.badge_id WHERE user_badges.user_id='$userid' AND badges.effect != 'NULL'");

	while ($row = $sql->fetch($res)) {
		$badgepermset[$c++] = array(
			'effect' => $row['effect'],
			'effect_variable' => $row['effect_variable']
		);
	}
	return $badgepermset;
}

//**TEMP: This function directly tests the database for if a perm exists and returns if it is or not.
function has_badge_perm($effectid, $userid = 0) {
	global $loguser, $config;
	if (!$config['badgesystem'])
		return false; //Break out of the function if disabled in the config

	if (!$userid)
		$userid = $loguser['id'];
	$badgepermset = perms_for_badges($userid);
	foreach ($badgepermset as $k => $v) {
		if ($effectid == $v['effect']) {
			if ($v['effect_variable'] != NULL)
				return $v['effect_variable'];
			else
				return true;
		}
	}
	return false;
}

?>