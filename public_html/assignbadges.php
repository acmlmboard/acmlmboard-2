<?php
/* This uses a duplicate of editbadges.php as template. -Emuz */

require("lib/common.php");

  $r = request_variables(array('id', 'uid', 'action','act'));
  $pagebar = array();
  checknumeric($r['id']);
  checknumeric($r['uid']);


  if(!has_perm('edit-user-badges')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Assign User Badges");

  if(!isset($r['uid']) || $r['uid'] == 0)
  {
    noticemsg("Error", "No User Requested.<br> <a href=./>Back to main</a>");
    pagefooter();
    die();
  }
  $id = $r['id'];
  $uid = $r['uid'];


  if ($r['action'] == "del") {
    unset($r['action']);
    if ($id > 0) {
      if ($sql->prepare('DELETE FROM user_badges WHERE id=?',array($id))) {
      $pagebar['message'] = "Badge successfully deleted.";
 }
else {
 $pagebar['message'] = "Unable to remove badge.";
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
		"color" => 1
	),
	"image" => array("caption"=>"Image", "width"=>"24px", "align"=>"center", "color"=>2),
	"name" => array("caption"=>"Name", "width"=>"200px", "align"=>"center", "color"=>1),
	"description" => array("caption"=>"Description", "color"=>2),
	"type" => array("caption"=>"Type", "align" => "center", "color"=>1),
  "priority" => array("caption"=>"Priority", "align" => "center", "color"=>2),
  "edit" => array("caption"=>"Actions","color"=>1),
);

$data = array();
$bdgReq = $sql->query("SELECT * FROM `badges`
                       RIGHT JOIN `user_badges` ON `badges`.`id` = `user_badges`.`badge_id`
                       WHERE `user_badges`.`user_id`='$uid' ORDER BY `priority` DESC");
while($bdg = $sql->fetch($bdgReq))
{
		$pics = explode("|", $bdg['image']);
		$pic = $pics[0];
$actions = array(
  array('title' => 'Edit','href' => 
'assignbadges.php?action=edit&uid='.$uid.'&id='.$bdg['id']),
  array('title' => 'Delete','href' => 
'assignbadges.php?action=del&uid='.$uid.'&id='.$bdg['id'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $bdg['id'],
			"image" => "<img src=\"".$pic."\" alt=\"\" />",
      "name" => str_replace("%%%VAL%%%", $bdg['badge_var'], $bdg['name']),
      "description" => str_replace("%%%VAL%%%", $bdg['badge_var'], $bdg['description']),
			"type" => $bdg['type'],
      "priority" => $bdg['priority'],
      "edit" => RenderActions($actions,1),
		);
  $badgecount++;
}
$pagebar['title'] = 'Assign User Badges';
$pagebar['actions'] = array(
    array('title' => 'Assign Badge','href' => 'assignbadges.php?action=new&uid='.$uid),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $s =
request_variables(array('badge_id','badge_var'));


if ($r['action']=="edit" && $id > 0) {

if(      $sql->prepare('UPDATE user_badges SET 
badge_id=?,badge_var=?,user_id=? WHERE id=?;', array(
$s['badge_id'],
$s['badge_var'],
$uid,
$id,
)
)){
      $pagebar['message'] = "Badge successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update Badge.";

}
}

elseif ($r['action']=="new"){
if (      $sql->prepare('INSERT INTO user_badges SET
user_id=?,badge_id=?,badge_var=? ;', array(
$uid,
$s['badge_id'],
$s['badge_var'],
)
)) {
$id = $sql->insertid();
$r['action'] = "edit";
      $pagebar['message'] = "Badge successfully assigned.";
}
else {
 $pagebar['message'] = "Unable to assign badge.";
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Assigned Badges','href' => 'assignbadges.php?uid='.$uid),
    );


if ($id > 0) {
    $t=$sql->fetchp('SELECT * FROM user_badges WHERE id=?',array($id));
$pagebar['title'] = $t['name'];
$pagebar['actions'] = array(
    array('title' => 'Delete Badge','href' => 
'assignbadges.php?action=del&uid='.$uid.'&id='.$id, 
'confirm' 
=> 
true),
);

}
else {
$pagebar['title'] = 'Assign Badge';
/*$s['image'],
$s['priority'],
$s['type'],
$s['name'],
$s['desc'],
$s['inherit'],
$s['posttext'],
$s['effect_variable'],
--
      "id" => $bdg['id'],
      "image" => "<img src=\"".$pic."\" alt=\"\" />",
      "name" => $bdg['name'],
      "desc" => $bdg['desc'],
      "type" => $bdg['type'],
      "priority" => $bdg['priority'],
*/

$t = array(
  'badge_id' => '',
  'badge_var' => '',
);
}
RenderPageBar($pagebar);

$allbdg = array();
$qallbadges = $sql->query("SELECT `id`, `name` FROM `badges`");

while ($allbdgquery= $sql->fetch($qallbadges))
{ 
  $allbdg[$allbdgquery['id']]= str_replace("%%%VAL%%%", '*', $allbdgquery['name']);

}

$form = array(
  'action' =>
    urlcreate('assignbadges.php', array(
      'action' => $r['action'],
      'uid' => $r['uid'],
      'id' => $t['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Badge Metadata',
      'fields' => array(
        'badge_id' => array(
          'title' => 'Badge ID',
          'type' => 'dropdown',
          'choices' => $allbdg,
'value' => $t['badge_id'],
        ),
        'badge_var' => array(
          'title' => 'Badge Variable Information',
          'type' => 'text',
          'length' => 30,
          'size' => 40,
'value' => $t['badge_var'],
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($id>0)?'Update metadata':'Assign Badge',
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
