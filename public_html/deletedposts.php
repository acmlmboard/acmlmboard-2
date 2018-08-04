<?php
	$nourltracker = 1;
	require "lib/common.php";
	
	if (!has_perm('track-deleted-posts'))
		error("Error", "You aren't allowed to do this.");
	
	if (isset($_POST['submit'])) {
		check_token($_POST['auth'], "undel");
		
		if (isset($_POST['undel']) && is_array($_POST['undel'])) {
			//--
			// The count of deleted posts must match on both ends
			// Otherwise, assume tampering.
			$_POST['undel'] = array_map('intval', array_keys($_POST['undel']));
			$idlist = implode(',', $_POST['undel']);
			$validcount = $sql->resultq("
				SELECT COUNT(*)
				FROM posts p
				LEFT JOIN threads t ON p.thread = t.id
				WHERE p.deleted = 1 
				  AND t.forum IN ".forums_with_edit_posts_perm()." 
				  AND p.id IN ({$idlist})
				ORDER BY p.id DESC
			");
			if (count($_POST['undel']) != $validcount) {
				error("Error", "No.");
			}
			//--
			$sql->query("UPDATE posts SET deleted = 0 WHERE id IN ({$idlist})");
		}
		redirect("?{$_SERVER['QUERY_STRING']}", "{$validcount} post".($validcount != 1 ? "s have" : " has")." been undeleted.", "Message", "the Undelete utility");
	}
	
	
	pageheader("Deleted posts");
	
	$_GET['u']    = isset($_GET['u'])    ? (int)$_GET['u']    : 0;
	$_GET['t']    = isset($_GET['t'])    ? (int)$_GET['t']    : 0;
	$_GET['page'] = isset($_GET['page']) ? (int)$_GET['page'] : 1;
				
	$where = "WHERE p.deleted = 1 AND t.forum IN ".forums_with_edit_posts_perm();
	if ($_GET['u']) $where .= "AND p.user   = {$_GET['u']} ";
	if ($_GET['t']) $where .= "AND p.thread = {$_GET['t']} ";	
	
	$total = $sql->resultq("
		SELECT COUNT(*) 
		FROM posts p 
		LEFT JOIN threads t ON p.thread = t.id
		{$where}
	");
	$limit = 50;
	$min = ($_GET['page']-1) * $limit;
	
	
	$posts = $sql->query("
		SELECT p.id, p.date, t.id tid, t.title ttitle, f.id fid, f.title ftitle, 
		       ".userfields('u1','u1').", ".userfields('u2','u2')."
		FROM posts p
		LEFT JOIN threads  t ON p.thread =  t.id
		LEFT JOIN forums   f ON t.forum  =  f.id
		LEFT JOIN users   u1 ON p.user   = u1.id
		LEFT JOIN users   u2 ON t.user   = u2.id
		{$where}
		ORDER BY p.id DESC
		LIMIT {$min},{$limit}
	");
	$pagectrl = pagelist($total, $limit, "?u={$_GET['u']}&t={$_GET['t']}", $_GET['page']);
	

	print "
	<form method='GET' action='?'>
	$L[TBL1]>
		$L[TRh]>$L[TDh] colspan='2'><b>Controls</b></td></tr>
		$L[TR]>
			$L[TD1c]><b>User:</b></td>
			$L[TD2]>".user_select('u', $_GET['u'])."</td>
		</tr>
		$L[TR]>
			$L[TD1c]><b>Thread ID:</b></td>
			$L[TD2]>$L[INPt]='t' style='width: 100px' value='{$_GET['t']}'></td>
		</tr>
		$L[TR]>
			$L[TD1c] style='width: 150px'>&nbsp;</td>
			$L[TD2]>$L[INPs] value='Search'></td>
		</tr>
	</table>
	</form>
	<br>
	{$pagectrl}
	<form method='POST' action='?{$_SERVER['QUERY_STRING']}'>
	$L[TBL1]>
		$L[TRh]>$L[TDh] colspan='5'>Deleted posts".($_GET['t'] ? " in thread <b>#{$_GET['t']}</b>" : "").($_GET['u'] ? " by user <b>#{$_GET['u']}</b>" : "")."</td></tr>
		$L[TRg]>
			$L[TD1c] style='width: 70px'>#</td>
			$L[TD1c] style='width: 200px'>Posted by</td>
			$L[TD1c]>Thread</td>
			$L[TD1c] style='width: 250px'>Forum</td>
			$L[TD1c] style='width: 150px'>Date</td>
		</tr>";
		
	while ($x = $sql->fetch($posts)) {
		print "
		$L[TR]>
			$L[TD1]>$L[INPc]='undel[{$x['id']}]' value=1> - <a href='thread.php?pid={$x['id']}&pin={$x['id']}#{$x['id']}'>{$x['id']}</a></td>
			$L[TD1c]>".userlink($x, "u1")."</td>
			$L[TD1]>
				<a href='thread.php?id={$x['tid']}'>".htmlspecialchars($x['ttitle'])."</a>
				<div class='sfont'>by ".userlink($x, "u2")."</div>
			</td>
			$L[TD1c]><a href='thread.php?id={$x['fid']}'>".htmlspecialchars($x['ftitle'])."</a></td>
			$L[TD1c]>".cdate($dateformat, $x['date'])."</td>
		</tr>";
	}
		
		
	print "
		$L[TRg]>
			$L[TD1c] colspan='5'>
				<span style='float:left'>$L[INPs]='submit' value='Undelete selected'></span>
				<span style='vertical-align:middle'>{$total} deleted post".($total != 1 ? "s" : "")." found</span>
			</td>
		</tr>
	</table>
	".auth_tag("undel")."
	</form>
	{$pagectrl}";
	
	pagefooter();


function user_select($name, $sel = 0) {
	global $sql;
	$userlist = "";
	$users = $sql->query("SELECT `id`, IF(`displayname` != '', `displayname`, `name`) `name` FROM `users` ORDER BY `name`");
	while ($x = $sql->fetch($users)) {
		$selected = ($x['id'] == $sel) ? " selected" : "";
		$userlist .= "<option value='{$x['id']}'{$selected}>{$x['name']}</option>\r\n";
	}
	return "
	<select name='{$name}' size='1'>
		<option value='0'>Select a user...</option>
		{$userlist}
	</select>";
}