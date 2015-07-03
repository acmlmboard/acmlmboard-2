<?php
  require 'lib/common.php';

  if (!has_perm('edit-groups'))
  {
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
  }
  
  $act = $_GET['act'];
  $errmsg = '';
  $caneditperms = has_perm('edit-permissions');
  
  if ($act == 'delete')
  {
	$id = unpacksafenumeric($_GET['id']);
	$group = $sql->fetchp("SELECT * FROM `group` WHERE `id`=?", array($id));
	
	if (!$group)
		$errmsg = 'Cannot delete group: invalid group ID';
	else
	{
		if ($group['primary'])
		{
			$usercount = $sql->resultp("SELECT COUNT(*) FROM `users` WHERE `group_id`=?", array($group['id']));
			if ($usercount > 0) $errmsg = 'This group cannot be deleted because it is primary and contains users';
		}
		
		if (!$errmsg && !$caneditperms)
		{
			$permcount = $sql->resultp("SELECT COUNT(*) FROM `x_perm` WHERE `x_type`='group' AND `x_id`=?", array($group['id']));
			if ($permcount > 0) $errmsg = 'This group cannot be deleted because it has permissions attached and you may not edit permissions.';
		}
		
		if (!$errmsg)
		{
            	   $sql->prepare("INSERT INTO `deletedgroups` SELECT * FROM `group` WHERE `id`=?", array($group['id']));
			$sql->prepare("DELETE FROM `group` WHERE `id`=?", array($group['id']));
			$sql->prepare("DELETE FROM `user_group` WHERE `group_id`=?", array($group['id']));
			$sql->prepare("DELETE FROM `x_perm` WHERE `x_type`='group' AND `x_id`=?", array($group['id']));
			$sql->prepare("UPDATE `group` SET `inherit_group_id`=0 WHERE `inherit_group_id`=?", array($group['id']));
			
			die(header('Location: editgroups.php'));
		}
	}
  }
  else if (isset($_POST['submit']) && ($act == 'new' || $act == 'edit'))
  {
	$title = trim($_POST['title']);
	
	$parentid = $_POST['inherit_group_id'];
	if ($parentid < 0 || $parentid > $sql->resultq("SELECT MAX(id) FROM `group`")) $parentid = 0;
	
	if ($act == 'edit')
	{
		$recurcheck = array($_GET['id']);
		$pid = $parentid;
		while ($pid > 0)
		{
			if ($pid == $recurcheck[0])
			{
				$errmsg = 'Endless recursion detected, choose another parent for this group';
				break;
			}
			
			$recurcheck[] = $pid;
			$pid = $sql->resultp("SELECT `inherit_group_id` FROM `group` WHERE `id`=?",array($pid));
		}
	}
	
	if (!$errmsg)
	{
		$default = $_POST['default'];
		if ($default < -1 || $default > 1) $default = 0;
		
		$banned = $_POST['banned']; 
		if ($banned > 1) $banned = 0; 
		
		$sortorder = (int)$_POST['sortorder'];
		
		$visible = $_POST['visible'] ? 1:0;
		$primary = $_POST['primary'] ? 1:0;
		
		if (empty($title))
			$errmsg = 'You must enter a name for the group.';
		else
		{
			$values = array($title, $_POST['nc0'], $_POST['nc1'], $_POST['nc2'], $parentid, $default, $banned, $sortorder, $visible, 
				$primary, $_POST['description']);
				
			if ($act == 'new')
				$sql->prepare("INSERT INTO `group` VALUES (0,?,'',NULL,?,?,?,?,?,?,?,?,?,?)", $values);
			else
			{
				$values[] = $_GET['id'];
				$sql->prepare("UPDATE `group` SET `title`=?,`nc0`=?,`nc1`=?,`nc2`=?,`inherit_group_id`=?,`default`=?,`banned`=?,
					`sortorder`=?,`visible`=?,`primary`=?,`description`=? WHERE id=?", $values);
			}
				
			die(header('Location: editgroups.php'));
		}
	}
  }
  
  pageheader('Edit groups');


    if (isset($_GET['deletedgroups']))
  {
 
 if ($act == 'undelete')
  {
	$id = unpacksafenumeric($_GET['id']);
	$deletedgroup = $sql->fetchp("SELECT * FROM `deletedgroups` WHERE `id`=?", array($id));
 
	if (!$deletedgroup)
		$errmsg = 'Cannot undelete group: invalid group ID';
	else
	{
		if (!$errmsg)
		{
                        $sql->prepare("INSERT INTO `group` SELECT * FROM `deletedgroups` WHERE `id`=?", array($deletedgroup['id']));
			$sql->prepare("DELETE FROM `deletedgroups` WHERE `id`=?", array($deletedgroup['id']));
                        $sql->prepare("UPDATE `deletedgroups` SET `inherit_group_id`=0 WHERE `inherit_group_id`=?", array($deletedgroup['id']));
		}
	}
  }
 
       $pagebar = array
	(
		'breadcrumb' => array(array('href'=>'/.', 'title'=>'Main'), array('href'=>'index.php', 'title'=>'Forums'), array('href'=>'management.php', 'title'=>'Management'), array('href'=>'editgroups.php', 'title'=>'Edit groups')),
		'title' => '',
		'actions' => array(array('href'=>'editgroups.php?act=new', 'title'=>'New group'), array('href'=>'editgroups.php', 'title'=>'Groups')),
		'message' => $errmsg
	);
        if (isset($_GET['deletedgroups']))
	{
		$pagebar['title'] = 'Deleted groups';
	}
 
        RenderPageBar($pagebar);
 
	$header = array
	(
		'sort' => array('caption'=>'Order', 'width'=>'32px', 'align'=>'center'),
		'id' => array('caption'=>'#', 'width'=>'32px', 'align'=>'center'),
		'name' => array('caption'=>'Name', 'align'=>'center'),
		'descr' => array('caption'=>'Description', 'align'=>'left'),
		'parent' => array('caption'=>'Parent group', 'align'=>'center'),
		'ncolors' => array('caption'=>'Username colors', 'width'=>'175px', 'align'=>'center'),
		'misc' => array('caption'=>'Default?', 'width'=>'120px', 'align'=>'center'),
		'bmisc' => array('caption'=>'Banned?', 'width'=>'120px', 'align'=>'center'), 
		'actions' => array('caption'=>'', 'align'=>'right'),
	);
 
       $deletedgroups = $sql->query("SELECT g.*, pg.title parenttitle FROM `deletedgroups` g LEFT JOIN `deletedgroups` pg ON pg.id=g.inherit_group_id ORDER BY sortorder");
       $data = array();
 
       while ($deletedgroup = $sql->fetch($deletedgroups))
	{
		$name = htmlspecialchars($deletedgroup['title']);
		if ($deletedgroup['primary']) $name = "<strong>{$name}</strong>";
		if (!$deletedgroup['visible']) $name = "<span style=\"opacity: 0.6;\">{$name}</span>";
 
		if ($deletedgroup['nc0'] && $group['nc1'] && $deletedgroup['nc2'])
			$ncolors = "<strong style=\"color: #{$group['nc0']};\">Male</strong> <strong style=\"color: #{$group['nc1']};\">Female</strong> <strong style=\"color: #{$group['nc2']};\">Unspec.</strong>";
		else
			$ncolors = '<small>(none set)</small>';
 
		$misc = '-';
		if ($deletedgroup['default'])
			$misc = $deletedgroup['default'] == -1 ? 'For first user' : 'For all users';
			
        $bmisc = '-'; 
		if ($deletedgroup['banned']) 
		    $bmisc = $deletedgroup['banned'] == 1 ? 'For banned users' : '-'; 
 
		$actions = array();
		if ($caneditperms) $actions[] = array('href'=>'editgroups.php?deletedgroups&act=undelete&id='.urlencode(packsafenumeric($deletedgroup['id'])), 'title'=>'Undelete', 
			'confirm'=>'Are you sure you want to undelete the group "'.htmlspecialchars($deletedgroup['title']).'"?');
 
		$data[] = array
		(
			'sort' => $deletedgroup['sortorder'],
			'id' => $deletedgroup['id'],
			'name' => $name,
			'descr' => htmlspecialchars($deletedgroup['description']),
			'parent' => $deletedgroup['parenttitle'] ? htmlspecialchars($deletedgroup['parenttitle']) : '<small>(none)</small>',
			'ncolors' => $ncolors,
			'misc' => $misc,
			'bmisc' => $bmisc, 
			'actions' => RenderActions($actions,true),
		);
	}
 
        RenderForm($form);
        RenderTable($data, $header);
	echo '<br>';
	$pagebar['message'] = '';
	RenderPageBar($pagebar);
  }
  elseif ($act == 'new' || $act == 'edit')
  {
	$pagebar = array
	(
		'breadcrumb' => array(array('href'=>'./', 'title'=>'Main'), array('href'=>'management.php', 'title'=>'Management'), array('href'=>'editgroups.php', 'title'=>'Edit groups')),
		'title' => '',
		'actions' => array(array('href'=>'editgroups.php?act=new', 'title'=>'New group'), array('href'=>'editgroups.php?deletedgroups', 'title'=>'Deleted groups')),
		'message' => $errmsg
	);
	
	if ($act == 'new')
	{
		$group = array('id'=>0, 'title'=>'', 'nc0'=>'', 'nc1'=>'', 'nc2'=>'', 'inherit_group_id'=>0, 'default'=>0, 'banned'=>0, 'sortorder'=>0, 'visible'=>0, 'primary'=>0, 'description'=>'');
		$pagebar['title'] = 'New group';
	}
	else
	{
		$group = $sql->fetchp("SELECT * FROM `group` WHERE id=?",array($_GET['id']));
              if (!$group) {
              noticemsg("Error", "Invalid group ID."); pagefooter(); die();
        }
		$pagebar['title'] = 'Edit group';
	}
		
	if ($group)
	{
		$grouplist = array(0 => '(none)');
		$allgroups = $sql->prepare("SELECT id,title FROM `group` WHERE id!=? ORDER BY sortorder",array($group['id']));
		while ($g = $sql->fetch($allgroups))
			$grouplist[$g['id']] = $g['title'];
			
		$defaultlist = array(0=>'-', -1=>'For first user', 1=>'For all users');
		$bannedlist = array(0=>'-', 1=>'For banned users'); 
		$visiblelist = array(1=>'Visible', 0=>'Invisible');
		$primarylist = array(1=>'Primary', 0=>'Secondary');
		
		$form = array
		(
			'action' => '',
			'method' => 'POST',
			'categories' => array(
				'group' => array(
					'title' => 'Group settings',
					'fields' => array(
						'title' => array('title'=>'Name', 'type'=>'text', 'length'=>255, 'size'=>50, 'value'=>$group['title']),
						'description' => array('title'=>'Description', 'type'=>'text', 'length'=>255, 'size'=>100, 'value'=>$group['description']),
						'inherit_group_id' => array('title'=>'Parent group', 'type'=>'dropdown', 'choices'=>$grouplist, 'value'=>$group['inherit_group_id']),
						'default' => array('title'=>'Default', 'type'=>'dropdown', 'choices'=>$defaultlist, 'value'=>$group['default']),
						'banned' => array('title'=>'Banned', 'type'=>'dropdown', 'choices'=>$bannedlist, 'value'=>$group['banned']), 
						'sortorder' => array('title'=>'Sort order', 'type'=>'numeric', 'length'=>8, 'size'=>4, 'value'=>$group['sortorder']),
						'visible' => array('title'=>'Visibility', 'type'=>'radio', 'choices'=>$visiblelist, 'value'=>$group['visible']),
						'primary' => array('title'=>'Type', 'type'=>'radio', 'choices'=>$primarylist, 'value'=>$group['primary']),
					)
				),
				'colors' => array(
					'title' => 'Username colors',
					'fields' => array(
						'nc0' => array('title'=>'Male color', 'type'=>'color', 'value'=>$group['nc0']),
						'nc1' => array('title'=>'Female color', 'type'=>'color', 'value'=>$group['nc1']),
						'nc2' => array('title'=>'Unspec. color', 'type'=>'color', 'value'=>$group['nc2']),
					)
				),
				'actions' => array(
					'fields' => array(
						'submit' => array('title'=>($act=='new' ? 'Create group':'Apply changes'), 'type'=>'submit'),
					)
				),
			)
		);
		
		RenderPageBar($pagebar);
		RenderForm($form);
		echo '<br>';
		$pagebar['message'] = '';
		RenderPageBar($pagebar);
	}
  }
  else
  {
	$pagebar = array
	(
		'breadcrumb' => array(array('href'=>'./', 'title'=>'Main'), array('href'=>'management.php', 'title'=>'Management')),
		'title' => 'Edit groups',
		'actions' => array(array('href'=>'editgroups.php?act=new', 'title'=>'New group'), array('href'=>'editgroups.php?deletedgroups', 'title'=>'Deleted groups')),
		'message' => $errmsg
	);
	
	RenderPageBar($pagebar);
	
	$header = array
	(
		'sort' => array('caption'=>'Order', 'width'=>'32px', 'align'=>'center'),
		'id' => array('caption'=>'#', 'width'=>'32px', 'align'=>'center'),
		'name' => array('caption'=>'Name', 'align'=>'center'),
		'descr' => array('caption'=>'Description', 'align'=>'left'),
		'parent' => array('caption'=>'Parent group', 'align'=>'center'),
		'ncolors' => array('caption'=>'Username colors', 'width'=>'175px', 'align'=>'center'),
		'misc' => array('caption'=>'Default?', 'width'=>'120px', 'align'=>'center'),
		'bmisc' => array('caption'=>'Banned?', 'width'=>'120px', 'align'=>'center'), 
		'actions' => array('caption'=>'', 'align'=>'right'),
	);
	
	$groups = $sql->query("SELECT g.*, pg.title parenttitle FROM `group` g LEFT JOIN `group` pg ON pg.id=g.inherit_group_id ORDER BY sortorder");
	$data = array();
	
	while ($group = $sql->fetch($groups))
	{
		$name = htmlspecialchars($group['title']);
		if ($group['primary']) $name = "<strong>{$name}</strong>";
		if (!$group['visible']) $name = "<span style=\"opacity: 0.6;\">{$name}</span>";
		
		if ($group['nc0'] && $group['nc1'] && $group['nc2'])
			$ncolors = "<strong style=\"color: #{$group['nc0']};\">Male</strong> <strong style=\"color: #{$group['nc1']};\">Female</strong> <strong style=\"color: #{$group['nc2']};\">Unspec.</strong>";
		else
			$ncolors = '<small>(none set)</small>';
		
		$misc = '-';
		if ($group['default'])
			$misc = $group['default'] == -1 ? 'For first user' : 'For all users';
			
		$bmisc = '-'; 
		if ($group['banned']) 
			$bmisc = $group['banned'] == 1 ? 'For banned users' : '-'; 
		
		$actions = array();
		if ($caneditperms) $actions[] = array('href'=>'editperms.php?gid='.$group['id'], 'title'=>'Edit permissions');
		$actions[] = array('href'=>'editgroups.php?act=edit&id='.$group['id'], 'title'=>'Edit');
		if ($caneditperms) $actions[] = array('href'=>'editgroups.php?act=delete&id='.urlencode(packsafenumeric($group['id'])), 'title'=>'Delete', 
			'confirm'=>'Are you sure you want to delete the group "'.htmlspecialchars($group['title']).'"? It will be permanently lost as well as all permissions attached to it.');
		
		$data[] = array
		(
			'sort' => $group['sortorder'],
			'id' => $group['id'],
			'name' => $name,
			'descr' => htmlspecialchars($group['description']),
			'parent' => $group['parenttitle'] ? htmlspecialchars($group['parenttitle']) : '<small>(none)</small>',
			'ncolors' => $ncolors,
			'misc' => $misc,
			'bmisc' => $bmisc, 
			'actions' => RenderActions($actions,true),
		);
	}
	
	RenderTable($data, $header);
	echo '<br>';
	$pagebar['message'] = '';
	RenderPageBar($pagebar);
  }

  pagefooter();

?>