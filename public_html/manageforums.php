<?php
require('lib/common.php');

if (!has_perm('edit-forums')) 
{
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
}


if ($_GET['ajax'])
{
	$ajax = $_GET['ajax'];
	if ($ajax == 'localmodRow')
	{
		$user = $sql->fetchp("SELECT ".userfields()." FROM users WHERE name=? OR displayname=?",array($_GET['user'],$_GET['user']));
		if (!$user) die();
		print $user['id'].'|'.localmodRow($user);
	}
	else if ($ajax == 'renderTag')
	{
		renderTag($_GET['text'], null, -1, $_GET['color']);
	}
	else if ($ajax == 'tagRow')
	{
		if (!trim($_GET['text']) || !trim($_GET['tag']) || !trim($_GET['color'])) die();
		print tagRow($_GET['text'], $_GET['tag'], null, (int)$_GET['bit'], $_GET['color']);
	}
	
	die();
}


$error = '';

if ($_POST['savecat'])
{
	// save new/existing category
	
	$cid = $_GET['cid'];
	$title = stripslashes($_POST['title']);
	$ord = (int)$_POST['ord'];
	$private = $_POST['private'] ? 1:0;
	
	if (!trim($title))
		$error = 'Please enter a title for the category.';
	else
	{
		if ($cid == 'new')
		{
			$cid = $sql->resultq("SELECT MAX(id) FROM categories");
			if (!$cid) $cid = 0;
			$cid++;
			
			$sql->prepare("INSERT INTO categories (id,title,ord,private) VALUES (?,?,?,?)", array($cid, $title, $ord, $private));
		}
		else
		{
			$cid = (int)$cid;
			if (!$sql->resultp("SELECT COUNT(*) FROM categories WHERE id=?",array($cid)))
				die(header('Location: manageforums.php'));
			
			$sql->prepare("UPDATE categories SET title=?, ord=?, private=? WHERE id=?", array($title, $ord, $private, $cid));
		}
		
		saveperms('categories', $cid);
		
		die(header('Location: manageforums.php?cid='.$cid));
	}
}
else if ($_POST['delcat'])
{
	// delete category
	
	$cid = (int)$_GET['cid'];
	$sql->prepare("DELETE FROM categories WHERE id=?",array($cid));
	
	deleteperms('categories', $cid);
	die(header('Location: manageforums.php'));
}
else if ($_POST['saveforum'])
{
	// save new/existing forum

	$fid = $_GET['fid'];
	$cat = (int)$_POST['cat'];
	$title = stripslashes($_POST['title']);
	$descr = stripslashes($_POST['descr']);
	$ord = (int)$_POST['ord'];
	$private = $_POST['private'] ? 1:0;
	$trash = $_POST['trash'] ? 1:0;
	$readonly = $_POST['readonly'] ? 1:0;
	$announcechan_id = (int)$_POST['announcechan_id'];
	
	if (!trim($title))
		$error = 'Please enter a title for the forum.';
	else
	{
		if ($fid == 'new')
		{
			$fid = $sql->resultq("SELECT MAX(id) FROM forums");
			if (!$fid) $fid = 0;
			$fid++;
			
			$sql->prepare("INSERT INTO forums (id,cat,title,descr,ord,private,trash,readonly,announcechan_id) VALUES (?,?,?,?,?,?,?,?,?)", 
				array($fid, $cat, $title, $descr, $ord, $private, $trash, $readonly, $announcechan_id));
		}
		else
		{
			$fid = (int)$fid;
			if (!$sql->resultp("SELECT COUNT(*) FROM forums WHERE id=?",array($fid)))
				die(header('Location: manageforums.php'));
			
			$sql->prepare("UPDATE forums SET cat=?, title=?, descr=?, ord=?, private=?, trash=?, readonly=?, announcechan_id=? WHERE id=?", 
				array($cat, $title, $descr, $ord, $private, $trash, $readonly, $announcechan_id, $fid));
		}
		
		// save localmods
		
		$oldmods = array();
		$qmods = $sql->prepare("SELECT uid FROM forummods WHERE fid=?",array($fid));
		while ($mod = $sql->fetch($qmods))
			$oldmods[$mod['uid']] = 1;
		
		$newmods = $_POST['localmod'];
		
		foreach ($oldmods as $uid=>$blarg)
		{
			if (!$newmods[$uid])
				$sql->prepare("DELETE FROM forummods WHERE fid=? AND uid=?", array($fid, $uid));
		}
		foreach ($newmods as $uid=>$blarg)
		{
			if (!$oldmods[$uid])
				$sql->prepare("INSERT INTO forummods (fid,uid) VALUES (?,?)", array($fid, $uid));
		}
		
		// save tags
		
		$oldtags = array();
		$qtags = $sql->prepare("SELECT bit,tag,color FROM tags WHERE fid=?",array($fid));
		while ($tag = $sql->fetch($qtags))
			$oldtags[$tag['bit']] = $tag;
		
		$newtags = $_POST['tag'];

		foreach ($oldtags as $rbit=>$blarg)
		{
			$bit = (int)$rbit;
			if (!$newtags[$bit])
				$sql->prepare("DELETE FROM tags WHERE fid=? AND bit=?", array($fid, $bit));
		}
		foreach ($newtags as $rbit=>$rdata)
		{
			$bit = (int)$rbit;
			$data = explode('|', $rdata);
			$name = rawurldecode($data[0]);
			$tag = rawurldecode($data[1]);
			$color = rawurldecode($data[2]);
			
			if ($oldtags[$bit])
				$sql->prepare("UPDATE tags SET name=?, tag=?, color=? WHERE fid=? AND bit=?", array($name, $tag, $color, $fid, $bit));
			else
				$sql->prepare("INSERT INTO tags (bit,fid,name,tag,color) VALUES (?,?,?,?,?)", array($bit, $fid, $name, $tag, $color));
			
			// create the new tag image if needed
			if (!$oldtags[$bit] || $oldtags[$bit]['tag'] != $tag || $oldtags[$bit]['color'] != $color)
				renderTag($tag, $fid, $bit, $color);
		}
		
		saveperms('forums', $fid);
		
		die(header('Location: manageforums.php?fid='.$fid));
	}
}
else if ($_POST['delforum'])
{
	// delete forum
	
	$fid = (int)$_GET['fid'];
	$sql->prepare("DELETE FROM forums WHERE id=?",array($fid));
	$sql->prepare("DELETE FROM forummods WHERE fid=?",array($fid));
	$sql->prepare("DELETE FROM tags WHERE fid=?",array($fid));
	
	deleteperms('forums', $fid);
	die(header('Location: manageforums.php'));
}
else if ($_POST['savechan'])
{
	// save new/existing channel
	
	$chanid = $_GET['chanid'];
	$channame = stripslashes($_POST['channame']);
	
	if (!trim($channame))
		$error = 'Please enter a name for the channel.';
	else
	{
		if ($chanid == 'new')
		{
			$chanid = $sql->resultq("SELECT MAX(id) FROM announcechans");
			if (!$chanid) $chanid = 0;
			$chanid++;
			
			$sql->prepare("INSERT INTO announcechans (id,chan) VALUES (?,?)", array($chanid, $channame));
		}
		else
		{
			$chanid = (int)$chanid;
			if (!$sql->resultp("SELECT COUNT(*) FROM announcechans WHERE id=?",array($chanid)))
				die(header('Location: manageforums.php'));
			
			$sql->prepare("UPDATE announcechans SET chan=? WHERE id=?", array($channame, $chanid));
		}
		
		die(header('Location: manageforums.php?chanid='.$chanid));
	}
}
else if ($_POST['delchan'])
{
	// delete channel
	
	$chanid = (int)$_GET['chanid'];
        $sql->prepare("UPDATE forums SET announcechan_id=? WHERE announcechan_id=?", array('0', $chanid));
	$sql->prepare("DELETE FROM announcechans WHERE id=?",array($chanid));
	
	die(header('Location: manageforums.php'));
}


