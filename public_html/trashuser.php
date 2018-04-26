<?php
	$nourltracker = 1;
	require 'lib/common.php';
	
	needs_login(1);
	
	if (!has_perm('trash-users'))
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

	const TOKEN_USERDEL = 30;
	const SHOW_ALL    = -20;
	const SHOW_BANNED = -10;
	
	$bannedgroups = $sql->getresultsbykey("SELECT id, title FROM `group` WHERE banned = 1", 'id', 'title'); // Can be more than one apparently
	
	pageheader();
	
	// Explicitly check if the 'dotrash' button was pressed
	// Since the forms have been merged to preserve sort options between deletions
	if (isset($_POST['dotrash']) && isset($_POST['banuser']) && is_array($_POST['banuser'])) {
		check_token($_POST['auth'], TOKEN_USERDEL);
		
		if (!isset($bannedgroups[$_POST['destgroup']])) {
			noticemsg("Nice try", "This is not a banned group.");
			pagefooter();
			die;
		}
		
		$banlist      = array_keys($_POST['banuser']);
		$banusercnt   = 0;
		$banusertext  = "";
		
		foreach($banlist as $id) {
			$id = (int) $id;
			
			if (!can_edit_user($id) || $id == $loguser['id']) // Prevent trashing users you can't delete
				continue;
			
			$user = $sql->fetchq("SELECT ".userfields('u')." FROM users u WHERE u.id = $id");

			// Mark all posts as deleted
			$delposts = $sql->resultq("SELECT COUNT(*) FROM posts WHERE user = {$id} AND deleted = 0");
			$posts = $sql->query("UPDATE posts SET deleted = 1 WHERE user = {$id}");
			// Trash all forums
			$threads = $sql->query("SELECT id FROM threads WHERE user = {$id} AND forum != {$trashid}");
			$delthreads = $sql->numrows($threads);
			while ($x = $sql->fetch($threads)) {
				editthread($x['id'],'',$trashid,'',1);
			}
			// Deliver the ban
			$sql->query("UPDATE users SET group_id = {$_POST['destgroup']} WHERE id = {$id}");
			
			$banusertext .= "$L[TR1]>$L[TD1c] style='width: 120px'>{$id}</td>$L[TD2]>".userlink($user)." (deleted {$delposts} post".($delposts != 1 ? "s" : "").", trashed {$delthreads} thread".($delthreads != 1 ? "s" : "").")</td></tr>";
			$banusercnt++;
			
		}

		print "
		$L[TBL1]>
			$L[TR1]>
				$L[TDgc] colspan=2>
					<b>{$banusercnt} user".($banusercnt != 1 ? "s" : "")." banned</b>
				</td>
			</tr>
			{$banusertext}
		</table>
		<br>
		";
	}
	
	// Layout config for easy add/removal
	$sort_types = array(
		'lastview'     => 'Last activity',
		'lastpost'     => 'Last post',
		'regdate'      => 'Registration date',
		'posts'        => 'Posts',
		'threads'      => 'Threads',
		'group_id'     => 'Group',
		'ip'           => 'IP address',
	);
	
	// Variable fetching
	$_POST['searchname']  = isset($_POST['searchname']) ? $_POST['searchname']    : "";
	$_POST['searchip']    = isset($_POST['searchip'])   ? $_POST['searchip']      : "";
	$_POST['maxposts']    = isset($_POST['maxposts'])   ? (int)$_POST['maxposts'] : 0;
	
	// Display only banned users by default
	$_POST['sortpower'] = isset($_POST['sortpower']) ? (int)$_POST['sortpower'] : SHOW_BANNED;
	$_POST['sortord']   = isset($_POST['sortord'])   ? (int)$_POST['sortord']   : 0;
	if (!isset($_POST['sorttype']) || !isset($sort_types[$_POST['sorttype']]))
		$_POST['sorttype'] = 'lastview';
	
	
	$listgroups = $sql->getresultsbykey("SELECT id, title FROM `group` WHERE `primary` = 1 ORDER BY sortorder ASC", 'id', 'title');
	$listgroups[SHOW_ALL]    = "* Any group";
	$listgroups[SHOW_BANNED] = "* All banned (default)";

 
 print "
