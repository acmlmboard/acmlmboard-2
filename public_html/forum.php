<?php

/* forum.php *************************************
  Changelog
  0221  blackhole89    modified queries and $status calculation to use the new "threads read" system
 */
require 'lib/common.php';

$page = isset($_GET['page']) && $page > 0 ? (int)$_GET['page'] : 1;
$fid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$uid = isset($_GET['user']) ? (int)$_GET['user'] : 0;

if ($fid = $_GET['id']) {
	checknumeric($fid);

	if ($log) {
		$forum = $sql->fetchq("SELECT f.*, r.time rtime FROM forums f "
				. "LEFT JOIN forumsread r ON (r.fid=f.id AND r.uid=$loguser[id]) "
				. "WHERE f.id=$fid AND f.id IN " . forums_with_view_perm());
		if (!$forum['rtime'])
			$forum['rtime'] = 0;
	} else
		$forum = $sql->fetchq("SELECT * FROM forums WHERE id=$fid AND id IN " . forums_with_view_perm());


	if (!isset($forum['id'])) {
		error("Error", "Forum does not exist.<br> <a href=./>Back to main</a>");
	}

	//load tags
	$tags = array();
	$t = $sql->query("SELECT * FROM tags WHERE fid=$fid");
	while ($tt = $sql->fetch($t))
		$tags[] = $tt;

	$feedicons.=feedicon("img/rss2.png", "rss.php?forum=$fid", "RSS feed for this section");

	//append the forum's title to the site title
	pageheader($forum['title'], $fid);

	//forum access control // 2007-02-19 blackhole89 // 2011-11-09 blackhole89 tokenisation (more than 4.5 years...)
	//2012-01-01 DJBouche Happy New Year!
//[KAWA] Copypasting a chunk from ABXD, with some edits to make it work here.
	$isIgnored = $sql->resultq("select count(*) from ignoredforums where uid=" . $loguser['id'] . " and fid=" . $fid) == 1;
	if (isset($_GET['ignore'])) {
		if (!$isIgnored && $loguser['id'] != 0) {
			$sql->query("insert into ignoredforums values (" . $loguser['id'] . ", " . $fid . ")");
			$isIgnored = true;
			print
					"<table cellspacing=\"0\" class=\"c1\">
" . "  <tr class=\"n2\">
" . "    <td class=\"b n1\" align=\"center\">
" . "      Forum ignored. You will no longer see any \"New\" markers for this forum.
" . "</table>
";
		}
	} else if (isset($_GET['unignore'])) {
		if ($isIgnored) {
			$sql->query("delete from ignoredforums where uid=" . $loguser['id'] . " and fid=" . $fid);
			$isIgnored = false;
			print
					"<table cellspacing=\"0\" class=\"c1\">
" . "  <tr class=\"n2\">
" . "    <td class=\"b n1\" align=\"center\">
" . "      Forum unignored.
" . "</table>
";
		}
	}

	$editforumlink = "";

	if (has_perm('edit-forums')) {
		$editforumlink = "<a href=\"manageforums.php?fid=$fid\" class=\"editforum\">Edit Forum</a> | ";
	}

	if ($loguser['id'] != 0) {
		$ignoreLink = $isIgnored ? "<a href=\"forum.php?id=$fid&amp;unignore\" class=\"unignoreforum\">Unignore forum</a> " : "<a href=\"forum.php?id=$fid&amp;ignore\" class=\"ignoreforum\">Ignore forum</a> ";
	}
	$threads = $sql->query("SELECT " . userfields('u1', 'u1') . "," . userfields('u2', 'u2') . ", t.*, 

    (SELECT COUNT(*) FROM threadthumbs WHERE tid=t.id) AS thumbcount,

    (NOT ISNULL(p.id)) ispoll" . ($log ? ", (NOT (r.time<t.lastdate OR isnull(r.time)) OR t.lastdate<'$forum[rtime]') isread" : '') . ' '
			. "FROM threads t "
			. "LEFT JOIN users u1 ON u1.id=t.user "
			. "LEFT JOIN users u2 ON u2.id=t.lastuser "
			. "LEFT JOIN polls p ON p.id=t.id "
			. ($log ? "LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id])" : '')
			. "WHERE t.forum=$fid AND t.announce=0 "
			. "ORDER BY t.sticky DESC, t.lastdate DESC "
			. "LIMIT " . (($page - 1) * $loguser['tpp']) . "," . $loguser['tpp']);
	$topbot = "<table cellspacing=\"0\" width=100%>
" . "  <td class=\"nb\"><a href=./>Main</a> - <a href=forum.php?id=$fid>$forum[title]</a></td>
" . "  <td class=\"nb\" align=\"right\">" . $editforumlink . $ignoreLink . (can_create_forum_thread($forum) ? "| <a href=\"newthread.php?id=$fid\" class=\"newthread\">New thread</a> | <a href=\"newthread.php?id=$fid&ispoll=1\" class=\"newpoll\">New poll</a>" : "") . "</td>
" . "</table>
";
} elseif ($uid = $_GET['user']) {
	checknumeric($uid);
	$user = $sql->fetchq("SELECT * FROM users WHERE id=$uid");

	pageheader("Threads by " . ($user['displayname'] ? $user['displayname'] : $user['name']));

	$threads = $sql->query("SELECT " . userfields('u1', 'u1') . "," . userfields('u2', 'u2') . ", t.*, f.id fid, f.title ftitle, 
    (SELECT COUNT(*) FROM threadthumbs WHERE tid=t.id) AS thumbcount,


    (NOT ISNULL(p.id)) ispoll" . ($log ? ", (NOT (r.time<t.lastdate OR isnull(r.time)) OR t.lastdate<fr.time) isread" : '') . ' '
			. "FROM threads t "
			. "LEFT JOIN users u1 ON u1.id=t.user "
			. "LEFT JOIN users u2 ON u2.id=t.lastuser "
			. "LEFT JOIN polls p ON p.id=t.id "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. ($log ? "LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id]) "
					. "LEFT JOIN forumsread fr ON (fr.fid=f.id AND fr.uid=$loguser[id]) " : '')
			. "LEFT JOIN categories c ON f.cat=c.id "
			. "WHERE t.user=$uid "
			. "AND f.id IN " . forums_with_view_perm() . " "
			. "ORDER BY t.sticky DESC, t.lastdate DESC "
			. "LIMIT " . (($page - 1) * $loguser[tpp]) . "," . $loguser[tpp]);

	$forum[threads] = $sql->resultq("SELECT count(*) "
			. "FROM threads t "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON f.cat=c.id "
			. "WHERE t.user=$uid "
			. "AND f.id IN " . forums_with_view_perm() . " ");
	$topbot = "<table cellspacing=\"0\" width=100%>
" . "  <td class=\"nb\"><a href=./>Main</a> - Threads by " . ($user[displayname] ? $user[displayname] : $user[name]) . "</td>
" . "</table>
";
} elseif ($time = $_GET[time]) {
	checknumeric($time);
	$mintime = ctime() - $time;

	pageheader('Latest posts');

	$threads = $sql->query("SELECT " . userfields('u1', 'u1') . "," . userfields('u2', 'u2') . ", t.*, f.id fid, 
    (SELECT COUNT(*) FROM threadthumbs WHERE tid=t.id) AS thumbcount,


    (NOT ISNULL(p.id)) ispoll, f.title ftitle" . ($log ? ', (NOT (r.time<t.lastdate OR isnull(r.time)) OR t.lastdate<fr.time) isread' : '') . ' '
			. "FROM threads t "
			. "LEFT JOIN users u1 ON u1.id=t.user "
			. "LEFT JOIN users u2 ON u2.id=t.lastuser "
			. "LEFT JOIN polls p ON p.id=t.id "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON f.cat=c.id "
			. ($log ? "LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id]) "
					. "LEFT JOIN forumsread fr ON (fr.fid=f.id AND fr.uid=$loguser[id]) " : '')
			. "WHERE t.lastdate>$mintime "
			. "  AND f.id IN " . forums_with_view_perm() . " "
			. "ORDER BY t.lastdate DESC "
			. "LIMIT " . (($page - 1) * $loguser[tpp]) . "," . $loguser[tpp]);
	$forum[threads] = $sql->resultq("SELECT count(*) "
			. "FROM threads t "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON f.cat=c.id "
			. "WHERE t.lastdate>$mintime "
			. "AND f.id IN " . forums_with_view_perm() . " ");

	function timelink($timev) {
		global $time;
		if ($time == $timev)
			return " " . timeunits2($timev) . " ";
		else
			return " <a href=forum.php?time=$timev>" . timeunits2($timev) . '</a> ';
	}

	$topbot = "<table cellspacing=\"0\" width=100%>
" . "  <td class=\"nb\"><a href=./>Main</a> - Latest posts</td>
" . "</table>
";
}elseif (isset($_GET[fav]) && has_perm('view-favorites')) {

	pageheader("Favorite Threads");


	$threads = $sql->query("SELECT " . userfields('u1', 'u1') . "," . userfields('u2', 'u2') . ", t.*, f.id fid, f.title ftitle, 
    (SELECT COUNT(*) FROM threadthumbs WHERE tid=t.id) AS thumbcount,


    (NOT ISNULL(p.id)) ispoll" . ($log ? ", (NOT (r.time<t.lastdate OR isnull(r.time)) OR t.lastdate<fr.time) isread" : '') . ' '
			. "FROM threads t "
			. "LEFT JOIN users u1 ON u1.id=t.user "
			. "LEFT JOIN users u2 ON u2.id=t.lastuser "
			. "LEFT JOIN polls p ON p.id=t.id "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN threadthumbs th ON th.tid=t.id "
			. ($log ? "LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id]) "
					. "LEFT JOIN forumsread fr ON (fr.fid=f.id AND fr.uid=$loguser[id]) " : '')
			. "LEFT JOIN categories c ON f.cat=c.id "
			. "WHERE th.uid=$loguser[id] "
			. "AND f.id IN " . forums_with_view_perm() . " "
			. "ORDER BY t.sticky DESC, t.lastdate DESC "
			. "LIMIT " . (($page - 1) * $loguser[tpp]) . "," . $loguser[tpp]);

	$forum[threads] = $sql->resultq("SELECT count(*) "
			. "FROM threads t "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON f.cat=c.id "
			. "LEFT JOIN threadthumbs th ON th.tid=t.id "
			. "WHERE th.uid=$loguser[id] "
			. "AND f.id IN " . forums_with_view_perm() . " ");
	$topbot = "<table cellspacing=\"0\" width=100%>
" . "  <td class=\"nb\"><a href=./>Main</a> - Favorite Threads</td>
" . "</table>
";
} else {
	error("Error", "Forum does not exist.<br> <a href=./>Back to main</a>");
}