pageheader('Forum management');

?>
<script type="text/javascript" src="manageforums.js"></script>
<script type="text/javascript" src="jscolor/jscolor.js"></script>
<style type="text/css">label { white-space: nowrap; } input:disabled { opacity: 0.5; }</style>
<?php

if ($error)
{
        noticemsg("Error", $error);
}

if ($cid = $_GET['cid'])
{
	// category editor
	
	if ($cid == 'new')
	{
		$cat = array('id' => 0, 'title' => '', 'ord' => 0, 'private' => 0);
	}
	else
	{
		$cid = (int)$cid;
		$cat = $sql->fetchp("SELECT * FROM categories WHERE id=?",array($cid));
	}
	
	print 	"<form action=\"\" method=\"POST\">
".			"	<table cellspacing=\"0\" class=\"c1\">
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>".($cid=='new' ? 'Create':'Edit')." category</td></tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">Title:</td>
".			"			<td class=\"b n2\"><input type=\"text\" name=\"title\" value=\"".htmlspecialchars($cat['title'])."\" size=50 maxlength=500></td>
".			"		</tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">Display order:</td>
".			"			<td class=\"b n2\"><input type=\"text\" name=\"ord\" value=\"{$cat['ord']}\" size=4 maxlength=10></td>
".			"		</tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">&nbsp;</td>
".			"			<td class=\"b n2\"><label><input type=\"checkbox\" name=\"private\" value=1".($cat['private'] ? ' checked="checked"':'')."> Private category</label></td>
".			"		</tr>
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>&nbsp;</td></tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">&nbsp;</td>
".			"			<td class=\"b n2\">
".			"				<input type=\"submit\" class=\"submit\" name=\"savecat\" value=\"Save category\"> ".($cid=='new' ? '':"
".			"				<input type=\"submit\" class=\"submit\" name=\"delcat\" value=\"Delete category\" onclick=\"if (!confirm('Really delete this category?')) return false;\"> ")."
".			"				<button type=\"button\" class=\"submit\" id=\"back\" onclick=\"window.location='manageforums.php';\">Back</button>
".			"			</td>
".			"		</tr>
".			"	</table>
".			"	<br>
";
	
	permtable('categories', $cid);
		
	print 	"</form>
";
}
else if ($fid = $_GET['fid'])
{
	// forum editor
	
	if ($fid == 'new')
	{
		$forum = array('id' => 0, 'cat' => 1, 'title' => '', 'descr' => '', 'ord' => 0, 'private' => 0, 'trash' => 0, 'readonly' => 0, 'announcechan_id' => 0);
	}
	else
	{
		$fid = (int)$fid;
		$forum = $sql->fetchp("SELECT * FROM forums WHERE id=?",array($fid));
	}
	
	$qcats = $sql->query("SELECT id,title FROM categories ORDER BY ord, id");
	$cats = array();
	while ($cat = $sql->fetch($qcats))
		$cats[$cat['id']] = $cat['title'];
	$catlist = fieldselect('cat', $forum['cat'], $cats);
	
	$qchans = $sql->query("SELECT id,chan FROM announcechans ORDER BY id");
	$chans = array(0 => 'Default');
	while ($chan = $sql->fetch($qchans))
		$chans[$chan['id']] = $chan['chan'];
	$chanlist = fieldselect('announcechan_id', $forum['announcechan_id'], $chans);
	
	print 	"<form action=\"\" method=\"POST\">
".			"	<table cellspacing=\"0\" class=\"c1\">
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>".($fid=='new' ? 'Create':'Edit')." forum</td></tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">Title:</td>
".			"			<td class=\"b n2\"><input type=\"text\" name=\"title\" value=\"".htmlspecialchars($forum['title'])."\" size=50 maxlength=500></td>
".			"		</tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">Description:<br><small>HTML allowed.</small></td>
".			"			<td class=\"b n2\"><textarea wrap=\"virtual\" name=\"descr\" rows=3 cols=50>".htmlspecialchars($forum['descr'])."</textarea></td>
".			"		</tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">Category:</td>
".			"			<td class=\"b n2\">{$catlist}</td>
".			"		</tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">Display order:</td>
".			"			<td class=\"b n2\"><input type=\"text\" name=\"ord\" value=\"{$forum['ord']}\" size=4 maxlength=10></td>
".			"		</tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">Report to IRC channel:<br><small>Leave this to default if you don't use IRC reporting.</small></td>
".			"			<td class=\"b n2\">{$chanlist}</td>
".			"		</tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">&nbsp;</td>
".			"			<td class=\"b n2\">
".			"				<label><input type=\"checkbox\" name=\"private\" value=1".($forum['private'] ? ' checked="checked"':'')."> Private forum</label>
".			"				<label><input type=\"checkbox\" name=\"readonly\" value=1".($forum['readonly'] ? ' checked="checked"':'')."> Read-only</label>
".			"				<label><input type=\"checkbox\" name=\"trash\" value=1".($forum['trash'] ? ' checked="checked"':'')."> Trash forum</label>
".			"			</td>
".			"		</tr>
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>&nbsp;</td></tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">&nbsp;</td>
".			"			<td class=\"b n2\">
".			"				<input type=\"submit\" class=\"submit\" name=\"saveforum\" value=\"Save forum\"> ".($fid=='new' ? '':"
".			"				<input type=\"submit\" class=\"submit\" name=\"delforum\" value=\"Delete forum\" onclick=\"if (!confirm('Really delete this forum?')) return false;\"> ")."
".			"				<button type=\"button\" class=\"submit\" id=\"back\" onclick=\"window.location='manageforums.php';\">Back</button>
".			"			</td>
".			"		</tr>
".			"	</table>
".			"	<br>
";
	
	permtable('forums', $fid);

	// localmods
	
	print 	"	<br>
".			"	<table cellspacing=\"0\" class=\"c1\">
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>Moderators</td></tr>
".			"		<tr class=\"c\"><td class=\"b c\">Add a moderator</td><td class=\"b c\">Current moderators</td></tr>
".			"		<tr class=\"n2\">
".			"			<td class=\"b\" style=\"width:50%; vertical-align:top;\">
".			"				<input type=\"text\" name=\"addmod_name\" id=\"addmod_name\" size=20 maxlength=32 onkeyup=\"localmodSearch(this);\"> <button type=\"button\" class=\"submit\" id=\"addmod\" onclick=\"addLocalmod();\">Add</button><br>
".			"				<select name=\"addmod_list\" id=\"addmod_list\" style=\"width:200px;\" size=5 onchange=\"chooseLocalmod(this);\"></select>
".			"			</td>
".			"			<td class=\"b\" id=\"modlist\" style=\"vertical-align:top;\">
";
	
	$qmods = $sql->prepare("SELECT ".userfields('u')." FROM forummods f LEFT JOIN users u ON u.id=f.uid WHERE f.fid=?",array($fid));
	while ($mod = $sql->fetch($qmods))
		print "<div>".localmodRow($mod)."</div>";
		
	print 	"			</td>
".			"		</tr>
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>&nbsp;</td></tr>
".			"		<tr>
".			"			<td class=\"b n2\" align=\"center\" colspan=2>
".			"				<input type=\"submit\" class=\"submit\" name=\"saveforum\" value=\"Save forum\">
".			"			</td>
".			"		</tr>
".			"	</table>
".			"	<br>
";
	
	// tags

	print 	"	<table cellspacing=\"0\" class=\"c1\">
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>Thread tags</td></tr>
".			"		<tr class=\"c\"><td class=\"b c\">Add a tag</td><td class=\"b c\">Current tags</td></tr>
".			"		<tr class=\"n2\">
".			"			<td class=\"b\" style=\"width:50%; vertical-align:top;\">
".			"				Name: <input type=\"text\" name=\"tag_name\" id=\"tag_name\" size=20 maxlength=64><br>
".			"				Tag text: <input type=\"text\" name=\"tag_tag\" id=\"tag_tag\" size=10 maxlength=20><br>
".			"				Color: <input class=\"color {pickerFaceColor:'black',pickerBorder:0,pickerInsetColor:'black'}\" value=\"808080\" name=\"tag_color\" id=\"tag_color\" size=6 maxlength=6><br>
".			"				<button type=\"button\" class=\"submit\" id=\"newtag\" onclick=\"newTag();\">New tag</button> <button type=\"button\" class=\"submit\" id=\"savetag\" onclick=\"saveTag('{$fid}');\">Save tag</button>
".			"			</td>
".			"			<td class=\"b\" id=\"taglist\" style=\"vertical-align:top;\">
";

	$qtags = $sql->prepare("SELECT * FROM tags WHERE fid=?",array($fid));
	while ($tag = $sql->fetch($qtags))
		print "<div>".tagRow($tag['name'], $tag['tag'], $fid, $tag['bit'], $tag['color'])."</div>";
	
	print 	"			</td>
".			"		</tr>
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>&nbsp;</td></tr>
".			"		<tr>
".			"			<td class=\"b n2\" align=\"center\" colspan=2>
".			"				<input type=\"submit\" class=\"submit\" name=\"saveforum\" value=\"Save forum\">
".			"			</td>
".			"		</tr>
".			"	</table>
";
	
	print 	"</form>
";
}
else if ($chanid = $_GET['chanid'])
{
	// channel editor
	
	if ($chanid == 'new')
	{
		$chan = array('id' => 0, 'chan' => '');
	}
	else
	{
		$chanid = (int)$chanid;
		$chan = $sql->fetchp("SELECT * FROM announcechans WHERE id=?",array($chanid));
	}
	
	print 	"<form action=\"\" method=\"POST\">
".			"	<table cellspacing=\"0\" class=\"c1\">
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>".($chanid=='new' ? 'Create':'Edit')." channel</td></tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">Name:</td>
".			"			<td class=\"b n2\"><input type=\"text\" name=\"channame\" value=\"".htmlspecialchars($chan['chan'])."\" size=50 maxlength=500></td>
".			"		</tr>
".			"		<tr class=\"h\"><td class=\"b h\" colspan=2>&nbsp;</td></tr>
".			"		<tr>
".			"			<td class=\"b n1\" align=\"center\">&nbsp;</td>
".			"			<td class=\"b n2\">
".			"				<input type=\"submit\" class=\"submit\" name=\"savechan\" value=\"Save channel\"> ".($chanid=='new' ? '':"
".			"				<input type=\"submit\" class=\"submit\" name=\"delchan\" value=\"Delete channel\" onclick=\"if (!confirm('Really delete this channel?')) return false;\"> ")."
".			"				<button type=\"button\" class=\"submit\" id=\"back\" onclick=\"window.location='manageforums.php';\">Back</button>
".			"			</td>
".			"		</tr>
".			"	</table>
".                      "</form>
";
}
else
{
	// main page -- category/forum listing
	
	$qcats = $sql->query("SELECT id,title FROM categories ORDER BY ord, id");
	$cats = array();
	while ($cat = $sql->fetch($qcats))
		$cats[$cat['id']] = $cat;
	
	$qforums = $sql->query("SELECT f.id,f.title,f.cat FROM forums f LEFT JOIN categories c ON c.id=f.cat ORDER BY c.ord, c.id, f.ord, f.id");
	$forums = array();
	while ($forum = $sql->fetch($qforums))
		$forums[$forum['id']] = $forum;

	$qchans = $sql->query("SELECT id,chan FROM announcechans ORDER BY id");
	$chans = array();
	while ($chan = $sql->fetch($qchans))
		$chans[$chan['id']] = $chan;
	
	$catlist = ''; $c = 1;
	foreach ($cats as $cat)
	{
		$catlist .= "<tr><td class=\"b n$c\"><a href=\"?cid={$cat['id']}\">{$cat['title']}</a></td></tr>";
		$c = ($c == 1) ? 2 : 1;
	}
	
	$forumlist = ''; $c = 1; $lc = -1;
	foreach ($forums as $forum)
	{
		if ($forum['cat'] != $lc)
		{
			$lc = $forum['cat'];
			$forumlist .= "<tr class=\"c\"><td class=\"b c\">{$cats[$forum['cat']]['title']}</td></tr>";
		}
		$forumlist .= "<tr><td class=\"b n$c\"><a href=\"?fid={$forum['id']}\">{$forum['title']}</a></td></tr>";
		$c = ($c==1) ? 2:1;
	}

	$chanlist = ''; $c = 1;
	foreach ($chans as $chan)
	{
		$chanlist .= "<tr><td class=\"b n$c\"><a href=\"?chanid={$chan['id']}\">{$chan['chan']}</a></td></tr>";
		$c = ($c==1) ? 2:1;
	}
	
	print 	"<table cellspacing=\"0\" style=\"width:100%;\"><tr>
".			"	<td class=\"b\" style=\"width:33.33%; vertical-align:top; padding-right:0.5em;\">
".			"		<table cellspacing=\"0\" class=\"c1\">
".			"			<tr class=\"h\"><td class=\"b h\">Categories</td></tr>
".			"			$catlist
".			"			<tr class=\"h\"><td class=\"b h\">&nbsp;</td></tr>
".			"			<tr><td class=\"b n1\"><a href=\"?cid=new\">New category</a></td></tr>
".			"		</table>
".			"	</td>
".			"	<td class=\"b\" style=\"width:33.33%; vertical-align:top; padding-left:0.5em; padding-right:0.5em;\">
".			"		<table cellspacing=\"0\" class=\"c1\">
".			"			<tr class=\"h\"><td class=\"b h\">Forums</td></tr>
".			"			$forumlist
".			"			<tr class=\"h\"><td class=\"b h\">&nbsp;</td></tr>
".			"			<tr><td class=\"b n1\"><a href=\"?fid=new\">New forum</a></td></tr>
".			"		</table>
".			"	</td>
".			"	<td class=\"b\" style=\"width:33.33%; vertical-align:top; padding-left:0.5em;\">
".			"		<table cellspacing=\"0\" class=\"c1\">
".			"			<tr class=\"h\"><td class=\"b h\">Channels</td></tr>
".			"			$chanlist
".			"			<tr class=\"h\"><td class=\"b h\">&nbsp;</td></tr>
".			"			<tr><td class=\"b n1\"><a href=\"?chanid=new\">New channel</a></td></tr>
".			"		</table>
".			"	</td>
".			"</tr></table>
";
}