<form method='POST' action='?'>
$L[TBL1]>
	$L[TRh]>$L[TDhc] colspan=2>Sort Options</td></tr>
	$L[TR1]>$L[TD1c] style='width: 300px'><b>User Search:</b></td>
		$L[TD2]>
			$L[INPt]='searchname' size=30 maxlength=25 value=\"".htmlspecialchars($_POST['searchname'])."\">
		</td>
	</tr>
	$L[TR1]>$L[TD1c]><b>IP Search:</b></td>
		$L[TD2]>
			$L[INPt]='searchip' size=20 maxlength=32 value=\"".htmlspecialchars($_POST['searchip'])."\">
			<small>use * as wildcard</small>
		</td>
	</tr>
	$L[TR1]>$L[TD1c]><b>Show users with less than:</b></td>
		$L[TD2]>$L[INPt]='maxposts' size=6 maxlength=9  value=\"".htmlspecialchars($_POST['maxposts'])."\"> posts</td></tr>
	$L[TR1]>$L[TD1c]><b>Group:</b></td>
		$L[TD2]>".fieldselect('sortpower', $_POST['sortpower'], $listgroups)."</td>
	</tr>
	$L[TR1]>$L[TD1c]><b>Sort by:</b></td>
		$L[TD2]>
			".fieldselect('sorttype', $_POST['sorttype'], $sort_types).", 
			".fieldoption('sortord', $_POST['sortord'], array('Descending', 'Ascending'))."
		</td>
	</tr>
	$L[TR1]>
		$L[TD1c]>&nbsp;</td>
		$L[TD2]>$L[INPs]='setfilter' value='Apply filters'></td>
	</tr>
</table>";

	// WHERE Clause
	$sqlwhere	= array();
	$values		= array();

	if ($_POST['maxposts']) {
		$sqlwhere[] = "`posts` <= ?";
		$values[]   = $_POST['maxposts'];
	}
	if ($_POST['searchip']) {
		$sqlwhere[] = "`ip` LIKE ?";
		$values[]   = str_replace("*", "%", $_POST['searchip']);
	}
	if ($_POST['searchname']) {
		$sqlwhere[] = "`name` LIKE ?";
		$values[]   = "%{$_POST['searchname']}%";
	}
	
	if ($_POST['sortpower'] == SHOW_BANNED) {
		$sqlwhere[] = "`group_id` IN (SELECT id FROM `group` WHERE banned = 1)";
	} else if ($_POST['sortpower'] != SHOW_ALL) {
		$sqlwhere[] = "`group_id` = ?";
		$values[]   = $_POST['sortpower'];
	}
	
	$wheretxt = $sqlwhere ? "WHERE ". implode(" AND ", $sqlwhere) : "";
	
	// ORDER Clause
	$sortfield = $_POST['sorttype'];
	$sortorder = $_POST['sortord'] ? "ASC" : "DESC";
	$users = $sql->prepare("
		SELECT *
		FROM users
		{$wheretxt}
		GROUP BY id
		ORDER BY {$sortfield} {$sortorder}", $values);
	$usercount	= $sql->numrows($users);

	// User results / selection table
print "
<br>
$L[TBL2]>
	$L[TRg]>$L[TDgc] colspan=9>{$usercount} user(s) found.</td></tr>
	$L[TRh]>
		$L[TDhc]>&nbsp;</td>
		$L[TDhc]>Name</td>
		$L[TDhc]>Posts</td>
		$L[TDhc]>Threads</td>
		$L[TDhc] style='width: 200px'>Registration date</td>
		$L[TDhc] style='width: 200px'>Last post</td>
		$L[TDhc] style='width: 200px'>Last activity</td>
		$L[TDhc]>Last URL</td>
		$L[TDhc]>IP</td>
	</tr>";
	while ($user = $sql->fetch($users)) {
		$userlink = userlink($user);
		
		if($user['lastpost']) $lastpost	= date($dateformat, $user['lastpost']);
			else $lastpost		= '-';
		if($user['lastview'] != $user['regdate']) $lastactivity	= date($dateformat, $user['lastview']);
			else $lastactivity	= '-';
		if($user['regdate']) $regdate = date($dateformat, $user['regdate']);
			else $regdate		= '-';

		// Padding of numbers to gray 0
		$textid	= str_pad($user['id'], 5, "x", STR_PAD_LEFT);
		$textid	= str_replace("x", "<font color=#606060>0</font>", $textid);
		$textid	= str_replace("</font><font color=#606060>", "", $textid);

	print "
	$L[TR1]>
		$L[TD1c]>$L[INPc]='banuser[{$user['id']}]' value=1></td>
		$L[TD2]>{$textid} - {$userlink}</td>
		$L[TD1c]>{$user['posts']}</td>
		$L[TD1c]>{$user['threads']}</td>
		$L[TD1c]>{$regdate}</td>
		$L[TD1c]>{$lastpost}</td>
		$L[TD1c]>{$lastactivity}</td>
		$L[TD2]>".htmlspecialchars($user['url'])."&nbsp;</td>
		$L[TD2c]>{$user['ip']}</td>
	</tr>";
	}

	print "
	$L[TR1]>
		$L[TD1] colspan=9>
			$L[INPs]='dotrash' value='Trash selected'> - Set group to: ".fieldselect("destgroup", $user['group_id'], $bannedgroups)."
			".auth_tag(TOKEN_USERDEL)."
		</td>
	</tr>
</table>
</form>";

  pagefooter();