<?php

require "lib/common.php";

needs_login(1);

$targetuserid = $loguser['id'];
$act = isset($_POST['action']) ? $_POST['action'] : '';

if (isset($_GET['id'])) {
	$temp = $_GET['id'];
	if (checknumeric($temp))
		$targetuserid = $temp;
	if ($config['rootuseremail'])
		$user = $sql->fetchq("SELECT * FROM `users` WHERE `id`='$targetuserid'");
}

if (!can_edit_user($targetuserid)) {
	$targetuserid = 0;
}

if ($targetuserid == 0) {
	if ($config['rootuseremail']) {
		if ((has_perm('edit-users') || has_perm('update-user-profile') || has_perm('update-profiles')) && $user['email'] != "") {
			$email = "<br>" . userlink($user) . "'s email: " . $user[email] . "<br>";
		} else {
			$email = "";
		}
		error("Error", "You have no permissions to do this!<br> " . $email . "<a href=./>Back to main</a>");
	} else {
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
	}
}

$blockroot = " AND `default` >= 0 ";

if (has_perm('no-restrictions')) {
	$blockroot = "";
}

$allgroups = $sql->query("SELECT * FROM `group` WHERE `primary`=1 $blockroot ORDER BY sortorder ASC");

$listgroup = array();

while ($group = $sql->fetch($allgroups)) {
	$listgroup[$group['id']] = $group['title'];
}

$token = md5($pwdsalt2 . $loguser['pass'] . $pwdsalt);
if ($act == 'Edit profile') {
	if ($_POST['token'] !== $token)
		die('No.');

	if ($_POST['pass'] != '' && $_POST['pass'] == $_POST['pass2'] && $targetuserid == $loguser['id'])
		setcookie('pass', packlcookie(md5($pwdsalt2 . $_POST['pass'] . $pwdsalt)), 2147483647);
}


global $user, $userrpg;

$user = $sql->fetchq("SELECT * FROM users WHERE `id` = $targetuserid");

if (!$user) {
	print "<table cellspacing=\"0\" class=\"c1\">
             <td class=\"b n1\" align=\"center\">
               This user doesn't exist!<br>
               <a href=./>Back to main</a>
           </table>";
	die(pagefooter());
}

$userrpg = getstats($userrpgdata = $sql->fetchq('SELECT u.name, u.posts, u.regdate, r.* '
		. 'FROM users u '
		. 'LEFT JOIN usersrpg r ON u.id=r.id '
		. "WHERE u.id=$user[id]"));


