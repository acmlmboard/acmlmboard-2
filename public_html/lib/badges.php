<?php
/* This file should contain all functions and setup code for badge.
 * Right now I have a basic Proof of concept function. When we go 'feature complete'
 * we'll have a more defined library.  
 */

function perms_for_badges($userid) {
	global $sql;
	//$res = $sql->prepare("SELECT * FROM x_perm WHERE x_type=? AND x_id=?",
	//$badgepermset=$sql->query("SELECT effect FROM badges RIGHT JOIN user_badges ON badges.id = user_badges.badge_id WHERE user_badges.user_id='$loguser[id]' AND badges.effect != 'NULL'");
	$badgepermset=array();

	$res=$sql->prepare("SELECT effect, effect_variable FROM badges RIGHT JOIN user_badges ON badges.id = user_badges.badge_id WHERE user_badges.user_id='$userid' AND badges.effect != 'NULL'");
	//if(!$sql->numrows($badgepermset) == 0) return true;
	while ($row = mysql_fetch_array($res)) {
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "got perm ".$row['perm_id']."<br>";
		$badgepermset[$c++] = array(
				'effect' => $row['effect'],
				'effect_variable' => $row['effect_variable']		
			);
	}
	return $badgepermset;
}

//**TEMP: This function directly tests the database for if a perm exists and returns if it is or not.
function has_badge_perm($effectid, $userid) {
	global $loguser;/*, $badgepermset;*/
	if (!$userid) $userid = $loguser['id'];
	$badgepermset = perms_for_badges($userid);
	foreach ($badgepermset as $k => $v) {
		//if ($v['id'] == 'no-restrictions') return true;
		if ($effectid == $v['effect']){
			if ($v['effect_variable'] != NULL) return $v['effect_variable'];
			else return true;			
		}

	}
	return false;
}


?>