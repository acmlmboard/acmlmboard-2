<?php
	//ipbans.php - core for new IP ban functions, started 2007-02-19 // blackhole89

	//delete expired IP bans
	$sql->query('DELETE FROM ipbans WHERE expires < '.ctime().' AND expires > 0');
	
	// Prioritize hard bans over soft ones
	$ban = $sql->fetchq("SELECT * FROM ipbans WHERE '{$_SERVER['REMOTE_ADDR']}' LIKE ipmask ORDER BY hard DESC");
	if ($ban) {
		// report the IP as banned like before
		if ($loguser['id']) {
			$sql->query("UPDATE `users` SET `ipbanned` = '1' WHERE `id` = '{$loguser['id']}'");
		} else {
			$sql->query("UPDATE `guests` SET `ipbanned` = '1' WHERE `ip` = '{$_SERVER['REMOTE_ADDR']}'");
		}
		
		// a ban appears to be present. check for type
		// and restrict user's access if necessary
		if ($ban['hard']) {
			//	  header("Location: http://banned.ytmnd.com/");
			//	  header("Location: http://board.acmlm.org/");
			//    fuck this shit
			die("Sorry, but your IP address appears to be banned from this board.");
		}

		// "soft" IP ban allows non-banned users with existing accounts to log on
		$bannedgroups = $sql->getresults("SELECT id FROM `group` WHERE `banned` = 1");
		if (!$loguser['id'] || in_array($loguser['group_id'], $bannedgroups)) { // NOTE: The last check originally didn't work and always failed
			if (strpos($_SERVER['PHP_SELF'], "login.php") === false) {
				error("IP restricted", "Access from your IP address to this board appears to be limited.<br><a href='login.php'>Login</a>");
			}
		}
	}