if ($act == 'Edit profile') {
	$error = '';

	if ($_POST['pass'] && $_POST['pass2'] && $_POST['pass'] != $_POST['pass2'])
		$error = "- The passwords you entered don't match.<br />";

	$minipic = 'minipic';
	if ($fname = $_FILES['minipic']['name']) {
		$fext = strtolower(substr($fname, -4));
		if ($fext != ".png" && $fext != ".gif") {
			$error.="- Invalid minipic file type; must be PNG or GIF.<br />";
		}
		if ($_FILES['minipic']['size'] > 10240) {
			$error.="- Minipic file size is too high; must be 10KB or less.<br />";
		}
		if (!$error) {
			$tmpfile = $_FILES['minipic']['tmp_name'];
			list($width, $height, $type) = getimagesize($tmpfile);
			if ($width != $minipicsize || $height != $minipicsize) {
				$error.="- Minipic size must be {$minipicsize}x$minipicsize.<br />";
			} else if ($type != 3 && $type != 1) {
				$error.="- Minipic file broken or not a valid PNG or GIF image!<br />";
			} else {
				if ($type == 1)
					$type = "gif";
				else
					$type = "png";
				$minipic = "\"data:image/$type;base64," . base64_encode(file_get_contents($tmpfile)) . "\"";
			}
		}
	}
	if ($_POST['minipicdel'])
		$minipic = "\"\"";
	$usepic = 'usepic';
	$fname = $_FILES['picture'];
	if ($fname['size'] > 0) {
		$ava_out = img_upload($fname, "userpic/$user[id]", $avatardimx, $avatardimy, $avatarsize);
		if ($ava_out != "OK!") {
			$error.=$ava_out;
		} else
			$usepic = "usepic+1";
	}
	if ($_POST['picturedel'])
		$usepic = 0;

	//check for table breach
	if (tvalidate($_POST['head'] . $_POST['sign']) != 0) {
		$error.="- Table tag count mismatch in post layout.<br />";
	}
	if (tvalidate($_POST['title']) != 0) {
		$error.="- Table tag count mismatch in custom title.<br />";
	}

	if ($_POST['fontsize'] < 30)
		$_POST['fontsize'] = 30;
	if ($_POST['fontsize'] > 999)
		$_POST['fontsize'] = 999;
	if ($_POST['sex'] < 0)
		$_POST['sex'] = 0;
	if ($_POST['sex'] > 2)
		$_POST['sex'] = 2;

	$pass = $_POST['pass'];
	if (!strlen($_POST['pass2']))
		$pass = "";
	$tztotal = $_POST['tzoffH'] * 3600 + $_POST['tzoffM'] * 60 * ($_POST['tzoffH'] < 0 ? -1 : 1);
	//Validate birthday values.
	if (!$_POST['birthM'] || !$_POST['birthD']) //Reject if any are missing.
		$birthday = -1;
	else {
		if (!is_numeric($_POST['birthM']) || !is_numeric($_POST['birthD'])) //Reject if not numeric.
			$birthday = -1;
	}
	if ($_POST['birthM'] > 12 || $_POST['birthD'] > 31) // fixes a small bug where if the fields are above a certain value, the profile fails to load
		$birthday = -1;
	$year = $_POST['birthY'];
	if (!$_POST['birthY'] || !is_numeric($_POST['birthY']))
		$year = -1;
	if ($birthday != -1 && $_POST['birthM'] != "" && $_POST['birthD'] != "")
		$birthday = str_pad($_POST['birthM'], 2, "0", STR_PAD_LEFT) . '-' . str_pad($_POST['birthD'], 2, "0", STR_PAD_LEFT) . '-' . $year;
	else
		$birthday = -1;

	$dateformat = ($_POST['presetdate'] ? $_POST['presetdate'] : $_POST['dateformat']);
	$timeformat = ($_POST['presettime'] ? $_POST['presettime'] : $_POST['timeformat']);

	if (has_perm("edit-users")) {

		//Update admin bells and whistles
		$targetgroup = $_POST['group_id'];
		checknumeric($targetgroup);
		if (!isset($listgroup[$targetgroup]))
			$targetgroup = 0;

		if (!can_edit_group_assets($targetgroup) && $targetgroup != $loguser['group_id']) {
			$error.="- You do not have the permissions to assign this group.<br />";
		}
		$targetname = $_POST['name'];

		if ($sql->resultq("SELECT COUNT(`name`) FROM `users` WHERE (`name` = '$targetname' OR `displayname` = '$targetname') AND `id` != $user[id]")) {
			$error.="- Name already in use.<br />";
		}
	}
	if (checkcdisplayname($targetuserid)) {
		//Checks Displayname to name and other displaynames
		$targetdname = $_POST['displayname'];

		if (checkcdisplayname($targetuserid) && $targetdname != "") {
			if ($sql->resultq("SELECT COUNT(`name`) FROM `users` WHERE (`name` = '$targetdname' OR `displayname` = '$targetdname') AND `id` != $user[id]")) {
				$error.="- Displayname already in use.<br />";
			}
		}
	}

	if (checkcusercolor($targetuserid)) {
		//Validate Custom username color is a 6 digit hex RGB color
		$custom_usercolor = $_POST['nick_color'];

		if ($custom_usercolor != "") {
			if (!preg_match('/^([A-Fa-f0-9]{6})$/', $custom_usercolor)) {
				$error.="- Custom usercolor is not a valid RGB hex color.<br />";
			}
		}
	}

	if (checkcextendedprofile($targetuserid)) {
		$qallfields = $sql->query("SELECT * FROM `profileext`");
		$count = false;

		while ($allfieldsgquery = $sql->fetch($qallfields)) {

			$pdata = addslashes($_POST[$allfieldsgquery['id']]);

			if ($pdata) {
				if (!preg_match("/" . $allfieldsgquery['validation'] . "/", $pdata))
					$error.=$allfieldsgquery['title'] . " doesn't match.";
			}
		}
	}

	if (!$error) {
		if (has_perm("edit-users")) {
			$spent = ($userrpg['GP'] + $userrpgdata['spent']) - $_POST['GP'];
			$sql->query("UPDATE usersrpg SET "
					. setfield('eq1') . ","
					. setfield('eq2') . ","
					. setfield('eq3') . ","
					. setfield('eq4') . ","
					. setfield('eq5') . ","
					. setfield('eq6') . ","
					. "`spent` = $spent,"
					. setfield('gcoins')
					. " WHERE `id` = $user[id]"
			);

		  $banreason = ””;
		  if($_POST['title']) $banreason = "`title` = 'Banned permanently: {$_POST['title']}', ";
		  else $banreason = "`title` = 'Banned permanently', ";

			$sql->query("UPDATE users SET "
					. ($targetgroup ? "`group_id` = $targetgroup, " : "")
					. ($_POST['permaban'] ? "`tempbanned` = '0', $banreason" : "")
					. "`name` = '$targetname'"
					. " WHERE `id`=$user[id]"
			);
		} 
		if (checkcextendedprofile($targetuserid)) {
			$qallfields = $sql->query("SELECT * FROM `profileext`");
			$count = false;
			if ($sql->prepare('DELETE FROM `user_profileext` WHERE user_id=?', array($targetuserid))) {
				
			} //Until multiples of each filed are enabled, wipe the slate.
			while ($allfieldsgquery = $sql->fetch($qallfields)) {
				if (substr(setfield($allfieldsgquery['id']), -3) != "=''") {//Should be replated with a better method.
					$pdata = addslashes($_POST[$allfieldsgquery['id']]);
					if ($sql->prepare('INSERT INTO `user_profileext` SET
                user_id=?,field_id=?,data=? ;', array(
								$targetuserid,
								$allfieldsgquery['id'],
								$pdata,
									)
							)) {
						
					}
				}
			}
		}
	}

	if (!$error) {
		$sql->query('UPDATE users SET '
				. ($pass ? 'pass="' . md5($pwdsalt2 . $pass . $pwdsalt) . '",' : '')
				. (checkcdisplayname($targetuserid) ? (setfield('displayname') . ',') : '')
				. (checkcusercolor($targetuserid) ? (setfield('nick_color') . ',') : '')
				. (checkcusercolor($targetuserid) ? (setfield('enablecolor') . ',') : '')
				. setfield('sex') . ','
				. setfield('ppp') . ','
				. setfield('tpp') . ','
				. setfield('signsep') . ','
				. setfield('longpages') . ','
				. setfield('rankset') . ','
				. (checkctitle($targetuserid) && !$_POST['permaban'] ? (setfield('title') . ',') : '')
				. setfield('realname') . ','
				. setfield('location') . ','
				. setfield('email') . ','
				. setfield('homeurl') . ','
				. setfield('homename') . ','
				. setfield('head') . ','
				. setfield('sign') . ','
				. setfield('bio') . ','
				. setfield('fontsize') . ','
				. setfield('theme') . ','
				. setfield('blocklayouts') . ','
				. ($config['spritesystem'] ? (setfield('blocksprites') . ',') : '')
				. setfield('emailhide') . ','
				. setfield('hidesmilies') . ','
				. setfield('numbargfx') . ','
				. ($config['alwaysshowlvlbar'] ? (setfield('showlevelbar') . ',') : '')
				. setfield('posttoolbar') . ','
				. (has_perm("show-online") || has_perm("edit-user-show-online") ? (setfield('hidden') . ',') : '')
				. setfield('timezone') . ','
				. "tzoff=$tztotal,"
				. "birth='$birthday',"
				. "usepic=$usepic,"
				. "minipic=$minipic,"
				. "dateformat='$dateformat',"
				. "timeformat='$timeformat' "
				. "WHERE `id`=$user[id]"
		);

		/* if($loguser[redirtype]==0){ //Classical Redirect
		  $loguser['blocksprites']=1;
		  pageheader('Edit profile');
		  print "<table cellspacing=\"0\" class=\"c1\">
		  ".        "  <td class=\"b n1\" align=\"center\">
		  ".        "    Profile changes saved!<br>
		  ".        "    ".redirect("profile.php?id=$user[id]",'the updated profile')."
		  ".        "</table>
		  ";
		  } else { //Modern redirect */
		redirect("profile.php?id=$user[id]", "Profile was edited successfully.");
		//}
		if ($config['log'] >= '1')
			$sql->query("INSERT INTO log VALUES(UNIX_TIMESTAMP(),'" . $_SERVER['REMOTE_ADDR'] . "','$loguser[id]','ACTION: " . addslashes("user edit " . $targetuserid) . "')");

		die(pagefooter());
	}
	else {
		noticemsg("Error", "Couldn't save the profile changes. The following errors occured:<br><br>" . $error);

		$act = '';
		foreach ($_POST as $k => $v)
			$user[$k] = $v;
		$user['birth'] = $birthday;
	}
}