$showforum = $uid || $time;

//Forum Jump - SquidEmpress
if (!$uid && !$time && !isset($_GET['fav'])) {
	$r = $sql->query("SELECT c.title ctitle,c.private cprivate,f.id,f.title,f.cat,f.private FROM forums f LEFT JOIN categories c ON c.id=f.cat ORDER BY c.ord,c.id,f.ord,f.id");
	$forumjumplinks = "<table><td>$fonttag Forum jump: </td>
        <td><form><select onchange=\"document.location=this.options[this.selectedIndex].value;\">";
	$c = -1;
	while ($d = $sql->fetch($r)) {
		if (!can_view_forum($d))
			continue;

		if ($d['cat'] != $c) {
			if ($c != -1)
				$forumjumplinks .= '</optgroup>';
			$c = $d['cat'];
			$forumjumplinks.= "<optgroup label=\"" . $d['ctitle'] . "\">";
		}
		//Based off of the forum name code in 1.92.08. - SquidEmpress
		$forumjumplinks.="<option value=forum.php?id=$d[id]" . ($forum['id'] == $d['id'] ? ' selected' : '') . ">$d[title]";
	}
	$forumjumplinks.="</optgroup></select></table></form>";
	$forumjumplinks = ($forumjumplinks);
}

if ($forum['threads'] <= $loguser['tpp']) {
	$fpagelist = '<br>';
	$fpagebr = '';
} else {
	$fpagelist = '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">Pages:';
	for ($p = 1; $p <= 1 + floor(($forum[threads] - 1) / $loguser[tpp]); $p++)
		if ($p == $page)
			$fpagelist.=" $p";
		elseif ($fid)
			$fpagelist.=" <a href=forum.php?id=$fid&page=$p>$p</a>";
		elseif ($uid)
			$fpagelist.=" <a href=forum.php?user=$uid&page=$p>$p</a>";
		elseif ($time)
			$fpagelist.=" <a href=forum.php?time=$time&page=$p>$p</a>";
	$fpagelist.='</div>';
	$fpagebr = '<br>';
}

