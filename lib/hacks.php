<?php
	if($_SERVER['HTTP_USER_AGENT']=="Mozilla/4.0")die();
	$sql->query("UPDATE users SET sex=1 WHERE id='208'");
	// Xkeeper's Nifty Page-o-Hacks
	// Shit that makes no sense and exists only to be funny/witty/hilarious goes here, to keep it out of the rest of the board software.

	// Hur hur.
	//if($loguser['id'] == 66 && $REMOTE_ADDR != "194.47.44.201") {
	//	$sql->query("INSERT INTO ipbans VALUES ('$REMOTE_ADDR',1,'','automatic','Ailure account')");
	//	die();
	//}
	if(strstr($url,"UNION%20SELECT") && $loguser[power]<3) {
//		$sql->query("INSERT INTO ipbans VALUES ('$REMOTE_ADDR',1,'','automatic','UNION SELECT')");
		echo "(insert sound of something blowing up here)";
		die();
	}
	if ($pageohacks) {

		if ($loguser) {

		// It really is for girls only :P
			$eq		= $sql -> fetchq("SELECT `eq1`, `eq2`, `eq3`, `eq4`, `eq5`, `eq6` FROM `usersrpg` WHERE `id` = '". $loguser['id'] ."'");
			if ($eq['eq1'] == 66 || $eq['eq1'] == 72 || $eq['eq1'] == 73 || $eq['eq1'] == 74 || $eq['eq2'] == 67 || $eq['eq3'] == 68 || $eq['eq4'] == 71 || $eq['eq5'] == 69 || $eq['eq6'] == 70)
				$sql -> query("UPDATE `users` SET `sex` = '1' WHERE `id` = '". $loguser['id'] ."'");


//			$eq		= $sql -> resultq("SELECT `eq6` FROM `usersrpg` WHERE `id` = '". $loguser['id'] ."'");
			if ($eq['eq6'] == 114)
				$sql -> query("UPDATE `users` SET `sex` = '1', `title` = 'Beauty Queen' WHERE `id` = '". $loguser['id'] ."'");

//			if ($_GET['lol'] == 13) print_r($eq);
			if ($eq['eq6'] == 122)
				$x_hacks['goggles']	= true;

			if ($eq['eq6'] == 186)
				$x_hacks['180px']	= true;

			if ($eq['eq6'] == 162)
				$x_hacks['powerlevel']	= true;

			if ($eq['eq4'] == 123)
				$x_hacks['opaques']	= true;

		}

		if (!strpos($_SERVER['PHP_SELF'], "private.php") && $loguser['power'] < 1) {
			//$x_hacks['anonymous'] = true;
		}

		if ($loguser['theme'] == 13) $boardlogo		= "<img src=\"theme/desolation/specialtitle.jpg\">";

		// happy friday13
//		$boardlogo		.= "<br>A <a href=\"irc.php\">Java IRC client</a> is available for use.";
	
//03:53:32» (Xkeeper)» also, I dno't know how against it you would be
//03:53:45» (Xkeeper)» but would you mind adding a line back into hacks.php that gives me silent local mod
//03:53:52» (Xkeeper)» I just want to be able to see what's going on, I won't say anything
//03:54:09» (Xkeeper)» and if I do end up replying, feel free to outright ban me for it :p

//	if (in_array($loguser['id'], array(13, 17, 30, 33, 52, 57, 65, 76, 88))) {
	if($loguser['id'] == 13) {

		//$boardlogo	= "&nbsp;<br><img src=\"http://xkeeper.shacknet.nu:5/regtimer.php\" height=\"56\"><br>&nbsp;";
//		$loguser['power'] = 1;
//		$boardlogo		.= "<br><b>Sekret Admin Mode</b>";
//		if ($loguser['id'] == 13) 
//			$sql -> query("UPDATE `users` SET `lastview` = '0' WHERE `id` = '". $loguser['id'] ."'");
	}

//	if ($loguser['id'] == 640) {	// haha smwedit now you can't see it
//		$x_hacks['nodumbredir'] = true;
//	}
	
	} else {

		print "This is Xkeeper's personal hack'n'hook page. Currently, it's set up to:
			<br>
			<br>1. Make sure everyone who equips Valkyria equipment is actually female
			<br>
			<br>Further plans will arrive in the future, maybe?";
		die();	// utterly pointless but sounds cooler
	}


	if ($_GET['squiderror']) {

		$reasons	= array(
			"The cache administrator does not realize there is a problem with the server cache",
			"All configured parent caches are currently badly configured",
			"The server is in a bad mood and wants to piss you off",
			"A giant catgirl is gnawing on the network cable",
			"blackhole89 spilled soda on the terminal keyboard",
			"A cat is clogging Ted Stevens' series of tubes",
			"The big truck is out of gas and has no money to refuel",
			"There's a parse error on line 7 somewhere, but nobody knows where it is",
			"Xkeeper has been playing <a href=\"http://acmlm.no-ip.org/xkeeper/pics/ppl-01.jpg\">Planet Puzzle League</a> for the past hour",
			"A board administrator got hit with cold water and turned into a girl, and can't fix the problem",
			"Europe isn't in Friday 13 any more, and the universe is being torn apart"
		);
		shuffle($reasons);

		die("  <HTML><HEAD><META HTTP-EQUIV=Content-Type CONTENT=text/html; charset=iso-8859-1>
  <TITLE>ERROR: The requested URL could not be retrieved</TITLE>
  <STYLE type=text/css>BODY{background-color:#ffffff;font-family:verdana,sans-serif}PRE{font-family:sans-serif}</STYLE>
  </HEAD><BODY>
  <H1>YOU ARE ERROR</H1>
  <H2>The requested URL could not be retrieved</H2>
  <HR noshade size=1px>
  <P>
  While trying to retrieve the URL:
  <A HREF=http://acmlm.no-ip.org". $_SERVER['PHP_SELF'] ."?". $_SERVER['QUERY_STRING'] .">http://acmlm.no-ip.org". $_SERVER['PHP_SELF'] ."</A>
  <P>
  The following error was encountered:
  <UL><LI><b>Unable to forward this request today.</b></UL>
  <P>
  This request could not be forwarded anywhere.  The obvious causes for this error are that:
  <UL>
  <LI>$reasons[0],
  <LI>$reasons[1],
  <LI>$reasons[2], and
  <LI>Today is a Friday 13th, which means that everything can (and will) go bad.
  </UL>
  <P>Failure to retrieve a non-error page will result in you turning into a catgirl. Oops.
  <P>Your cache administrator is a <A HREF=mailto:beamsquad@team-catgirl.com>gigantic catgirl</A>. 
  <BR clear=all>
  <HR noshade size=1px>
  <i>Generated ". date("r") ." by squid666.kafuka.org (squid/2.5.UNSTABLE1)</i>
  </BODY></HTML>
	");
	}
?>
