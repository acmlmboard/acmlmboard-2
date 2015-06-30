<?php

require("lib/common.php");

  $r = request_variables(array('id','action','act'));
  $pagebar = array();
  checknumeric($r['id']);

  if(!has_perm('edit-sprites')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Edit Sprite Categories");

  $spritecateg = array();
  $qspritecateg = $sql->query("SELECT `id`, `name` FROM `spritecateg`");
  
  while ($allspcquery= $sql->fetch($qspritecateg))
  { 
    $spritecateg[$allspcquery['id']]= $allspcquery['name'];
  
  }
  $id = $r['id'];

 if(empty($r['action'])){

$headers = array
(
	"id" => array
	(
		"caption" => "#",
		"width" => "32px",
		"align" => "center",
		"color" => 1
	),
	"name" => array("caption"=>"Name", "align"=>"center", "color"=>1),
"edit" => array("caption"=>"Actions","color"=>1),
);

$data = array();
$scReq = $sql->query("SELECT * FROM spritecateg ORDER BY id ASC");
while($sc = $sql->fetch($scReq))
{
$actions = array(
  array('title' => 'Edit','href' => 
'editspritecategories.php?action=edit&id='.$sc['id']),
  array('title' => 'Delete','href' => 
'editspritecategories.php?action=del&id='.$sc['id'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $sc['id'],
			"name" => $sc['name'],
"edit" => RenderActions($actions,1),
		);
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Main','href' => './'), array('title' => 'Management','href' => 'management.php'),
    );
$pagebar['title'] = 'Edit Sprite Categories';
$pagebar['actions'] = array(
    array('title' => 'New Sprite Category','href' => 'editspritecategories.php?action=new'),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);
}

  if ($r['action'] == "del") {
    unset($r['action']);
        $spritecategory=$sql->fetchp('SELECT * FROM spritecateg WHERE id=?',array($id));
        if (!$spritecategory) noticemsg("Error","Unable to delete sprite category: invalid sprite category ID.");
     else if ($sql->prepare('DELETE FROM spritecateg WHERE id=?',array($id))) {
      $pagebar['message'] = "Sprite category successfully deleted.";
 }
else {
 $pagebar['message'] = "Unable to delete sprite category.";
}
  }

 elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $spca =
request_variables(array('name'));


if ($r['action']=="edit") {

if(      $sql->prepare('UPDATE spritecateg SET 
name=? WHERE id=?;', array(
$spca['name'],
$id,
)
)){
      $pagebar['message'] = "Sprite category successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update sprite category.";

}
}

elseif ($r['action']=="new"){
if (      $sql->prepare('INSERT INTO spritecateg SET
name=? ;', array(
$spca['name'],
)
)) {
$id = $sql->insertid();
$r['action'] = "edit";
      $pagebar['message'] = "Sprite category successfully created.";
}
else {
 $pagebar['message'] = "Unable to create sprite category.";
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Main','href' => './'), array('title' => 'Management','href' => 'management.php'), array('title' => 'Edit Sprite Categories','href' => 'editspritecategories.php'),
    );

   if ($id != 0) { 
   $tsc=$sql->fetchp('SELECT * FROM spritecateg WHERE id=?',array($id));
  if (!$tsc) { noticemsg("Error", "Invalid sprite category ID"); pagefooter(); die();
  } else {
$pagebar['title'] = $tsc['name'];
$pagebar['actions'] = array(
    array('title' => 'Delete Sprite Category','href' => 
'editspritecategories.php?action=del&id='.$id, 
'confirm' 
=> 
true),
);
  }

}
else {
$pagebar['title'] = 'New Sprite Category';
$tsc = array(
  'id' =>-2,
  'name' => '',
);
}
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editspritecategories.php', array(
      'action' => $r['action'],
      'id' => $tsc['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Sprite Category Name',
      'fields' => array(
        'name' => array(
          'title' => 'Name',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => $tsc['name'],
        ),
),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          //'title' => 'Update metadata':'Create sprite category',
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