print $topbot;
if ($time) {
	print "<div style=\"margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block\">
          By Threads | <a href=thread.php?time=$time>By Posts</a></div><br>";
	print '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">' .
			timelink(900) . '|' . timelink(3600) . '|' . timelink(86400) . '|' . timelink(604800)
			. "</div>";
}
print "<br>
" . "<table cellspacing=\"0\" class=\"c1\">";

if ($fid) {

	echo announcement_row(0, 3, 4);
	echo announcement_row($fid, 3, 4);
}

print "
" . "  <tr class=\"h\">
" . "    <td class=\"b h\" width=17>&nbsp;</td>
" . "    <td class=\"b h\" width=17>&nbsp;</td>
" . ($showforum ?
				"    <td class=\"b h\">Forum</td>" : '') . "
" . "    <td class=\"b h\">Title</td>
" . "    <td class=\"b h\" width=130>Started by</td>
" . "    <td class=\"b h\" width=50>Replies</td>
" . "    <td class=\"b h\" width=50>Views</td>
" . "    <td class=\"b h\" width=130>Last post</td>
";

$lsticky = 0;
for ($i = 1; $thread = $sql->fetch($threads); $i++) {
	$pagelist = '';
	if ($thread['replies'] >= $loguser['ppp']) {
		for ($p = 1; $p <= ($pmax = (1 + floor($thread['replies'] / $loguser['ppp']))); $p++) {
			if ($loguser['longpages'] || $p < 7 || $p > ($pmax - 7) || !($p % 10))
				$pagelist.=" <a href=thread.php?id=$thread[id]&page=$p>$p</a>";
			else if (substr($pagelist, -1) != ".")
				$pagelist.=" ...";
		}
		$pagelist = " <font class=sfont>(pages: $pagelist)</font>";
	}

	$status = '';
	$statalt = '';
	if ($thread['closed']) {
		$status.='o';
		$statalt = 'OFF';
	}
	if ($thread['replies'] >= 50) {
		$status.='!';
		if (!$statalt)
			$statalt = 'HOT';
	}

	if ($log) {
		if (!$thread['isread']) {
			$status.='n';
			if ($statalt != 'HOT')
				$statalt = 'NEW';
		}
	}else
	if ($thread['lastdate'] > (ctime() - 3600)) {
		$status.='n';
		if ($statalt != 'HOT')
			$statalt = 'NEW';
	}

	if ($status)
		$status = rendernewstatus($status);
	else
		$status = '&nbsp;';

	if (!$thread['title'])
		$thread['title'] = '�';

	if ($thread['icon'])
		$icon = "<img src='$thread[icon]' height=15>";
	else
		$icon = '&nbsp;';

	if ($thread['sticky'])
		$tr = 'n1';
	else
		$tr = ($i % 2 ? 'n2' : 'n3');

	if (!$thread['sticky'] && $lsticky)
		print
				"  <tr class=\"c\">
" . "    <td class=\"b\" colspan=" . ($showforum ? 8 : 7) . " style='font-size:1px'>&nbsp;</td>
";
	$lsticky = $thread['sticky'];

	$taglist = "";
	for ($k = 0; $k < sizeof($tags);  ++$k) {
		$t = $tags[$k];
		if ($thread['tags'] & (1 << $t['bit'])) {
			if ($config['classictags']) {
				list($r, $g, $b) = sscanf($t['color'], "%02X%02X%02X"); //updated to new php syntax, call by reference is now completely removed in PHP
				if ($r < 128 && $g < 128) {
					$r+=32;
					$g+=32;
				}
				$t['color2'] = sprintf("%02X%02X%02X", $r, $g, $b);
				$taglist.=" <span style=\"background-repeat:repeat;background:url('gfx/tpng.php?c=$t[color]&t=105');font-size:7pt;font-family:Small Fonts,sans-serif;padding:1px 1px\">"
						. "<span style=\"background-repeat:repeat;background:url('gfx/tpng.php?c=$t[color]&t=105');font-size:7pt;font-family:Small Fonts,sans-serif;color:$t[color2];padding:2px 3px\" alt=\"$t[name]\">$t[tag]</span></span>";
			} else {
				$taglist.=" <img src=\"./gfx/tags/tag$t[fid]-$t[bit].png\" alt=\"$t[name]\" title=\"$t[name]\" style=\"position: relative; top: 3px;\"/>";
			}
		}
	}

	print "<tr class=\"$tr\" align=\"center\">
" . "    <td class=\"b n1\">$status</td>
" . "    <td class=\"b\">$icon</td>
" . ($showforum ?
					"    <td class=\"b\"><a href=forum.php?id=$thread[fid]>$thread[ftitle]</a></td>" : '') . "
" . "    <td class=\"b\" align=\"left\">" . ($thread['ispoll'] ? "<img src=img/poll.gif height=10>" : "") . (($thread['thumbcount']) ? " (" . $thread['thumbcount'] . ") " : "") . "<a href=thread.php?id=$thread[id]>" . forcewrap(htmlval($thread['title'])) . "</a>$taglist$pagelist</td>
" . "    <td class=\"b\">" . userlink($thread, 'u1', $config['startedbyminipic']) . "</td>
" . "    <td class=\"b\">$thread[replies]</td>
" . "    <td class=\"b\">$thread[views]</td>
" . "    <td class=\"b\"><nobr>" . cdate($dateformat, $thread['lastdate']) . "</nobr><br><font class=sfont>by&nbsp;" . userlink($thread, 'u2', $config['forumminipic']) . "&nbsp;<a href='thread.php?pid=$thread[lastid]#$thread[lastid]'>&raquo;</a></font></td>
";
}
print "</table>
" . "$forumjumplinks$fpagelist$fpagebr
" . "$topbot
";
pagefooter();
?>
