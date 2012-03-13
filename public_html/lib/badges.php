<?php
/* This file should contain all functions and setup code for badge.
 * Right now I have a basic Proof of concept function. When we go 'feature complete'
 * we'll have a more defined library. */

//**TEMP: This function directly tests the database for if a perm exists and returns if it is or not.
function has_badge_perm($effectid) {
	global $loguser, $sql;
	//return false; //Debug disable
	$badgepermset=$sql->query("SELECT effect FROM badges RIGHT JOIN user_badges ON badges.id = user_badges.badge_id WHERE user_badges.user_id='$loguser[id]' AND badges.effect != 'NULL'");
	if(!$sql->numrows($badgepermset) == 0) return true;
	/*foreach ($badgepermset as $k => $v) {
		//if ($v['id'] == 'no-restrictions') return true;
		if ($effectid == $v['effect']) return true;
	}*/
	return false;
}


?>