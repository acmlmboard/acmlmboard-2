<?php

// [Mega-Mario] preload group data, makes things a lot easier afterwards
$usergroups = array();
$r = $sql->query("SELECT * FROM `group`");
while ($g = $sql->fetch($r))
	$usergroups[$g['id']] = $g;

//this processes the permission stack, in this order:
//-user permissions
//-user's primary group permissions, then the parent group's permissions, recursively until it reaches the top
//-user's secondary groups in sort order descending, then recursively through parents..
//first encountered occurence of a permission has precendence (+/-)
function load_user_permset() {
	global $logpermset, $loguser, $sql, $loggroups;


	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user perm set<br>";
	//load user specific permissions
	$logpermset = perms_for_x('user',$loguser['id']);

	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user secondary groups<br>";
	$loggroups = secondary_groups_for_user($loguser['id']);
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "iterating through user secondary groups<br>";
	foreach ($loggroups as $gid) {
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "Group $gid<br>";
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user secondary groups perm sets<br>";
		$logpermset = apply_group_permissions($logpermset,$gid);
	}

	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user primary group perm set<br>";
	$logpermset = apply_group_permissions($logpermset,$loguser['group_id']);

	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "done loading permset";
}

//Badge permset

function permset_for_user($userid) {
	global $sql;
	$permset = array();
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user perm set<br>";
	//load user specific permissions
	$permset = perms_for_x('user',$userid);

	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user secondary groups<br>";
	$groups = secondary_groups_for_user($userid);
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "iterating through user secondary groups<br>";
	foreach ($groups as $gid) {
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "Group $gid<br>";
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user secondary groups perm sets<br>";
		$permset = apply_group_permissions($permset,$gid);
	}

	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user primary group perm set<br>";
	$permset = apply_group_permissions($permset,gid_for_user($userid));

	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "done loading permset";
	return $permset;
}

function is_root_gid($gid) {
	global $sql;
	$result = $sql->resultp("SELECT `default` FROM `group` WHERE id=?",array($gid));
	if ($result < 0) return true;
	return false;
}

function gid_for_user($userid) {
	global $sql;
	$row = $sql->fetchp("SELECT group_id FROM users WHERE id=?",array($userid));
	return $row['group_id'];	
}

function sex_for_user($userid) {
	global $sql;
	$row = $sql->fetchp("SELECT sex FROM users WHERE id=?",array($userid));
	return $row['sex'];
}
function color_for_group($gid,$sex) {
	global $sql;
	$row = $sql->fetchp("SELECT nc$sex FROM `group` WHERE id=?",array($gid));
	return $row["nc$sex"];
}

function load_guest_permset() {
	global $logpermset;
	$logpermset = array();
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user secondary groups<br>";
	$loggroups = secondary_groups_for_user(0);
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "iterating through user secondary groups<br>";
	foreach ($loggroups as $gid) {
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "Group $gid<br>";
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user secondary groups perm sets<br>";
		$logpermset = apply_group_permissions($logpermset,$gid);
	}
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "done loading permset";	
}

function load_bot_permset() {
	global $logpermset;
	$logpermset = array();
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user secondary groups<br>";
	$loggroups = secondary_groups_for_user(-1);
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "iterating through user secondary groups<br>";
	foreach ($loggroups as $gid) {
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "Group $gid<br>";
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "loading user secondary groups perm sets<br>";
		$logpermset = apply_group_permissions($logpermset,$gid);
	}
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "done loading permset";	
}

function title_for_perm($permid) {
	global $sql;
	$row = $sql->fetchp("SELECT title FROM perm WHERE id=?",array($permid));
	return $row['title'];
}

function apply_group_permissions($permset,$gid) {
	//apply group permissions from lowest node upwards
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "apply group $gid permissions<br>";
	while ($gid > 0) {		 
		$gpermset = perms_for_x('group',$gid);
		foreach ($gpermset as $k => $v) {
			//remove already added permissions
			if (in_permset($permset,$v)) unset($gpermset[$k]);
		}
		//merge permissions
		$permset = array_merge($permset,$gpermset);
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "$gid inherits from ";
		$gid = parent_group_for_group($gid);
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "$gid<br>";
	}
	return $permset;
}

function secondary_groups_for_user($userid) {
	global $sql;
	$out = array();
	$c = 0;
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "looking up secondary groups for user=$userid<br>";
	$res = $sql->prepare("SELECT * FROM user_group WHERE user_id=? ORDER BY sortorder DESC",array($userid));
	while ($row = $sql->fetch($res)) {
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "got group=".$row['group_id']."<br>";
		$out[$c++] = $row['group_id'];
	}
	return $out;
}

