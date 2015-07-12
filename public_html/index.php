<?php

/* index.php ****************************************
  Changelog
  Xkeeper     fixed what blackhole89 broke, which was mostly nothing
  blackhole89 moved mark forum /all forums read here
  blackhole89 added consideration of minpower for forum/category display
  Xkeeper     added support for category ordering
 * ************************************************** */


if (isset($_GET['p'])) {
	$p = $_GET['p'];
	return header("Location:thread.php?pid=$p#$p");
}
if (isset($_GET['t'])) {
	$t = $_GET['t'];
	return header("Location:thread.php?id=$t");
}
if (isset($_GET['u'])) {
	$u = $_GET['u'];
	return header("Location:profile.php?id=$u");
}
if (isset($_GET['a'])) {
	$a = $_GET['a'];
	return header("Location:thread.php?announce=$a");
}
$showonusers = 1;
require 'lib/common.php';

$rdmsg = "";
if (isset($_COOKIE['pstbon']) && $_COOKIE['pstbon'] == -1) {
	header("Set-Cookie: pstbon=" . $_COOKIE['pstbon'] . "; Max-Age=1; Version=1");
	$rdmsg = "<script language=\"javascript\">
	function dismiss()
	{
		document.getElementById(\"postmes\").style['display'] = \"none\";
	}
</script>
	<div id=\"postmes\" onclick=\"dismiss()\" title=\"Click to dismiss.\"><br>
" . "<table cellspacing=\"0\" class=\"c1\" width=\"100%\" id=\"edit\"><tr class=\"h\"><td class=\"b h\">";
	$rdmsg.="Post Radar saved!<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
" . "<tr><td class=\"b n1\" align=\"left\">Post Radar has been saved successfully.</td></tr></table></div>";
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

//mark forum read
if ($log && $action == 'markread') {
	$fid = $_GET['fid'];
	if ($fid != 'all') {
		checknumeric($fid);
		//delete obsolete threadsread entries
		$sql->query("DELETE r "
				. "FROM threadsread r "
				. "LEFT JOIN threads t ON t.id=r.tid "
				. "WHERE t.forum=$fid "
				. "AND r.uid=" . $loguser['id']);
		//add new forumsread entry
		$sql->query("REPLACE INTO forumsread VALUES ($loguser[id],$fid," . ctime() . ')');
	} else {
		//mark all read
		$sql->query("DELETE FROM threadsread WHERE uid=" . $loguser['id']);
		$sql->query("REPLACE INTO forumsread (uid,fid,time) SELECT " . $loguser['id'] . ",f.id," . ctime() . " FROM forums f");
	}

	// remove nasty GET strings so that refreshers like me don't mark things read over and over and burp
	header('Location: index.php');
}

// Moved pageheader here so that we can do header()s without fucking everything up again
pageheader();

$categs = $sql->query("SELECT * "
		. "FROM categories "
		. "ORDER BY ord,id");
while ($c = $sql->fetch($categs)) {
	if (can_view_cat($c))
		$categ[$c['id']] = $c;
}

//[KAWA] ABXD does ignores with a very nice SQL trick that I think Mega-Mario came up with one day.
//Unfortunately, this place is too hairy to add the trick to so I'll have to use a third query to collect the ignores. The first is categories. The second is the forum list itself.
$ignores = array();
$ignoreQ = $sql->query("SELECT * FROM ignoredforums WHERE uid = " . $loguser['id']);
while ($i = $sql->fetch($ignoreQ))
	$ignores[$i['fid']] = true;

$forums = $sql->query("SELECT f.*" . ($log ? ", r.time rtime" : '') . ", c.private cprivate, " . userfields('u', 'u') . ", u.minipic uminipic "
		. "FROM forums f "
		. "LEFT JOIN users u ON u.id=f.lastuser "
		. "LEFT JOIN categories c ON c.id=f.cat "
		. ($log ? "LEFT JOIN forumsread r ON r.fid=f.id AND r.uid=$loguser[id] " : '')
		. " WHERE announce=0 "
		. "ORDER BY c.ord,c.id,f.ord,f.id");
$cat = -1;
if (isset($_COOKIE['pstbon'])) {
	print $rdmsg;
}
print "
" . "<table cellspacing=\"0\" class=\"c1\">";

echo announcement_row(0, 2, 3);

echo
"  <tr class=\"h\">
" . "    <td class=\"b h\" width=17>&nbsp;</td>
" . "    <td class=\"b h\">Forum</td>
" . "    <td class=\"b h\" width=50>Threads</td>
" . "    <td class=\"b h\" width=50>Posts</td>
" . "    <td class=\"b h\" width=150>Last post</td>
";

$lmods = array();
$r = $sql->query("SELECT f.fid, " . userfields('u') . " FROM forummods f LEFT JOIN users u ON u.id=f.uid");
while ($mod = $sql->fetch($r))
	$lmods[$mod['fid']][] = $mod;

while ($forum = $sql->fetch($forums)) {
	if (!can_view_forum($forum))
		continue;

	if ($forum['cat'] != $cat) {
		$cat = $forum['cat'];
		print "  <tr class=\"c\">
" . "    <td class=\"b\" colspan=5>" . ($categ[$cat]['private'] ? ('(' . ($categ[$cat]['title']) . ')') : ($categ[$cat]['title'])) . "</td>
";
	}

	if ($forum['posts'] > 0 && $forum['lastdate'] > 0)
		$lastpost = '<nobr>' . cdate($dateformat, $forum['lastdate']) . '</nobr><br><font class=sfont>by&nbsp;' . userlink($forum, 'u', $config['indexminipic']) . "&nbsp;<a href='thread.php?pid=" . $forum['lastid'] . "#" . $forum['lastid'] . "'>&raquo;</a></font>";
	else
		$lastpost = 'None';

	if ($forum['lastdate'] > ($log ? $forum['rtime'] : ctime() - 3600)) {
		if ($log) {
			$thucount = $sql->resultq("SELECT count(*) FROM threads t"
					. " LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id])"
					. " LEFT JOIN forumsread f ON (f.fid=t.forum AND f.uid=$loguser[id])"
					. " WHERE t.forum=$forum[id]"
					. " AND ((r.time < t.lastdate OR isnull(r.time)) AND (f.time < t.lastdate OR isnull(f.time)))"
					. " AND (r.uid=$loguser[id] OR isnull(r.uid))");
			$status = rendernewstatus("n", $thucount);
		} else {
			$status = '&nbsp;';
		}
	} else {
		$status = '&nbsp;';
	}

	if (isset($ignores[$forum['id']])) {
		$status = "&nbsp;";
		$ignoreFX = "style=\"opacity: 0.5;\"";
	} else
		$ignoreFX = "";

	$modstring = "";
	if (isset($lmods[$forum['id']]))
		foreach ($lmods[$forum['id']] as $mod)
			$modstring.=userlink($mod) . ", ";
	if ($modstring)
		$modstring = "<br>(moderated by: " . substr($modstring, 0, -2) . ")";
//    else $modstring="<p>&nbsp;</p>";
	print
			"  <tr align=\"center\">
" . "    <td class=\"b n1\">$status</td>
" . "    <td class=\"b n2\" align=\"left\">
" . "      " . ($forum['private'] ? '(' : '') . "<a href=\"forum.php?id=$forum[id]\" $ignoreFX>$forum[title]</a>" . ($forum['private'] ? ')' : '') . "<br>
" . "      <span class=sfont $ignoreFX>" . str_replace("%%%SPATULANDOM%%%", $spatulas[$spaturand], $forum['descr']) . "$modstring</span>
" . "    </td>
" . "    <td class=\"b n1\">$forum[threads]</td>
" . "    <td class=\"b n1\">$forum[posts]</td>
" . "    <td class=\"b n2\">$lastpost</td>
";
}
print "</table>
";
pagefooter();
?>