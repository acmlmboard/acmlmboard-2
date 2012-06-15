<?php
  require 'lib/common.php';
  
/*
[02:57:12]	Emuz	x_perm - the table assigning the specific permission to a user/forum/group via g/f/uid and x_type (group, forum, user)
[02:59:05]	Emuz	group - holds primary and secondary groups. Primary are selectable in the editprofile and are the user color definitions. Secondary are used to give rights and access without changing the main group etc. just like you'd expect
[02:59:20]	Emuz	they inhert from the previous group/sub group
[03:00:16]	Emuz	perm - I am 80% sure it is ment to be a list for any future editor for the perms. It's not really in code as of yet
[03:00:47]	Emuz	permbind - list of what perms can bind to for the editor
[03:01:08]	Emuz	permcat - Sort catagory for the editor for perms

'special' permissions:
 * no-restrictions: cancels out all the 'normal' others
 * show-as-staff: for memberlist
 * banned, staff: seem like useless/unimplemented permissions
*/

  if (!has_perm('edit-permissions'))
  {
	pageheader('Edit permissions');
	no_perm();
  }
  
  if (isset($_GET['gid']))
  {
	$id = (int)$_GET['gid'];
	$permowner = $sql->fetchp("SELECT id,title,inherit_group_id FROM `group` WHERE id=?", array($id));
	$type = 'group';
	$typecap = 'Group';
  }
  else if (isset($_GET['uid']))
  {
	$id = (int)$_GET['uid'];
	$permowner = $sql->fetchp("SELECT u.id,u.name AS title,u.group_id,g.title AS group_title FROM users u LEFT JOIN `group` g ON g.id=u.group_id WHERE u.id=?", array($id));
	$type = 'user';
	$typecap = 'User';
  }
  else if (isset($_GET['fid']))
  {
	$id = (int)$_GET['fid'];
	$permowner = $sql->fetchp("SELECT id,title FROM forums WHERE id=?", array($id));
	$type = 'forum';
	$typecap = 'Forum';
  }
  else
  {
	$id = 0;
	$permowner = null;
	$type = '';
	$typecap = '';
  }
  
  if (!$permowner)
  {
	// TODO: functions for custom error messages (this is not nice at all)
	pageheader('Edit permissions');
    print
		"$L[TBL1]>
".  	"  $L[TR2]>
".  	"    $L[TD1c]>
".  	"      Invalid {$type} ID.
".  	"$L[TBLend]
";
    pagefooter();
    die();
  }
  
  $errmsg = '';
  
  // actions go here
  
  pageheader('Edit permissions');

  $pagebar = array
  (
	  'breadcrumb' => array(array('href'=>'./', 'title'=>'Main')),
	  'title' => 'Edit permissions',
	  'actions' => array(),
  	  'message' => $errmsg
  );
	
  RenderPageBar($pagebar);
  
  // edit form goes right here
  echo 'Edit form is still a todo. For now enjoy the permissions overview.<br><br>';
  
  $permset = PermSet($type, $id);
  $permsassigned = array();
  
  $permoverview = '<strong>'.$typecap.' permissions:</strong><br>';
  $permoverview .= PermTable($permset);
  
  if ($type == 'group' && $permowner['inherit_group_id'] > 0)
  {
	$permoverview .= '<br><hr>';
	$permoverview .= '<strong>Permissions inherited from parent groups:</strong><br>';
	
	$parentid = $permowner['inherit_group_id'];
	while ($parentid > 0)
	{
		$parent = $sql->fetchp("SELECT title,inherit_group_id FROM `group` WHERE id=?", array($parentid));
		$permoverview .= '<br>'.htmlspecialchars($parent['title']).':<br>';
		$permoverview .= PermTable(PermSet('group', $parentid));
		
		$parentid = $parent['inherit_group_id'];
	}
  }
  else if ($type == 'user')
  {
	$permoverview .= '<hr>';
	$permoverview .= '<strong>Permissions inherited from primary group \''.htmlspecialchars($permowner['group_title']).'\':</strong><br>';
	
	$parentid = $permowner['group_id'];
	while ($parentid > 0)
	{
		$parent = $sql->fetchp("SELECT title,inherit_group_id FROM `group` WHERE id=?", array($parentid));
		$permoverview .= '<br>'.htmlspecialchars($parent['title']).':<br>';
		$permoverview .= PermTable(PermSet('group', $parentid));
		
		$parentid = $parent['inherit_group_id'];
	}
	
	$secgroups = $sql->prepare("SELECT g.id,g.title FROM user_group ug LEFT JOIN `group` g ON ug.group_id=g.id WHERE ug.user_id=? ORDER BY ug.sortorder DESC", array($id));
	while ($secgroup = $sql->fetch($secgroups))
	{
		$permoverview .= '<hr>';
		$permoverview .= '<strong>Permissions inherited from secondary group \''.htmlspecialchars($secgroup['title']).'\':</strong><br>';
		
		$parentid = $secgroup['id'];
		while ($parentid > 0)
		{
			$parent = $sql->fetchp("SELECT title,inherit_group_id FROM `group` WHERE id=?", array($parentid));
			$permoverview .= '<br>'.htmlspecialchars($parent['title']).':<br>';
			$permoverview .= PermTable(PermSet('group', $parentid));
			
			$parentid = $parent['inherit_group_id'];
		}
	}
  }
  
  $header = array('cell' => array('caption'=>"Permissions overview for {$type} '".htmlspecialchars($permowner['title'])."'"));
  $data = array(array('cell' => $permoverview));
  RenderTable($data, $header);
	
  echo '<br>';
  $pagebar['message'] = '';
  RenderPageBar($pagebar);

  pagefooter();
  
  
  function PermSet($type, $id)
  {
	global $sql;
	return $sql->prepare("
		SELECT 
			x.*,
			p.title AS permtitle,
			pb.title AS bindtitle
		FROM 
			x_perm x
			LEFT JOIN perm p ON p.id=x.perm_id
			LEFT JOIN permbind pb ON pb.id=p.permbind_id
		WHERE
			x.x_type=? AND x.x_id=?", 
		array($type,$id));
  }
  
  function PermTable($permset)
  {
	global $sql, $permsassigned;
	$ret = '';
	
	$i = 0;
	while ($perm = $sql->fetch($permset))
	{
		$key = $perm['perm_id'];
		if ($perm['bindvalue']) $key .= '['.$perm['bindvalue'].']';
		
		$discarded = false;
		if (isset($permsassigned[$key])) $discarded = true;
		else $permsassigned[$key] = true;
		
		$permtitle = $perm['permtitle'];
		if (!$permtitle) $permtitle = $perm['perm_id'];
		
		$ret .= "<td style=\"width:25%;\">&bull; ";
		if ($discarded) $ret .= '<s>';
		if ($perm['revoke']) $ret .= '<span style="color:#f88;">Revoke</span>: ';
		else $ret .= '<span style="color:#8f8;">Grant</span>: ';
		$ret .= '\''.htmlspecialchars($permtitle).'\'';
		
		if ($perm['bindvalue'])
		{
			$bindtitle = strtolower($perm['bindtitle']);
			if (!$bindtitle) $bindtitle = $perm['permbind_id'];
			if (!$bindtitle) $bindtitle = 'ID';
			
			$ret .= ' for '.htmlspecialchars($bindtitle).' #'.$perm['bindvalue'];
		}
		
		if ($discarded) $ret .= '</s>';
		
		$ret .= "</td>\n";
		
		$i++;
		if (($i % 4) == 0) $ret .= "</tr>\n<tr>\n";
	}
	
	if (($i % 4) != 0)
		$ret .= "<td colspan=\"".(4-($i%4))."\">&nbsp;</td>\n";
		
	if (!$ret) $ret = "<td>&bull; None</td>\n";
	
	return "<table style=\"width:100%;\">\n<tr>\n{$ret}</tr>\n</table>\n";
  }

?>