function in_permset($permset,$perm) {
	foreach ($permset as $v) {
		if (($v['id'] == $perm['id']) && ($v['bindvalue'] == $perm['bindvalue'])) 
			return true;
	}
	return false;
}

function can_view_cat($cat) {

	//can view public categories
	//HOSTILE DEBUGGING echo 'checking for public cat<br>';
	if (!has_perm('view-public-categories')) return false;

	//HOSTILE DEBUGGING echo 'getting cat of forum<br>';

	//is it a private category?
	//HOSTILE DEBUGGING echo 'checking private of cat<br>';
	if ($cat['private']) {
		//HOSTILE DEBUGGING echo 'checking for private cat<br>';
		//can view the forum's category

		if (!has_perm('view-all-private-categories') &&	
		    !has_perm_with_bindvalue('view-private-category',$cat['id'])) return false;	
	}
	return true;	
}


function can_edit_post($post) {
  global $loguser;
  if ($post['user'] == $loguser['id'] && has_perm('update-own-post')) return true;
  else if (has_perm('update-post')) return true;
  else if (can_edit_forum_posts($post['tforum'])) return true;

  return false;
}

function can_edit_group_assets($gid) {
  global $sql,$loguser;
  if (has_perm('edit-all-group')) return true;
  else if (has_perm_with_bindvalue('can-edit-group', $gid)) return true;
  return false;
}

function can_edit_user_assets($gid) {
  global $sql,$loguser;
  if (has_perm('edit-all-group-member')) return true;
  else if (has_perm_with_bindvalue('can-edit-group-member', $gid)) return true;
  return false;
}

function can_edit_user($uid) {
  global $sql,$loguser;

  $gid = gid_for_user($uid);
  if (is_root_gid($gid) && !has_perm('no-restrictions')) return false;
  if ((!can_edit_user_assets($gid) && $uid!=$loguser['id']) && !has_perm('no-restrictions')) return false;

  if ($uid == $loguser['id'] && has_perm('update-own-profile')) return true;
  else if (has_perm('update-profiles')) return true;
  else if (has_perm_with_bindvalue('update-user-profile',$uid)) return true;
  return false;
}

function can_edit_user_moods($uid) {
  global $sql,$loguser;
  if ($uid == $loguser['id'] && has_perm('update-own-moods')) return true;
  else if (has_perm('edit-moods')) return true;
  else if (has_perm_with_bindvalue('update-user-moods',$uid)) return true;
  return false;
}


function cats_with_view_perm() {
  global $sql;
  static $cache = "";
  if($cache != "") return $cache;
  $cache="(";
  $r=$sql->query("SELECT id,private FROM categories");
  while($d=$sql->fetch($r)) {
    if(can_view_cat($d)) $cache.="$d[id],";
  }
  $cache.="NULL)";
  return $cache;
}


function forums_with_view_perm() {
  global $sql;
  static $cache = "";
  if($cache != "") return $cache;
  $cache="(";
  $r=$sql->query("SELECT f.id, f.private, f.cat, c.private cprivate FROM forums f LEFT JOIN categories c ON c.id=f.cat");
  while($d=$sql->fetch($r)) {
    if(can_view_forum($d)) $cache.="$d[id],";
  }
  $cache.="NULL)";
  return $cache;
}

function forums_with_edit_posts_perm() {
  global $sql;
  static $cache = "";
  if($cache != "") return $cache;
  $cache="(";
  $r=$sql->query("SELECT id FROM forums");
  while($d=$sql->fetch($r)) {
    if(can_edit_forum_posts($d[id])) $cache.="$d[id],";
  }
  $cache.="NULL)";
  return $cache;
}

function forums_with_delete_posts_perm() {
  global $sql;
  static $cache = "";
  if($cache != "") return $cache;
  $cache="(";
  $r=$sql->query("SELECT id FROM forums");
  while($d=$sql->fetch($r)) {
    if(can_delete_forum_posts($d[id])) $cache.="$d[id],";
  }
  $cache.="NULL)";
  return $cache;
}

function forums_with_edit_threads_perm() {
  global $sql;
  static $cache = "";
  if($cache != "") return $cache;
  $cache="(";
  $r=$sql->query("SELECT id FROM forums");
  while($d=$sql->fetch($r)) {
    if(can_edit_forum_threads($d[id])) $cache.="$d[id],";
  }
  $cache.="NULL)";
  return $cache;
}

