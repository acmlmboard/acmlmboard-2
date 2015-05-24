<?php

require("lib/common.php");

  $r = request_variables(array('id','action','act'));
  $pagebar = array();
  checknumeric($r['id']);

  if(!has_perm('edit-sprites')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Edit Sprites");
  
  $spritecateg = array();
  $qspritecateg = $sql->query("SELECT `id`, `name` FROM `spritecateg`");
  
  while ($allspcquery= $sql->fetch($qspritecateg))
  { 
    $spritecateg[$allspcquery['id']]= $allspcquery['name'];
  
  }
  $id = $r['id'];

  if ($r['action'] == "del") {
    unset($r['action']);
    if ($id > 0) {
        $sprite=$sql->fetchp('SELECT * FROM sprites WHERE id=?',array($id));
        if (!$sprites) $pagebar['message'] = "Unable to delete sprite: invalid sprite ID.";
     else if ($sql->prepare('DELETE FROM sprites WHERE id=?',array($id))) {
      $pagebar['message'] = "Sprite successfully deleted.";
 }
else {
 $pagebar['message'] = "Unable to delete sprite.";
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
	"img" => array("caption"=>"Image", "width"=>"32px", "color"=>2),
	"name" => array("caption"=>"Name", "align"=>"center", "color"=>1),
  "franchiseid" => array("caption"=>"Franchise", "align"=>"center", "color"=>2),
	"flavor" => array("caption"=>"Description", "color"=>1),
	"rarity" => array("caption"=>"Rarity", "color"=>2),
  "edit" => array("caption"=>"Actions","color"=>1),
);

$data = array();
$monReq = $sql->query("SELECT * FROM sprites ORDER BY id ASC");
while($mon = $sql->fetch($monReq))
{
		$pics = explode("|", $mon['pic']);
		$pic = $pics[0];
$actions = array(
  array('title' => 'Edit','href' => 
'editsprites.php?action=edit&id='.$mon['id']),
  array('title' => 'Delete','href' => 
'editsprites.php?action=del&id='.$mon['id'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $mon['id'],
			"img" => "<img src=\"img/sprites/".$pic."\" title=\"".$mon['title']."\" alt=\"\" />",
			"name" => $mon['name'],
      "franchiseid" => $spritecateg[$mon['franchiseid']],
			"flavor" => $mon['flavor'],
			"rarity" => $mon['rarity'],
      "edit" => RenderActions($actions,1),
		);
}
$pagebar['title'] = 'Edit Sprites';
$pagebar['actions'] = array(
    array('title' => 'New Sprite','href' => 'editsprites.php?action=new'),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $s =
request_variables(array('name','franchiseid','pic','alt','anchor','title','flavor','rarity'));


if ($r['action']=="edit" && $id > 0) {

if(      $sql->prepare('UPDATE sprites SET 
name=?,franchiseid=?,pic=?,alt=?,anchor=?,title=?,flavor=?,rarity=? WHERE id=?;', array(
$s['name'],
$s['franchiseid'],
$s['pic'],
$s['alt'],
$s['anchor'],
$s['title'],
$s['flavor'],
$s['rarity'],
$id,
)
)){
      $pagebar['message'] = "Sprite successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update  sprite.";

}
}

elseif ($r['action']=="new"){
if (      $sql->prepare('INSERT INTO sprites SET
name=?,franchiseid=?,pic=?,alt=?,anchor=?,title=?,flavor=?,rarity=? ;', array(
$s['name'],
$s['franchiseid'],
$s['pic'],
$s['alt'],
$s['anchor'],
$s['title'],
$s['flavor'],
$s['rarity'],
)
)) {
$id = $sql->insertid();
$r['action'] = "edit";
      $pagebar['message'] = "Sprite successfully created.";
}
else {
 $pagebar['message'] = "Unable to create sprite.";
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Sprites','href' => 'editsprites.php'),
    );


if ($id > 0) {
    $t=$sql->fetchp('SELECT * FROM sprites WHERE id=?',array($id));
  if (!$t) { noticemsg("Error", "Invalid sprite ID"); pagefooter(); die();
  } else {
$pagebar['title'] = $t['name'];
$pagebar['actions'] = array(
    array('title' => 'Delete Sprite','href' => 
'editsprites.php?action=del&id='.$id, 
'confirm' 
=> 
true),
);
  }

}
else {
$pagebar['title'] = 'New Sprite';
$t = array(
  'id' => 0,
  'name' => '',
  'franchiseid' => 1,
  'pic' => '',
  'alt' => '',
  'anchor' => 'free',
  'flavor' => '',  
  'rarity' => '0',  
);
}
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editsprites.php', array(
      'action' => $r['action'],
      'id' => $t['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Sprite Metadata',
      'fields' => array(
        'name' => array(
          'title' => 'Name',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => $t['name'],
        ),
        'franchiseid' => array(
          'title' => 'Franchise ID',
/*          'type' => 'numeric',
          'length' => 4,*/
          'type' => 'dropdown',
          'choices' => $spritecateg,
'value' => $t['franchiseid'],        
	),
        'rarity' => array(
          'title' => 'Rarity',
          'type' => 'numeric',
          'length' => 2,
'value' => $t['rarity'],
        ),
        'pic' => array(
          'title' => 'Image',
          'type' => 'imgref',
'value' => $t['pic'],
        ),
        'alt' => array(
          'title' => 'Alternate Image',
          'type' => 'imgref',
'value' => $t['alt'],
        ),
        'anchor' => array(
          'title' => 'Anchor',
          'type' => 'dropdown',
          'choices' => array(
              'free' => 'Free',
              'sidepic' => 'Sidepic',
              'bottom' => 'Bottom',
	      'top' => 'Top',
              ),
'value' => $t['anchor'],
        ),
        'title' => array(
          'title' => 'Title',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => $t['title'],
        ),
        'flavor' => array(
          'title' => 'Description',
          'type' => 'text',
          'length' => 255,
          'size' => 80,
'value' => $t['flavor'],
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($id>0)?'Update metadata':'Create sprite',
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