pagefooter();


function rec_grouplist($parent, $level, $tgroups, $groups)
{
	$total = count($tgroups);
	foreach ($tgroups as $g)
	{
		if ($g['inherit_group_id'] != $parent)
			continue;
		
		$g['indent'] = $level;
		$groups[] = $g;
		
		$groups = rec_grouplist($g['id'], $level+1, $tgroups, $groups);
	}
	
	return $groups;
}
function grouplist()
{
	global $sql, $usergroups;
	
	$groups = array();
	$groups = rec_grouplist(0, 0, $usergroups, $groups);
	
	return $groups;
}

function permtable($bind, $id)
{
	global $sql;
	
	$qperms = $sql->prepare("SELECT id,title FROM perm WHERE permbind_id=?",array($bind));
	$perms = array(); 
	while ($perm = $sql->fetch($qperms))
		$perms[$perm['id']] = $perm['title'];
	
	$groups = grouplist();
	
	$qpermdata = $sql->prepare("SELECT x.x_id,x.perm_id,x.revoke FROM x_perm x LEFT JOIN perm p ON p.id=x.perm_id WHERE x.x_type=? AND p.permbind_id=? AND x.bindvalue=?",
		array('group',$bind,$id));
	$permdata = array();
	while ($perm = $sql->fetch($qpermdata))
		$permdata[$perm['x_id']][$perm['perm_id']] = !$perm['revoke'];
		
	print 	"<table cellspacing=\"0\" class=\"c1\">
".			"	<tr class=\"h\"><td class=\"b h\">Group</td><td class=\"b h\" colspan=2>Permissions</td></tr>
";
	
	$c = 1;
	foreach ($groups as $group)
	{
		$gid = $group['id'];
		$gtitle = htmlspecialchars($group['title']);
		
		$pf = $group['primary'] ? '<strong' : '<span';
		if ($group['nc2']) $pf .= ' style="color: #'.htmlspecialchars($group['nc2']).';"';
		$pf .= '>';
		$sf = $group['primary'] ? '</strong>' : '</span>';
		$gtitle = "{$pf}{$gtitle}{$sf}";
		
		$doinherit = false;
		$inherit = '';
		if ($group['inherit_group_id'])
		{
			$doinherit = !isset($permdata[$gid]) || empty($permdata[$gid]);
			
			$check = $doinherit ? ' checked="checked"':'';
			$inherit = "<label><input type=\"checkbox\" name=\"inherit[{$gid}]\" value=1 onclick=\"toggleAll('perm_{$gid}',!this.checked);\"{$check}> Inherit from parent</label>&nbsp;";
		}
		
		$permlist = '';
		foreach ($perms as $pid => $ptitle)
		{
			if ($doinherit) $check = ' disabled="disabled"';
			else $check = $permdata[$gid][$pid] ? ' checked="checked"':'';
			
			$permlist .= "<label><input type=\"checkbox\" name=\"perm[{$gid}][{$pid}]\" value=1 class=\"perm_{$gid}\"{$check}> {$ptitle}</label> ";
		}
		
		print 	"	{$L['TR'.$c]}>
".				"		<td class=\"b\" style=\"width:200px;\"><span style=\"white-space:nowrap;\">".str_repeat('&nbsp; &nbsp; ', $group['indent']).$gtitle."</span></td>
".				"		<td class=\"b\" style=\"width:100px;\">{$inherit}</td>
".				"		<td class=\"b\">{$permlist}</td>
".				"	</tr>
".				"	<tr>
".				"		<td class=\"b n3\" colspan=3 style=\"height:4px;\"></td>
".				"	</tr>
";
		
		$c = ($c==1) ? 2:1;
	}
	
	print 	"	{$L['TR'.$c]}>
".			"		<td class=\"b\">&nbsp;</td>
".			"		<td class=\"b\" colspan=2>
".			"			<input type=\"submit\" class=\"submit\" name=\"save".($bind=='forums' ? 'forum':'cat')."\" value=\"Save ".($bind=='forums' ? 'forum':'category')."\">
".			"		</td>
".			"	</tr>
".			"</table>
";
}