function forums_with_delete_threads_perm() {
  global $sql;
  static $cache = "";
  if($cache != "") return $cache;
  $cache="(";
  $r=$sql->query("SELECT id FROM forums");
  while($d=$sql->fetch($r)) {
    if(can_delete_forum_threads($d[id])) $cache.="$d[id],";
  }
  $cache.="NULL)";
  return $cache;
}

function can_view_forum($forum) {
	//must fulfill the following criteria

	if (!can_view_cat(array('id'=>$forum['cat'], 'private'=>$forum['cprivate']))) return false;

	//can view public forums
	//HOSTILE DEBUGGING echo 'checking for public forum<br>';
	if (!has_perm('view-public-forums')) return false;

	//and if the forum is private
	//HOSTILE DEBUGGING echo 'checking private of forum<br>';
	if ($forum['private']) {
		//can view the forum
		//HOSTILE DEBUGGING echo 'checking for private forum<br>';
		if (!has_perm('view-all-private-forums') && 
		    !has_perm_with_bindvalue('view-private-forum',$forum['id'])) return false;
	}
	return true;
}


function needs_login($head=0) {
	global $log;
	if (!$log) { 
		if ($head) pageheader('Login required');
		$err = "You need to be logged in to do that!<br><a href=login.php>Please login here.</a>";	
	        print
        "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"n2\">
".      "    <td class=\"b n1\" align=\"center\">
".      "     $err
".      "</table>
";
      pagefooter();
      die();
	}
}


/*function no_perm() {
	        print
        "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"n2\">
".      "    <td class=\"b n1\" align=\"center\">
".      "      You have no permissions to do this!<br> <a href=./>Back to main</a> 
".      "</table>
";
      pagefooter();
      die();
}*/

function grouplink($usex, $gid) {
	global $sql, $usergroups;

	$group = $usergroups[$gid];
	if ($group['default'] != 1) 
	return "<font color='#".$group['nc'.$usex]."'>".$group['title']."</font>";
	else return "";
}

/*function forum_not_found() {
	        print
        "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"n2\">
".      "    <td class=\"b n1\" align=\"center\">
".      "      Forum does not exist. <br> <a href=./>Back to main</a> 
".      "</table>
";
      pagefooter();
      die();
}*/

/*function pm_not_found() {
	        print
        "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"n2\">
".      "    <td class=\"b n1\" align=\"center\">
".      "      Private message does not exist. <br> <a href=./>Back to main</a> 
".      "</table>
";
      pagefooter();
      die();
}*/

/*function thread_not_found() {
	        print
        "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"n2\">
".      "    <td class=\"b n1\" align=\"center\">
".      "      Thread does not exist. <br> <a href=./>Back to main</a> 
".      "</table>
";
      pagefooter();
      die();
}*/


function can_create_forum_thread($forum) {

       global $log; 
	if ($forum['readonly'] && !has_perm('override-readonly-forums')) return false;

	//must fulfill the following criteria

	//can create public threads
	//HOSTILE DEBUGGING echo 'checking for public forum<br>';
	if (!has_perm('create-public-thread')) return false;
       if (!has_perm('post-offline') && !$log) return false;

	//and if the forum is private
	//HOSTILE DEBUGGING echo 'checking private of forum<br>';
	if ($forum['private']) {
		//can view the forum
		//HOSTILE DEBUGGING echo 'checking for private forum<br>';
		if (!has_perm('create-all-private-forum-threads') && 
		    !has_perm_with_bindvalue('create-private-forum-thread',$forum['id'])) return false;
	}
	return true;
}

function can_create_forum_post($forum) {

       global $log; 
	if ($forum['readonly'] && !has_perm('override-readonly-forums')) return false;

	//must fulfill the following criteria

	//can create public threads
	//HOSTILE DEBUGGING echo 'checking for public forum<br>';
	if (!has_perm('create-public-post')) return false;
       if (!has_perm('post-offline') && !$log) return false;

	//and if the forum is private
	//HOSTILE DEBUGGING echo 'checking private of forum<br>';
	if ($forum['private']) {
		//can view the forum
		//HOSTILE DEBUGGING echo 'checking for private forum<br>';
		if (!has_perm('create-all-private-forum-posts') && 
		    !has_perm_with_bindvalue('create-private-forum-post',$forum['id'])) return false;
	}
	return true;
}


function can_create_forum_announcements($forumid) {
	if (!has_perm('create-all-forums-announcement') && 
		!has_perm_with_bindvalue('create-forum-announcement',$forumid)) return false;
	
	return true;
}

