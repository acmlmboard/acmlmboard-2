<?php

require "lib/common.php";
require "lib/threadpost.php";

$rdmsg = "";
if (!empty($_COOKIE['pstbon'])) {
	header("Set-Cookie: pstbon=" . $_COOKIE['pstbon'] . "; Max-Age=1; Version=1");
	$rdmsg = "<script language=\"javascript\">
	function dismiss()
	{
		document.getElementById(\"postmes\").style['display'] = \"none\";
	}
</script>
	<div id=\"postmes\" onclick=\"dismiss()\" title=\"Click to dismiss.\"><br>
" . "<table cellspacing=\"0\" class=\"c1\" width=\"100%\" id=\"edit\"><tr class=\"h\"><td class=\"b h\">";
	if ($_COOKIE['pstbon'] == -1) {
		$rdmsg.="Edit Successful<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
" . "<tr><td class=\"b n1\" align=\"left\">User has been banned.</td></tr></table></div>";
	} elseif ($_COOKIE['pstbon'] < -1) {
		$rdmsg.="Edit Successful<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
" . "<tr><td class=\"b n1\" align=\"left\">User has been unbanned.</td></tr></table></div>";
	} else {
		$rdmsg.="Edit Successful<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
" . "<tr><td class=\"b n1\" align=\"left\">Profile was edited successfully.</td></tr></table></div>";
	}
}

loadsmilies();

$uid = isset($_GET['id']) ? (int)$_GET['id'] : -1;
if($uid < 0) {
	error("Error", "You must specify a user ID!");
}

$user = $sql->fetchq("SELECT * FROM `users` WHERE `id` = '$uid'");
if(!$user) {
	error("Error", "This user does not exist!");
}

$group = $sql->fetchp("SELECT * FROM `group` WHERE id=?", array($user['group_id']));

pageheader("Profile for " . ($user['displayname'] ? $user['displayname'] : $user['name']));

$days = (ctime() - $user['regdate']) / 86400;
$pfound = $sql->resultq("SELECT count(*) FROM `posts` WHERE `user`='$uid'");
$pavg = sprintf("%1.02f", $user['posts'] / $days);
$tfound = $sql->resultq("SELECT count(*) FROM `threads` WHERE `user`='$uid'");
$tavg = sprintf('%1.02f', $user['threads'] / $days);

if ($user['posts']) {
	$exp = calcexp($user['posts'], (ctime() - $user['regdate']) / 86400);
	$lvl = calclvl($exp);
	$expleft = calcexpleft($exp);
	$expstatus = "Level: $lvl<br>EXP: $exp (for next level: $expleft)";

	$pexp = "$expstatus<br>Gain: " . calcexpgainpost($user['posts'], (ctime() - $user['regdate']) / 86400) . " EXP per post, " . calcexpgaintime($user['posts'], (ctime() - $user['regdate']) / 86400) . " seconds to gain 1 EXP when idle";
} else
	$pexp = "None";


$thread = $sql->fetchq("SELECT `p`.`id`, `t`.`title` `ttitle`, `f`.`title` `ftitle`, `t`.`forum`, `f`.`private`
                            FROM `forums` `f`
                            LEFT JOIN `threads` `t` ON `t`.`forum`=`f`.`id`
                            LEFT JOIN `posts` `p` ON `p`.`thread`=`t`.`id`
                            WHERE `p`.`date`='$user[lastpost]' AND p.user='$uid' AND `f`.`id` IN " . forums_with_view_perm());

$threadhack = $sql->fetchq("SELECT `p`.`id`, `t`.`title` `ttitle`, `t`.`forum`, `t`.`announce`
                            FROM `threads` `t`
                            LEFT JOIN `posts` `p` ON `p`.`thread`=`t`.`id`
                            WHERE `p`.`date`='$user[lastpost]' AND p.user='$uid' AND `t`.`forum`='0'");

if (!$config['topposts'])
	$topposts = 5000;
else
	$topposts = $config['topposts'];
if (!$config['topthreads'])
	$topthreads = 200;
else
	$topthreads = $config['topthreads'];

if ($user['posts'])
	$pprojdate = ctime() + (ctime() - $user['regdate']) * ($topposts - $user['posts']) / ($user['posts']);
if (!$user['posts'] or $user['posts'] >= $topposts or $pprojdate > 2000000000 or $pprojdate < ctime())
	$pprojdate = "";
else
	$pprojdate = " -- Projected date for $topposts posts: " . date("m-d-y h:i A", $pprojdate);


if ($user['threads'])
	$tprojdate = ctime() + (ctime() - $user['regdate']) * ($topthreads - $user['threads']) / ($user['threads']);
if (!$user['threads'] or $user['threads'] >= $topthreads or $tprojdate > 2000000000 or $tprojdate < ctime())
	$tprojdate = "";
else
	$tprojdate = " -- Projected date for $topthreads threads: " . date("m-d-y h:i A", $tprojdate);

if ($pfound && $thread) {
	$lastpostlink = "<br>in <a href=\"thread.php?pid=$thread[id]#$thread[id]\">" . forcewrap(htmlval($thread['ttitle'])) . "</a> 
                     (<a href=\"forum.php?id=$thread[forum]\">" . htmlval($thread['ftitle']) . "</a>)";
} else if ($pfound && $threadhack['announce'] && $threadhack['forum'] == 0) {
	$lastpostlink = "<br>in <a href=\"thread.php?pid=$threadhack[id]#$threadhack[id]\">" . forcewrap(htmlval($threadhack['ttitle'])) . "</a>  
                     (<a href=\"thread.php?announce=0\">Announcements</a>)";
} else if ($user['posts'] == 0) {
	$lastpostlink = "";
} else {
	$lastpostlink = "<br>in <i>(restricted forum)</i>";
}

//[KAWA] Adapting to new theme system...
$themes = unserialize(file_get_contents("themes_serial.txt"));
$themename = $themes[0][0];
foreach ($themes as $theme) {
	if ($theme[1] == $user['theme']) {
		$themename = $theme[0];
		break;
	}
}

if ($user['birth'] != -1) {
	//Crudely done code.
	//You're Goddamn right. :P - SquidEmpress
	$monthnames = array(1 => 'January', 'February', 'March', 'April',
		'May', 'June', 'July', 'August',
		'September', 'October', 'November', 'December');
	$bdec = explode("-", $user['birth']);
	$bstr = $bdec[2] . "-" . $bdec[0] . "-" . $bdec[1];
	$mn = intval($bdec[0]);
	if ($bdec['2'] <= 0 && $bdec['2'] > -2)
		$birthday = $monthnames[$mn] . " " . $bdec[1];
	else
		$birthday = date("l, F j, Y", strtotime($bstr));

	$age = '<!-- This feature requires PHP 5.3.0 or higher -->';
	if (class_exists('DateTime') && method_exists('DateTime', 'diff')) {
		$bd1 = new DateTime($bstr);
		$bd2 = new DateTime(date("Y-m-d"));
		if ($bd2 < $bd1 && !$bdec['2'] <= 0)
			$age = '(not born yet)';
		else if ($bdec['2'] <= 0 && $bdec['2'] > -2)
			$age = '';
		else {
			$bd3 = $bd1->diff($bd2);
			$age = "(" . intval($bd3->format("%Y")) . " years old)";
		}
	}
} else {
	$birthday = "";
	$age = "";
}

//This code was done by Gywall 
if ($user['email'] && !$user['emailhide']) {
	$email = EmailObscurer($user['email']);
} else {
	$email = "";
}

if ($user['homeurl'] && $user['homename'])
	$homepage = "<a href=\"" . htmlval($user['homeurl']) . "\">" . htmlval($user['homename']) . "</a> - " . htmlval($user['homeurl']);
elseif ($user['homeurl'] && !$user['homename'])
	$homepage = "<a href=\"" . htmlval($user['homeurl']) . "\">" . htmlval($user['homeurl']) . "</a>";
elseif (!$user['homeurl'] && $user['homename'])
	$homepage = $user['homeurl'];
else
	$homepage = "";

if ($user['url'][0] == "!") {
	$user['url'] = substr($user['url'], 1);
	$user['ssl'] = 1;
}

$post['date'] = ctime();
$post['ip'] = $user['ip'];
$post['num'] = 0; //$user[posts];  #2/26/2007 xkeeper - threadpost can hide "1/" now

$post['mood'] = -1;
$post['id'] = -1;
$post['nolayout'] = 0;
$post['nosmilies'] = 0;
$post['thread'] = -1;

$post['text'] = "[quote=\"Emuz\" id=\"2\"]<a href=\"/#fakelink\">[b]The[/b] sample link.</a> [quote=\"Acmlm\" id=\"1\"][quote=\"Shroomy\"]Sample nested quote.[/quote][spoiler]Sample spoiler, but I guess I ruined that for you hehe[/spoiler][/quote]^Sample [u]sarcasm[/u][/quote]
How about some code: 
[code]<?php
if (\$whateverthislongstupidvariable == \$anotherstupidlylongnamedvariable) //Scrydan was here.... 
{
   print \"Sample code.\"; #oops you just missed him!
}
?>[/code]
[i]Sample[/i] message.";

/* "[quote=nobody in particular]".
  "[quote=somebody else](<a href=http://en.wikipedia.org/wiki/Example>sample</a> quote)".
  "[quote=William Shakespeare]".
  "<a href=http://en.wikipedia.org/wiki/Bracket>(</a>sample quote<a href=http://en.wikipedia.org/wiki/Bracket>)</a>[/quote][/quote]".
  "(sample <a href=http://en.wikipedia.org/wiki/Quote>quote</a>)[/quote](sample <a href=http://en.wikipedia.org/wiki/Text>text</a>)"; */

foreach ($user as $field => $val) {
	$post['u' . $field] = $val;
}

$shoplist = "
         <table cellspacing=\"0\" class=\"c1\" width=\"100%\">
           <tr class=\"h\">
             <td class=\"b h\" colspan=\"2\">Equipped Items</td></tr>";

$shops = $sql->query('SELECT * FROM itemcateg ORDER BY corder');
$eq = $sql->fetchq("SELECT * FROM usersrpg WHERE id='$uid'");
$eqitems = $sql->query("SELECT * FROM items WHERE `id`='$eq[eq1]' OR `id`='$eq[eq2]' OR `id`='$eq[eq3]' OR `id`='$eq[eq4]' OR `id`='$eq[eq5]' OR `id`='$eq[eq6]'");

while ($item = $sql->fetch($eqitems)) {
	$items[$item['id']] = $item;
}

while ($shop = $sql->fetch($shops)) {
	$shoplist.="
         <tr class=\"sfont\">
           <td class=\"b n1\" width=\"70\">$shop[name]</td>
           <td class=\"b n2\"><a href=\"shop.php?action=desc&id=" . $eq['eq' . $shop['id']] . "\">" . $items[$eq['eq' . $shop['id']]]['name'] . "</a></td>
         </tr>";
}
$shoplist.="
         </table>";

//New Badge List
$badgelist = "";
if ($config['badgesystem']) {
	$q = $sql->query("SELECT * FROM `badges`
                       RIGHT JOIN `user_badges` ON `badges`.`id` = `user_badges`.`badge_id`
                       WHERE `user_badges`.`user_id`='$uid' AND `badges`.`image`!= '' ORDER BY `priority` DESC LIMIT 9");

	if (!$sql->numrows($q) == 0) {
		$badgelist = "
           <table cellspacing=\"0\" class=\"c1\" width=\"100%\">
             <tr class=\"h\">
               <td class=\"b h\" colspan=\"3\">Badges</td></tr>";
		$numbadges = 0;
		$badgelist.="<tr>";
		while ($badge = $sql->fetch($q)) {
			$badgelist.= "<td class=\"b n2\" align=\"center\"><img src=\"" . htmlval($badge['image']) . "\" alt=\"\" title=\"" . htmlval(str_replace("%%%VAL%%%", $badge['badge_var'], $badge['name'])) . "\" /></td>";
			$numbadges++;
			if ($numbadges % 3 == 0)
				$badgelist .= "</tr><tr>";
		}
		while ($numbadges < 9) {
			$badgelist.= "<td class=\"b n1\" align=\"center\"><img src=\"img/_.png\" width=\"25\" height=\"25\" /></td>";
			$numbadges++;
			if ($numbadges % 3 == 0)
				$badgelist .= "</tr><tr>";
		}
		$badgelist.="            <tr class=\"h\">
               <td class=\"b n1\" align=\"center\" colspan=\"3\"><a href=\"badges.php?uid=" . $uid . "\">(more)</a></td></tr>";
		$badgelist .= "</table>    <br>";
	}
}
//END badge list
//More indepth test to not show the link if you can't edit your own perms
$editpermissions = "";
if (has_perm('edit-permissions')) {
	if (!has_perm('edit-own-permissions') && $loguser['id'] == $uid)
		$editpermissions = "";
	else
		$editpermissions = "| <a href=\"editperms.php?uid=" . $user['id'] . "\">Edit user permissions</a>";
}

$secondarygroups = "";
if (has_perm('assign-secondary-groups')) {
	/* if(!has_perm('edit-own-permissions') && $loguser['id'] == $uid) $secondarygroups =""; //Not really needed in normal context. I commented it out in case someone may want this -Emuz
	  else */$secondarygroups = "| <a href=\"assignsecondary.php?uid=" . $user['id'] . "\">Manage secondary groups</a>";
}

$bannedgroup = $sql->resultq("SELECT id FROM `group` WHERE `banned`=1");

$banuser = "";
if (has_perm('edit-permissions')) {
	if (!has_perm('ban-users'))
		$banuser = "";
	elseif ($user['group_id'] != $bannedgroup)
		$banuser = "| <a href=\"banmanager.php?id=" . $user['id'] . "\">Ban user</a>";
	elseif ($user['group_id'] = $bannedgroup)
		$banuser = "| <a href=\"banmanager.php?unban&id=" . $user['id'] . "\">Unban user</a>";
}

//[KAWA] Blocklayout ported from ABXD
$qblock = "SELECT * FROM `blockedlayouts` WHERE `user`='$uid' AND `blockee`='$loguser[id]'";
$rblock = $sql->query($qblock);
$isblocked = $sql->numrows($rblock);
$blocklayoutlink = '';

if (has_perm("block-layout")) {
	if (isset($_GET['block']) && $log) {
		$block = (int) $_GET['block'];

		if ($block && !$isblocked) {
			$qblock = "INSERT INTO `blockedlayouts` (`user`, `blockee`) values ('$uid', '$loguser[id]')";
			$rblock = $sql->query($qblock);
			$blockmessage = "Layout blocked.";
			$isblocked = true;
		} elseif (!$block && $isblocked) {
			$qblock = "DELETE FROM `blockedlayouts` WHERE `user`='$uid' AND `blockee`='$loguser[id]' LIMIT 1";
			$rblock = $sql->query($qblock);
			$blockMessage = "Layout unblocked.";
			$isblocked = false;
		}

		if ($blockmessage) {
			print "
       <table cellspacing=\"0\" class=\"c1\">
         <td class=\"b n1\" align=\"center\">
           $blockmessage
       </table>";
		}
	}
	
	if ($isblocked)
		$blocklayoutlink = "| <a href=\"profile.php?id=$uid&amp;block=0\">Unblock layout</a>";
	else
		$blocklayoutlink = "| <a href=\"profile.php?id=$uid&amp;block=1\">Block layout</a>";
}

//timezone calculations
$now = new DateTime("now");
$usertz = new DateTimeZone($user['timezone']);
$userdate = new DateTime("now", $usertz);
$userct = date_format($userdate, $dateformat);
$logtz = new DateTimeZone($loguser['timezone']);
$usertzoff = $usertz->getOffset($now);
$logtzoff = $logtz->getOffset($now);

$user['showminipic'] = 1;

//User color override - Should be moved to a function.
$u = ''; // what was this originally?
$group = $usergroups[$user[$u . 'group_id']];
$realnc = $group['nc' . $user[$u . 'sex']];

//Toggles class define for spans where appropriate
$unclass = '';
$usercnickcolor = '';
$userdisplayname = false;
$showrealnick = false;

if ($config['useshadownccss'])
	$unclass = "class='needsshadow'";
//If user has a a displayname, a custom username color, or both, we need to show the realname field.
if ($config['perusercolor'] && $user['enablecolor'])
	$usercnickcolor = $user['nick_color'];

if ($config['displayname'] && $user['displayname'])
	$userdisplayname = true;

if ($userdisplayname || $usercnickcolor) {
	$showrealnick = true;
}

print "<a href=\"./\">Main</a> - Profile for " . userdisp($user) . "
           <br><br>
";
if (!empty($_COOKIE['pstbon'])) {
	print $rdmsg;
}
print "    <table cellspacing=\"0\" width=\"100%\">
             <td class=\"nb\" valign=\"top\">
               <table cellspacing=\"0\" class=\"c1\">
                 <tr class=\"h\">
                   <td class=\"b h\" colspan=\"2\">General information</td>
                   " . ($showrealnick ? "<tr><td class=\"b n1\" width=\"110\"><b>Real handle</b></td><td class=\"b n2\"><span $unclass style='color:#" . $realnc . ";'><b>" . htmlval($user['name']) . "</b></span>" : "") . "
                 <tr>
                   <td class=\"b n1\" width=\"110\"><b>Group</b></td>
                   <td class=\"b n2\">$group[title]
                 <tr>
                   <td class=\"b n1\" width=\"110\"><b>Total posts</b></td>
                   <td class=\"b n2\">$user[posts] ($pfound found, $pavg per day)$pprojdate
                 <tr>
                   <td class=\"b n1\"><b>Total threads</b></td>
                   <td class=\"b n2\">$user[threads] ($tfound found, $tavg per day)$tprojdate
                 <tr>
                   <td class=\"b n1\"><b>EXP status</b></td>
                   <td class=\"b n2\">$pexp
                 <tr>
                   <td class=\"b n1\"><b>Registered on</b></td>
                   <td class=\"b n2\">" . cdate($dateformat, $user['regdate']) . " (" . timeunits($days * 86400) . " ago)
                 <tr>
                   <td class=\"b n1\"><b>Last post</b></td>
                   <td class=\"b n2\">
                     " . ($user['lastpost'] ? cdate($dateformat, $user['lastpost']) . " (" . timeunits(ctime() - $user['lastpost']) . " ago)" : "None") . "
                     $lastpostlink
                 <tr>
                   <td class=\"b n1\"><b>Last view</b></td>
                   <td class=\"b n2\">
                     " . cdate($dateformat, $user['lastview']) . " (" . timeunits(ctime() - $user['lastview']) . " ago)
                     " . ($user['url'] ? "<br>at <a href=\"" . htmlval($user['url']) . "\">" . htmlval($user['url']) . "</a>" : '') . "
                     " . ($user['ip'] && has_perm("view-post-ips") ? "<br>from IP: $user[ip]" : '') . "
               </table>
               <br>
               <table cellspacing=\"0\" class=\"c1\">
                 <tr class=\"h\">
                   <td class=\"b h\" colspan=\"2\">Contact information</td>
                 <tr>
                   <td class=\"b n1\" width=\"110\"><b>Email address</b></td>
                   <td class=\"b n2\">$email
                 <tr>
                   <td class=\"b n1\"><b>Homepage</b></td>
                   <td class=\"b n2\">$homepage";

if ($config['extendedprofile']) {
	$fieldReq = $sql->query("SELECT * FROM `profileext`
                       RIGHT JOIN `user_profileext` ON `profileext`.`id` = `user_profileext`.`field_id`
                       WHERE `user_profileext`.`user_id`='$uid'");
	while ($pfield = $sql->fetch($fieldReq)) {
		print "                 <tr>
                   <td class=\"b n1\"><b>" . $pfield['title'] . "</b></td>";

		if ($pfield['parser'] == "email") {
			$fieldvalue = EmailObscurer($pfield['data']);
		} else
			$fieldvalue = preg_replace("/$pfield[validation]/", $pfield['fmt'], $pfield['data']);

		print "                 <td class=\"b n2\">" . $fieldvalue;
	}
}
print "               </table>
                   <br>";

print "<table cellspacing=\"0\" class=\"c1\">
                 <tr class=\"h\">
                   <td class=\"b h\" colspan=\"2\">User settings</td>
                 <tr>
                   <td class=\"b n1\" width=\"110\"><b>Theme</b></td>
                   <td class=\"b n2\">
                     " . htmlval($themename) . "
                 <tr>
                   <td class=\"b n1\" width=\"110\"><b>Time offset</b></td>
                   <td class=\"b n2\">
                     " . sprintf("%d:%02d", ($usertzoff - $logtzoff) / 3600, abs(($usertzoff - $logtzoff) / 60) % 60) . " from you
                     <br>(current time: " . $userct . ")
                 <tr>
                   <td class=\"b n1\"><b>Items per page</b></td>
                   <td class=\"b n2\">$user[ppp] posts, $user[tpp] threads
               </table>
               <br>
               <table cellspacing=\"0\" class=\"c1\">
                 <tr class=\"h\">
                   <td class=\"b h\" colspan=\"2\">Personal information</td>
                 <tr>
                   <td class=\"b n1\" width=\"110\"><b>Real name</b></td>
                   <td class=\"b n2\">" . ($user['realname'] ? htmlval($user['realname']) : "") . "
                 <tr>
                   <td class=\"b n1\"><b>Location</b></td>
                   <td class=\"b n2\">" . ($user['location'] ? htmlval($user['location']) : "") . "
                 <tr>
                   <td class=\"b n1\"><b>Birthday</b></td>
                   <td class=\"b n2\">$birthday $age
                 <tr>
                   <td class=\"b n1\"><b>Bio</b></td>
                   <td class=\"b n2\">" . ($user['bio'] ? postfilter($user['bio']) : "") . "
               </table>
             </td>
             <td class=\"nb\" width=\"15\"></td>
             <td class=\"nb\" width=\"256\" valign=\"top\">
               <table cellspacing=\"0\" class=\"c1\">
                 <tr class=\"h\">
                   <td class=\"b h\" colspan=\"2\">RPG status</td>
                 <tr>
                   <td class=\"b n1\"><img src=\"gfx/status.php?u=$uid\">
               </table>
               <br>
           $badgelist
           $shoplist
             </td>
           </table>
           <br>
           <table cellspacing=\"0\" class=\"c1\">
             <tr class=\"h\">
               <td class=\"b h\">Sample post</td>
             <tr>
           </table>
           " . threadpost($post, 0) . "
           <br>
           <table cellspacing=\"0\" class=\"c1\">
             <tr class=\"h\">
               <td class=\"b n2\"><a href=\"forum.php?user=$user[id]\">View threads</a>
                       | <a href=\"thread.php?user=$user[id]\">Show posts</a>
                       | <a href=\"postsbyuser.php?id=$user[id]\">List posts</a>
                       | <a href=\"postsbyuser.php?postsbytime&id=$user[id]\">Posts by time of day</a>
                       | <a href=\"postsbyuser.php?postsbyforum&id=$user[id]\">Posts by forum</a>
                       | <a href=\"postsbyuser.php?postsbythread&id=$user[id]\">Posts by thread</a>
                       $blocklayoutlink
                       " . (has_perm('create-pms') ? "| <a href=\"sendprivate.php?uid=" . $user['id'] . "\">Send private message</a>" : "") . "
                       " . (has_perm('view-user-pms') ? "| <a href=\"private.php?id=" . $user['id'] . "\">View private messages</a>" : "") . "
                       " . (has_perm('edit-moods') ? "| <a href=\"mood.php?user=" . $user['id'] . "\">Edit mood avatars</a>" : "") . "
                       " . (has_perm('edit-users') ? "| <a href=\"editprofile.php?id=" . $user['id'] . "\">Edit user</a>" : "") . "
                       " . $banuser . " " . $editpermissions . " " . $secondarygroups . "
           </table>";
pagefooter();
?>