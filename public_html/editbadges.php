<?php
/* This uses a duplicate of editsprites.php as template. -Emuz */

require("lib/common.php");

  $r = request_variables(array('id','action','act'));
  $pagebar = array();
  checknumeric($r['id']);

  if(!has_perm('edit-badges')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Edit Badges");

  $id = $r['id'];

  if ($r['action'] == "del") {
    unset($r['action']);
    if ($id > 0) {
        $badge=$sql->fetchp('SELECT * FROM badges WHERE id=?',array($id));
        if (!$badge) $pagebar['message'] = "Unable to delete badge: invalid badge ID.";
     else if ($sql->prepare('DELETE FROM badges WHERE id=?',array($id))) {
      $pagebar['message'] = "Badge successfully deleted.";
 }
else {
 $pagebar['message'] = "Unable to delete badge.";
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
$bdgReq = $sql->query("SELECT * FROM badges ORDER BY id ASC");
while($bdg = $sql->fetch($bdgReq))
{
		$pics = explode("|", $bdg['image']);
		$pic = $pics[0];
$actions = array(
  array('title' => 'Edit','href' => 
'editbadges.php?action=edit&id='.$bdg['id']),
  array('title' => 'Delete','href' => 
'editbadges.php?action=del&id='.$bdg['id'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $bdg['id'],
			"image" => "<img src=\"".$pic."\" alt=\"\" />",
			"name" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $bdg['name']),//$bdg['name'],
			"description" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $bdg['description']),
			"type" => $bdg['type'],
      "priority" => $bdg['priority'],
      "edit" => RenderActions($actions,1),
		);
}
$pagebar['title'] = 'Edit Badges';
$pagebar['actions'] = array(
    array('title' => 'New Badge','href' => 'editbadges.php?action=new'),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $s =
request_variables(array('image','priority','type','name','description','inherit','posttext','effect'));


if ($r['action']=="edit" && $id > 0) {

if(      $sql->prepare('UPDATE badges SET 
image=?,priority=?,type=?,name=?,description=?,inherit=?,posttext=?,effect=? WHERE id=?;', array(
$s['image'],
$s['priority'],
$s['type'],
$s['name'],
$s['description'],
$s['inherit'],
$s['posttext'],
$s['effect'],
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
if (      $sql->prepare('INSERT INTO badges SET
image=?,priority=?,type=?,name=?,description=?,inherit=?,posttext=?,effect=? ;', array(
$s['image'],
$s['priority'],
$s['type'],
$s['name'],
$s['description'],
$s['inherit'],
$s['posttext'],
$s['effect'],
)
)) {
$id = $sql->insertid();
$r['action'] = "edit";
      $pagebar['message'] = "Badge successfully created.";
}
else {
 $pagebar['message'] = "Unable to create badge.";
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Badges','href' => 'editbadges.php'),
    );


if ($id > 0) {
    $t=$sql->fetchp('SELECT * FROM badges WHERE id=?',array($id));
  if (!$t) { noticemsg("Error", "Invalid badge ID"); pagefooter(); die();
  } else {
$pagebar['title'] = $t['name'];
$pagebar['actions'] = array(
    array('title' => 'Delete Badge','href' => 
'editbadges.php?action=del&id='.$id, 
'confirm' 
=> 
true),
);
  }

}
else {
$pagebar['title'] = 'New Badge';
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
  'id' => 0,
  'priority' => '10',
  'type' => 1,
  'name' => '',
  'desc' => '',
  'inherit' => '',
  'posttext' => '',  
  'effect' => '',  
);
}
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editbadges.php', array(
      'action' => $r['action'],
      'id' => $t['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Badge Metadata',
      'fields' => array(
        'name' => array(
          'title' => 'Name',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => $t['name'],
        ),
        'type' => array(
          'title' => 'Badge Type',
          'type' => 'dropdown',
          'choices' => array(
              '1' => '1',
              '2' => '2',
              '3' => '3',
              ),
'value' => $t['type'],
        ),
        'priority' => array(
          'title' => 'Priority',
          'type' => 'numeric',
          'length' => 3,
'value' => $t['priority'],
        ),
        'inherit' => array(
          'title' => 'Inherit from',
          'type' => 'numeric',
          'length' => 3,
'value' => $t['inherit'],
        ),
        'image' => array(
          'title' => 'Image',
          'type' => 'imgref',
'value' => $t['image'],
        ),
        'posttext' => array(
          'title' => 'Post Text',
          'type' => 'text',
          'length' => 3,
          'length' => 60,
          'size' => 40,
'value' => $t['posttext'],
        ),
        'effect' => array(
          'title' => 'Effect ID Name',
          'type' => 'text',
          'length' => 25,
          'size' => 30,
'value' => $t['effect'],
        ),
        'description' => array(
          'title' => 'Description',
          'type' => 'text',
          'length' => 255,
          'size' => 80,
'value' => $t['description'],
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($id>0)?'Update metadata':'Create badge',
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