function can_create_locked_posts($forumid, $threadid) {
	/*if (!has_perm('override-closed-all') && 
		!has_perm_with_bindvalue('override-closed-forum',$forumid) && 
		!has_perm_with_bindvalue('override-closed-thread',$threadid)) return false;*/
		if (has_perm('override-closed-all')) return true; 

		if (has_perm_with_bindvalue('override-closed-forum',$forumid)) return true; 
		if (has_perm_with_bindvalue('override-closed-thread',$threadid)) return true;
	
	return false;
}

function can_post_consecutively($forumid) {
                if (has_perm('consecutive-posts')) return true; 

                if (has_perm_with_bindvalue('create-consecutive-forum-post',$forumid)) return true; 

        return false;
}

function can_edit_forum_posts($forumid) {
	if (!has_perm('update-post') && 
		!has_perm_with_bindvalue('edit-forum-post',$forumid)) return false;
	
	return true;
}

function can_delete_forum_posts($forumid) {
	if (!has_perm('delete-post') && 
		!has_perm_with_bindvalue('delete-forum-post',$forumid)) return false;
	
	return true;
}

function can_edit_forum_threads($forumid) {
	if (!has_perm('update-thread') && 
		!has_perm_with_bindvalue('edit-forum-thread',$forumid)) return false;
	
	return true;
}

function can_view_forum_post_history($forumid) {
	if (!has_perm('view-post-history') && 
		!has_perm_with_bindvalue('view-forum-post-history',$forumid)) return false;	
	return true;
}


function can_delete_forum_threads($forumid) {
	if (!has_perm('delete-thread') && 
		!has_perm_with_bindvalue('delete-forum-thread',$forumid)) return false;
	
	return true;
}


function catid_of_forum($forumid) {
	global $sql;
	$row = $sql->fetchp("SELECT cat FROM forums WHERE id=?",array($forumid));
	return $row['cat'];
}

function has_perm($permid) {
	global $logpermset;
	foreach ($logpermset as $k => $v) {
		if ($v['id'] == 'no-restrictions') return true;
		if ($permid == $v['id'] && !$v['revoke']) return true;
	}
	return false;
}

function has_perm_revoked($permid) {
	global $logpermset;
	foreach ($logpermset as $k => $v) {
		if ($v['id'] == 'no-restrictions') return false;
		if ($permid == $v['id'] && $v['revoke']) return true;
	}
	return false;
}

function has_perm_with_bindvalue($permid,$bindvalue) {
	global $logpermset;
	foreach ($logpermset as $k => $v) {
		if ($v['id'] == 'no-restrictions') return true;
		if ($permid == $v['id'] && !$v['revoke'] && $bindvalue == $v['bindvalue']) 
		return true;
	}
	return false;	
}

function has_special_perm($permid) {
	global $logpermset;
	//This function does the same as has_perm, but does not check for no-retrictions.
	foreach ($logpermset as $k => $v) {
		if ($permid == $v['id'] && !$v['revoke']) return true;
	}
	return false;
}

function parent_group_for_group($groupid) {
	global $usergroups;

	$gid = $usergroups[$groupid]['inherit_group_id'];
	if ($gid > 0) {
		return $gid;
	}
	else {
		return 0;
	}
}

function perms_for_x($xtype,$xid) {
	global $sql;
	//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "querying for perms where $xtype=$xid<br>";
	$res = $sql->prepare("SELECT * FROM x_perm WHERE x_type=? AND x_id=?",
						array($xtype,$xid));

	$out = array();
	$c = 0;
	while ($row = $sql->fetch($res)) {
		//HOSTILE DEBUGGING //HOSTILE DEBUGGING echo "got perm ".$row['perm_id']."<br>";
		$out[$c++] = array(
				'id' => $row['perm_id'],
				'bind_id' => $row['permbind_id'],
				'bindvalue' => $row['bindvalue'],
				'revoke' => $row['revoke'],
				'xtype' => $xtype,
				'xid' => $xid			
			);
	}
	return $out;
}

 function forumlink_by_id($fid) {
    global $sql;
    $f = $sql->fetchp("SELECT id,title FROM forums WHERE id=? AND id IN ".forums_with_view_perm(),array($fid));
    if ($f) return "<a href=forum.php?id=$f[id]>$f[title]</a>";
    else return 0;
 }

 function threadlink_by_id($tid) {
 	global $sql;
    $thread = $sql->fetchp("SELECT id,title FROM threads WHERE id=? AND forum IN ".forums_with_view_perm(),array($tid));
	
	if ($thread) return "<a href=thread.php?id=$thread[id]>".forcewrap(htmlval($thread[title]))."</a>";
	else return 0;
 }

?>