function deleteperms($bind, $id)
{
	global $sql;
	
	$sql->prepare("DELETE x FROM x_perm x LEFT JOIN perm p ON p.id=x.perm_id WHERE x.x_type=? AND p.permbind_id=? AND x.bindvalue=?",
		array('group', $bind, $id));
}

function saveperms($bind, $id)
{
	global $sql, $usergroups;
	
	$qperms = $sql->prepare("SELECT id FROM perm WHERE permbind_id=?",array($bind));
	$perms = array(); 
	while ($perm = $sql->fetch($qperms))
		$perms[] = $perm['id'];
	
	// delete the old perms
	deleteperms($bind, $id);
	
	// apply the new perms
	foreach ($usergroups as $gid=>$group)
	{
		if ($_POST['inherit'][$gid])
			continue;
			
		$myperms = $_POST['perm'][$gid];
		foreach ($perms as $perm)
			$sql->prepare("INSERT INTO `x_perm` (`x_id`,`x_type`,`perm_id`,`permbind_id`,`bindvalue`,`revoke`)
				VALUES (?,?,?,?,?,?)", array($gid, 'group', $perm, $bind, $id, $myperms[$perm]?0:1));
	}
}


function localmodRow($user)
{
	return "<span style=\"min-width:200px; display:inline-block;\">".userlink($user)."</span>".
		"<button class=\"submit\" onclick=\"deleteLocalmod(this.parentNode); return false;\">Remove</button>".
		"<input type=\"hidden\" name=\"localmod[{$user['id']}]\" id=\"localmod_{$user['id']}\" value=1>";
}


function renderTag($TagText, $ForumID, $TagBit, $TintColour)
{
	$TagTextImage = RenderText($TagText);
	$Tag = Image::Create($TagTextImage->Size[0] + 11, 16);

	$LeftImage = Image::LoadPNG("./gfx/tagleft.png");
	$RightImage = Image::LoadPNG("./gfx/tagright.png");
	$Tag->DrawImageDirect($LeftImage, 0, 0);
	
	for ($X = 7; $X < $Tag->Size[0] - 7; $X += 4)
		$Tag->DrawImageDirect($RightImage, $X, 0);

	$Tag->DrawImageDirect($RightImage, $Tag->Size[0] - 8, 0);
	$Tag->Colourize(hexdec(substr($TintColour, 0, 2)), hexdec(substr($TintColour, 2, 2)), hexdec(substr($TintColour, 4, 2)), 0xFF);

	$Tag->DrawImageDirect($TagTextImage, 8, 2);
	
	if ($ForumID === null)
		$Tag->OutputPNG();
	else
		$Tag->SavePNG("./gfx/tags/tag$ForumID-$TagBit.png");

	$LeftImage->Dispose();
	$RightImage->Dispose();
	$Tag->Dispose();
	$TagTextImage->Dispose();
}

function tagRow($text, $tag, $fid, $bit, $color)
{
	$tagdata = rawurlencode($text).'|'.rawurlencode($tag).'|'.rawurlencode($color);
	if ($bit >= 0) $tagdata .= '|'.$bit;
	
	$imgfile = "./gfx/tags/tag$fid-$bit.png";
	if ($fid === null || !file_exists($imgfile))
		$imgfile = "manageforums.php?ajax=renderTag&amp;text=$tag&amp;color=$color";
	$imgtag = "<img src=\"{$imgfile}\" alt=\"".htmlspecialchars($tag)."\" style=\"vertical-align:bottom;\">";
	
	return "<span style=\"min-width:200px; display:inline-block;\">".htmlspecialchars($text)."&nbsp;{$imgtag}</span>".
		"<button class=\"submit\" onclick=\"editTag({$bit}); return false;\">Edit</button>".
		"<button class=\"submit\" onclick=\"deleteTag({$bit},this.parentNode); return false;\">Remove</button>".
		"<input type=\"hidden\" name=\"tag[{$bit}]\" id=\"tag_{$bit}\" value=\"".htmlspecialchars($tagdata)."\">";
}

?>