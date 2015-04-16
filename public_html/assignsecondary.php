<?php
/* This uses a duplicate of editbadges.php as template. -Emuz */

require("lib/common.php");

  $r = request_variables(array('id', 'uid', 'action','act'));
  $pagebar = array();
  checknumeric($r['id']);
  checknumeric($r['uid']);

  pageheader("Assign Secondary Groups");

  if(!has_perm('assign-secondary-groups')) { noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>"); pagefooter(); die(); }
  if($r['action'] == "edit") noperm(); // Poormans bypass - Needs to be properly retooled

  if(!isset($r['uid']) || $r['uid'] == 0)
  {
    noticemsg("Error", "No User Requested.<br> <a href=./>Back to main</a>");
    pagefooter();
    die();
  }
  $id = $r['id'];
  $uid = $r['uid'];

  /*if($loguser['id'] == $uid && !has_perm('edit-own-permissions'))
  {
    noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
    pagefooter();
    die();    
  }*///Not really needed in normal context. I commented it out incase someone may want this -Emuz

  if ($r['action'] == "del") {
    unset($r['action']);
    if((is_root_gid($id) || !can_edit_group_assets($id) && $id!=$loguser['group_id']) && !has_perm('no-restrictions')) $pagebar['message'] = "You do not have the permissions to revoke this group.";
    else if ($id > 0) {
      if ($sql->prepare('DELETE FROM user_group WHERE user_id=? AND group_id=? LIMIT 1',array($uid, $id))) {
      $pagebar['message'] = "User successfully removed from group.";
 }
else {
 $pagebar['message'] = "Unable to remove user from group.";
}
    }
  }

  if(empty($r['action'])) {


$headers = array
(
	"id" => array //Entry key is used in $data to bind fields
	(
		"caption" => "#",
		"width" => "32px",
		"align" => "center",
		"color" => 1,
    "hidden" => 'true'
	),
	"group" => array("caption"=>"Name", "width"=>"1400px", "align"=>"center", "color"=>2),
  "edit" => array("caption"=>"Actions", "align" => "left", "color"=>1),
);

$data = array();
$sndgReq = $sql->query("SELECT * FROM `group`
                       RIGHT JOIN `user_group` ON `group`.`id` = `user_group`.`group_id`
                       WHERE `user_group`.`user_id`='$uid'");
while($sndg = $sql->fetch($sndgReq))
{

$actions = array(
 /* array('title' => 'Edit','href' => 
'assignsecondary.php?action=edit&uid='.$uid.'&id='.$sndg['id']),*/
  array('title' => 'Revoke','href' => 
'assignsecondary.php?action=del&uid='.$uid.'&id='.$sndg['id'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $sndg['id'],
      "name" => $sndg['title'],
      "edit" => RenderActions($actions,1),
		);
  $badgecount++;
}
$pagebar['title'] = 'Assign Secondary Group';
$pagebar['actions'] = array(
    array('title' => 'Assign Group','href' => 'assignsecondary.php?action=new&uid='.$uid),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $s =
request_variables(array('badge_id','badge_var'));


if ($r['action']=="edit" && $id > 0) {

if(      $sql->prepare('UPDATE user_groupf SET 
badge_id=?,badge_var=?,user_id=? WHERE user_id=? AND id=?;', array(
$s['badge_id'],
$s['badge_var'],
$uid,
$id,
)
)){
      $pagebar['message'] = "Secondary groups successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update secondary groups.";

}
}

elseif ($r['action']=="new"){
if((is_root_gid($s['badge_id']) || !can_edit_group_assets($s['badge_id']) && $s['badge_id']!=$loguser['group_id']) && !has_perm('no-restrictions')) $pagebar['message'] = "You do not have the permissions to assign this group.";
else if (      $sql->prepare('INSERT INTO user_group SET
user_id=?,group_id=?,sortorder=? ;', array(
$uid,
$s['badge_id'],
0,
)
)) {
$id = $sql->insertid();
$r['action'] = "edit";
      $pagebar['message'] .= "Group successfully added.";
}
else {
 $pagebar['message'] = "Unable to assign user to group.";
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Assigned Groups','href' => 'assignsecondary.php?uid='.$uid),
    );


if ($id > 0) {
    $t=$sql->fetchp('SELECT * FROM user_group WHERE user_id=? AND group_id=?',array($uid), array($id));
$pagebar['title'] = $t['name'];
$pagebar['actions'] = array(
    array('title' => 'Delete Badge','href' => 
'assignsecondary.php?action=del&uid='.$uid.'&id='.$id, 
'confirm' 
=> 
true),
);

}
else {
$pagebar['title'] = 'Assign Group';
/*$s['image'],
$s['priority'],
$s['type'],
$s['name'],
$s['desc'],
$s['inherit'],
$s['posttext'],
$s['effect_variable'],
--
      "id" => $sndg['id'],
      "image" => "<img src=\"".$pic."\" alt=\"\" />",
      "name" => $sndg['name'],
      "desc" => $sndg['desc'],
      "type" => $sndg['type'],
      "priority" => $sndg['priority'],
*/

$t = array(
  'group_id' => '',
);
}
RenderPageBar($pagebar);

$allbdg = array();
$qallbadges = $sql->query("SELECT `id`, `title` FROM `group`");

while ($allbdgquery= $sql->fetch($qallbadges))
{ 
  $allbdg[$allbdgquery['id']]= $allbdgquery['title'];

}

$form = array(
  'action' =>
    urlcreate('assignsecondary.php', array(
      'action' => $r['action'],
      'uid' => $r['uid'],
      'id' => $t['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Secondary Group Assign',
      'fields' => array(
        'badge_id' => array(
          'title' => 'Secondary Group',
          'type' => 'dropdown',
          'choices' => $allbdg,
'value' => $t['badge_id'],
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($id>0)?'Update metadata':'Assign Group',
          'type' => 'submit',
        ),
      ),
    ),    
  ),
);

RenderForm($form);

}


pagefooter();

?>