if ($act == 'Preview theme') {
	/* if($loguser[redirtype]==0){ //Classical Redirect
	  $loguser['blocksprites']=1;
	  pageheader('Edit profile');
	  print "<table cellspacing=\"0\" class=\"c1\">
	  ".        "  <td class=\"b n1\" align=\"center\">
	  ".        "    The theme will be previewed<br>
	  ".        "    ".redirect("/?theme=$_POST[theme]",'the theme preview')."
	  ".        "</table>
	  ";
	  } else { //Modern redirect */
	redirect("/index.php?theme={$_POST['theme']}", 0);
	//}
	die(pagefooter());
}

pageheader('Edit profile');

if (empty($act)) {


	$listsex = array('Male', 'Female', 'N/A');

	$alltz = $sql->query("SELECT name FROM `timezones`");

	$listtimezones = array();
	while ($tz = $sql->fetch($alltz)) {
		$listtimezones[$tz['name']] = $tz['name'];
	}

	$birthM = '';
	$birthD = '';
	$birthY = '';
	if ($user['birth'] != -1) {
		$birthday = explode('-', $user['birth']);
		$birthM = $birthday[0];
		$birthD = $birthday[1];
		$birthY = $birthday[2];
	}

	$dateformats = array('', 'm-d-y', 'd-m-y', 'y-m-d', 'Y-m-d', 'm/d/Y', 'd.m.y', 'M j Y', 'D jS M Y');
	$timeformats = array('', 'h:i A', 'h:i:s A', 'H:i', 'H:i:s');

	foreach ($dateformats as $format)
		$datelist[$format] = ($format ? $format . ' (' . cdate($format, ctime()) . ')' : '');
	foreach ($timeformats as $format)
		$timelist[$format] = ($format ? $format . ' (' . cdate($format, ctime()) . ')' : '');

	$passinput = "<input type=\"password\" name=pass size=13 maxlength=32> / Retype: <input type=\"password\" name=pass2 size=13 maxlength=32>";
	$birthinput = "
" . "      Month: <input type=\"text\" name=birthM size=2 maxlength=2 value=$birthM>
" . "      Day:   <input type=\"text\" name=birthD size=2 maxlength=2 value=$birthD>
" . "      Year:  <input type=\"text\" name=birthY size=4 maxlength=4 value=$birthY>
" . "    ";
	$tzoffinput = "
" . "      <input type=\"text\" name=tzoffH size=3 maxlength=3 value=" . (int) ($user['tzoff'] / 3600) . "> :
" . "      <input type=\"text\" name=tzoffM size=2 maxlength=2 value=" . floor(abs($user['tzoff'] / 60) % 60) . ">
" . "    ";
	//http://jscolor.com/try.php
	$colorinput = "
<script type=text/javascript src=jscolor/jscolor.js></script>
" . "      <input type=\"text\" name=nick_color class=color value=" . $user['nick_color'] . "><input type=checkbox name=enablecolor value=1 id=enablecolor " . ($user['enablecolor'] ? "checked" : "") . "><label for=enablecolor>Enable Color</label>
" . "    ";

	print "<form action='editprofile.php?id=$targetuserid' method='post' enctype='multipart/form-data'>
" . " <table cellspacing=\"0\" class=\"c1\">
" .
			catheader('Login information') . "
" . (has_perm("edit-users") ? fieldrow('Username', fieldinput(40, 255, 'name')) : fieldrow('Username', $user[name])) . "
" . (checkcdisplayname($targetuserid) ? fieldrow('Display name', fieldinput(40, 255, 'displayname')) : "" ) . "
" . fieldrow('Password', $passinput) . "
";

	if (has_perm("edit-users"))
		print
				catheader('Administrative bells and whistles') . "
" . fieldrow('Group', fieldselect('group_id', $user['group_id'], $listgroup)) . "
" . (($user['tempbanned'] > 0) ? fieldrow('Ban Information', '<input type=checkbox name=permaban value=1 id=permaban><label for=permaban>Make ban permanent</label>') : "" ) . "
";

	print
			catheader('Appearance') . "
" . fieldrow('Rankset', fieldselect('rankset', $user['rankset'], ranklist())) . "
" . ((checkctitle($targetuserid)) ? fieldrow('Title', fieldinput(40, 255, 'title')) : "") . "
" . fieldrow('Picture', '<input type=file name=picture size=40> <input type=checkbox name=picturedel value=1 id=picturedel><label for=picturedel>Erase</label><br><font class=sfont>Must be PNG, JPG or GIF, within 80KB, within ' . $avatardimx . 'x' . $avatardimy . '.</font>') . "
" . fieldrow('MINIpic', '<input type=file name=minipic size=40> <input type=checkbox name=minipicdel value=1 id=minipicdel><label for=minipicdel>Erase</label><br><font class=sfont>Must be PNG or GIF, within 10KB, exactly ' . $minipicsize . 'x' . $minipicsize . '.</font>') . "
" . (checkcusercolor($targetuserid) ? fieldrow('Custom username color', $colorinput) : "" ) . "
";

	if (has_perm("edit-users"))
		print catheader('RPG Stats') . "
  " . fieldrow('Coins', fieldinputrpg(9, 7, 'GP')) . "
  " . fieldrow('Frog Coins', fieldinputrpg(9, 7, 'gcoins')) . "
  " . fieldrow('Weapon', itemselect('eq1', $userrpgdata['eq1'], 1)) . "
  " . fieldrow('Armour', itemselect('eq2', $userrpgdata['eq2'], 2)) . "
  " . fieldrow('Shield', itemselect('eq3', $userrpgdata['eq3'], 3)) . "
  " . fieldrow('Helmet', itemselect('eq4', $userrpgdata['eq4'], 4)) . "
  " . fieldrow('Boots', itemselect('eq5', $userrpgdata['eq5'], 5)) . "
  " . fieldrow('Accessory', itemselect('eq6', $userrpgdata['eq6'], 6)) . "
  ";

	print
			catheader('Personal information') . "
" . fieldrow('Sex', fieldoption('sex', $user['sex'], $listsex)) . "
" . fieldrow('Real name', fieldinput(40, 60, 'realname')) . "
" . fieldrow('Location', fieldinput(40, 60, 'location')) . "
" . fieldrow('Birthday', $birthinput) . "
" . fieldrow('Bio', fieldtext(5, 80, 'bio')) . "
" .
			catheader('Post layout') . "
" . fieldrow('Header', fieldtext(5, 80, 'head')) . "
" . fieldrow('Signature', fieldtext(5, 80, 'sign')) . "
" . fieldrow('Signature line', fieldoption('signsep', $user['signsep'], array('Display', 'Hide'))) . "
" .
			catheader('Contact information') . "
" . fieldrow('Email address', fieldinput(40, 60, 'email')) . "
" . fieldrow('Homepage URL', fieldinput(40, 200, 'homeurl')) . "
" . fieldrow('Homepage name', fieldinput(40, 60, 'homename'));
	if (checkcextendedprofile($targetuserid)) {
		$fieldReq = $sql->query("SELECT * FROM `profileext`
                         RIGHT JOIN `user_profileext` ON `profileext`.`id` = `user_profileext`.`field_id`
                         WHERE `user_profileext`.`user_id`='$targetuserid'");
		$userprof = array();
		while ($pfield = $sql->fetch($fieldReq)) {
			$userprof[$pfield['field_id']] = $pfield['data'];
		}

		$qallfields = $sql->query("SELECT * FROM `profileext`");
		while ($allfieldsgquery = $sql->fetch($qallfields)) {
			print fieldrow($allfieldsgquery['title'] . "<br /><small>" . $allfieldsgquery['description'] . " (IE: <b>" . $allfieldsgquery['example'] . "</b>)</small>", fieldinputprofile(40, 200, $allfieldsgquery['id'], $userprof));
		}
	}
	//Implemented the show-online perm. - SquidEmpress
	print"
" .
			catheader('Options') . "
" . fieldrow('Theme', fieldselect('theme', $user['theme'], themelist())) . "
" . fieldrow('Timezone', fieldselect('timezone', $user['timezone'], $listtimezones)) . "
" . fieldrow('Posts per page', fieldinput(3, 3, 'ppp')) . "
" . fieldrow('Threads per page', fieldinput(3, 3, 'tpp')) . "
" . fieldrow('Long pagelists', fieldoption('longpages', $user['longpages'], array('Abbreviate as needed', 'Always display in entirety'))) . "
" . fieldrow('Font size', fieldinput(3, 3, 'fontsize')) . "
" . fieldrow('Date format', fieldinput(15, 15, 'dateformat') . ' or preset: ' . fieldselect('presetdate', 0, $datelist)) . "
" . fieldrow('Time format', fieldinput(15, 15, 'timeformat') . ' or preset: ' . fieldselect('presettime', 0, $timelist)) . "
" . fieldrow('Post layouts', fieldoption('blocklayouts', $user['blocklayouts'], array('Show everything in general', 'Block everything'))) . "
";
	if ($config['spritesystem'])
		print"
" . fieldrow('Sprites', fieldoption('blocksprites', $user['blocksprites'], array('Show them', 'Disable sprite layer'))) . "
";
	print"
" . fieldrow('Smilies', fieldoption('hidesmilies', $user['hidesmilies'], array('Show smilies', 'Do not show smilies'))) . "
" . fieldrow('Hide Email', fieldoption('emailhide', $user['emailhide'], array('Show my email', 'Hide my email'))) . "
";
	if ($user['id'] == $loguser['id'] && has_perm("show-online") || has_perm("edit-user-show-online")) // i think this should have been double equals.
		print"
" . fieldrow('Hide from Online Views', fieldoption('hidden', $user['hidden'], array('Show me online', 'Never show me online'))) . "
";
	print"
" . fieldrow('AB1.x Number and Bar Graphics', fieldoption('numbargfx', $user['numbargfx'], array('Show them in AB1.x themes', 'Never show them in AB1.x themes'))) . "
";
	if ($config['alwaysshowlvlbar'])
		print"
" . fieldrow('EXP level bars', fieldoption('showlevelbar', $user['showlevelbar'], array('Show EXP bars', 'Disable EXP bars'))) . "
";
	print"
" . fieldrow('Posting Toolbar', fieldoption('posttoolbar', $user['posttoolbar'], array('Show Toolbar', 'Hide Toolbar'))) . "
" .
			catheader('&nbsp;') . "
" . "  <tr class=\"n1\">
" . "    <td class=\"b\">&nbsp;</td>
" . "    <td class=\"b\"><input type=\"submit\" class=\"submit\" name=action value='Edit profile'>
" . "    <input type=\"submit\" class=\"submit\" name=action value='Preview theme'></td>
" . " </table>
" . " <input type=\"hidden\" name=token value='$token'>
" . "</form>
";
}

pagefooter();
?>