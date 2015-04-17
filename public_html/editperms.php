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
  $permlist = null;

  if (!has_perm('edit-permissions'))
  {
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
  }
  
  if (isset($_GET['gid']))
  {
	$id = (int)$_GET['gid'];
	if((is_root_gid($id) || (!can_edit_group_assets($id) && $id!=$loguser['group_id'])) && !has_perm('no-restrictions'))
	{
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");		
	}
	if(	$loguser['group_id'] == $id && !has_perm('edit-own-permissions'))
	{
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");		
	}
	$permowner = $sql->fetchp("SELECT id,title,inherit_group_id FROM `group` WHERE id=?", array($id));
	$type = 'group';
	$typecap = 'Group';
  }
  else if (isset($_GET['uid']))
  {
	$id = (int)$_GET['uid'];

	$tuser = $sql->fetchp("SELECT `group_id` FROM users WHERE id=?",array($id));
	if ((is_root_gid($tuser[$u.'group_id']) || (!can_edit_user_assets($tuser[$u.'group_id']) && $id!=$loguser['id'])) && !has_perm('no-restrictions')) 
	{
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
	} 

	if ($id == $loguser['id'] && !has_perm('edit-own-permissions'))
	{
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
	}
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
    error("Error", "Invalid {$type} ID.");
  }
  
  $errmsg = '';
  
  if (isset($_POST['addnew']))
  {
	$revoke = (int)$_POST['revoke_new'];
	$permid = stripslashes($_POST['permid_new']);
	$bindval = (int)$_POST['bindval_new'];
	
	if(has_perm('no-restrictions') || $permid != 'no-restrictions') { 
    $sql->prepare("INSERT INTO `x_perm` (`x_id`,`x_type`,`perm_id`,`permbind_id`,`bindvalue`,`revoke`) VALUES (?,?,?,'',?,?)",
		array($id, $type, $permid, $bindval, $revoke)); 
    $msg="The ".title_for_perm($permid)." permission has been successfully assigned!"; 
    } else { 
    $msg="You do not have the permissions to assign the ".title_for_perm($permid)." permission!"; 
    } 
  }
  else if (isset($_POST['apply']))
  {
	$keys = array_keys($_POST['apply']);
	$pid = $keys[0];
	
	$revoke = (int)$_POST['revoke'][$pid];
	$permid = stripslashes($_POST['permid'][$pid]);
	$bindval = (int)$_POST['bindval'][$pid];
	
	if(has_perm('no-restrictions') || $permid != 'no-restrictions') { 
    $sql->prepare("UPDATE `x_perm` SET `perm_id`=?, `bindvalue`=?, `revoke`=? WHERE `id`=?",
		array($permid, $bindval, $revoke, $pid)); 
    $msg="The ".title_for_perm($permid)." permission has been successfully edited!"; 
    } else { 
    $msg="You do not have the permissions to edit the ".title_for_perm($permid)." permission!"; 
    }
  }
  else if (isset($_POST['del']))
  {
	$keys = array_keys($_POST['del']);
	$pid = $keys[0];
	$permid = stripslashes($_POST['permid'][$pid]);
	if(has_perm('no-restrictions') || $permid != 'no-restrictions') { 
    $sql->prepare("DELETE FROM `x_perm`WHERE `id`=?", array($pid)); $msg="The ".title_for_perm($permid)." permission has been successfully deleted!"; 
    } else { 
    $msg="You do not have the permissions to delete the ".title_for_perm($permid)." permission!"; 
    } 
  }
  
  pageheader('Edit permissions');

  $pagebar = array
  (
	  'breadcrumb' => array(array('href'=>'./', 'title'=>'Main')),
	  'title' => 'Edit permissions',
	  'actions' => array(),
  	  'message' => $msg
  );
	
  RenderPageBar($pagebar);
  
  // um yeah, plain <form> here. I would use the layout functions but those aren't flexible enough for what I want :/ -- Mega-Mario
  print
	"<form action=\"\" method=\"POST\">
";

  $header = array('c0' => array('caption' => '&nbsp;'), 'c1' => array('caption' => '&nbsp;'));
  $data = array();
  
  $permset = PermSet($type, $id);
  $row = array(); $i = 0;
  while ($perm = $sql->fetch($permset))
  {
	$pid = $perm['id'];
	
	$field = RevokeSelect("revoke[{$pid}]", $perm['revoke']);
	$field .= PermSelect("permid[{$pid}]", $perm['perm_id']);
	$field .= "for ID <input type=\"text\" name=\"bindval[{$pid}]\" value=\"".$perm['bindvalue']."\" size=3 maxlength=8> ";
	$field .= "<input type=\"submit\" name=\"apply[{$pid}]\" value=\"Apply changes\">";
	$field .= "<input type=\"submit\" name=\"del[{$pid}]\" value=\"Remove\">";
	$row['c'.$i] = $field;
	
	$i++;
	if ($i == 2)
	{
		$data[] = $row;
		$row = array();
		$i = 0;
	}
  }
  if (($i % 2) != 0)
  {
	$row['c1'] = '&nbsp;';
	$data[] = $row;
  }
  
  RenderTable($data, $header);
  
  $header = array('c0' => array('caption' => 'Add permission'));
  $field = RevokeSelect("revoke_new", 0);
  $field .= PermSelect("permid_new", null);
  $field .= "for ID <input type=\"text\" name=\"bindval_new\" value=\"\" size=3 maxlength=8> ";
  $field .= "<input type=\"submit\" name=\"addnew\" value=\"Add\">";
  $data = array(array('c0' => $field));
  RenderTable($data, $header);
  
  print
	"</form>
".	"<br>
";
  
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
  
  
  function PermSelect($name, $sel)
  {
	global $sql, $permlist;
	
	$cat = -1;
	if (!$permlist)
	{
		$perms = $sql->query("
			SELECT 
				p.id AS permid, p.title AS permtitle,
				pc.id AS cat, pc.title AS cattitle
			FROM 
				perm p
				LEFT JOIN permcat pc ON pc.id=p.permcat_id
			ORDER BY pc.sortorder ASC, p.title ASC");
			
		$permlist = array();
		while ($perm = $sql->fetch($perms))
			$permlist[] = $perm;
	}
		
	$out = "\t<select name=\"{$name}\">\n";
	foreach ($permlist as $perm)
	{
		if ($perm['cat'] != $cat)
		{
			if ($cat != -1) $out .= "\t\t</optgroup>\n";
			$cat = $perm['cat'];
			$out .= "\t\t<optgroup label=\"".($perm['cattitle'] ? htmlspecialchars($perm['cattitle']) : 'General')."\">\n";
		}
		
		$chk = ($perm['permid'] == $sel) ? ' selected="selected"' : '';
		$out .= "\t\t\t<option value=\"".htmlspecialchars($perm['permid'])."\"{$chk}>".htmlspecialchars($perm['permtitle'])."</option>\n";
	}
	$out .= "\t\t</optgroup>\n\t</select>\n";
	
	return $out;
  }
  
  function RevokeSelect($name, $sel)
  {
	$out = "\t<select name=\"{$name}\">\n";
	$out .= "\t\t<option value=\"0\"".($sel==0 ? ' selected="selected"':'').">Grant</option>\n";
	$out .= "\t\t<option value=\"1\"".($sel==1 ? ' selected="selected"':'').">Revoke</option>\n";
	$out .= "\t</select>\n";
	
	return $out;
  }
  
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