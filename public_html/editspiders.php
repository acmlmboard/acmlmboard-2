<?php
//Based off of editsmilies.php

require("lib/common.php");

  $r = request_variables(array('id','action','act'));
  $pagebar = array();

  if(!has_perm('edit-spiders')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Edit Spiders");

  $id = $r['id'];

  if ($r['action'] == "del") {
    unset($r['action']);
        $bot=$sql->fetchp('SELECT * FROM robots WHERE bot_name=?',array($id));
        if (!$bot) $pagebar['message'] = "Unable to delete spider: invalid spider name.";
     else if ($sql->prepare('DELETE FROM robots WHERE bot_name=?',array($id))) {
      $pagebar['message'] = "Spider successfully deleted.";
 }
  }

  if(empty($r['action'])) {


$headers = array
(
	"id" => array //Entry key is used in $data to bind fields
	(
		"caption" => "Name",
		"align" => "center",
		"color" => 1
	),
	"agent" => array("caption"=>"Agent", "align"=>"center", "color"=>2),
  "edit" => array("caption"=>"Actions", "width"=>"100px", "color"=>1),
);

$data = array();
$spReq = $sql->query("SELECT * FROM robots");
while($sp = $sql->fetch($spReq))
{
$actions = array(
  array('title' => 'Edit','href' => 
'editspiders.php?action=edit&id='.$sp['bot_name']),
  array('title' => 'Delete','href' => 
'editspiders.php?action=del&id='.$sp['bot_name'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $sp['bot_name'],
			"agent" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $sp['bot_agent']),
      "edit" => RenderActions($actions,1),
		);
}
$pagebar['title'] = 'Edit Spiders';
$pagebar['actions'] = array(
    array('title' => 'New Spider','href' => 'editspiders.php?action=new'),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $s =
request_variables(array('bot_name','bot_agent'));


if ($r['action']=="edit" && $id) {

if (empty($s['bot_name']) || empty($s['bot_agent'])) {
      $pagebar['message'] = "The name and/or agent for this spider cannot be empty.";

} else {

if(      $sql->prepare('UPDATE robots SET 
bot_agent=?, bot_name=? WHERE bot_name=?;', array(
$s['bot_agent'], $s['bot_name'],
$id,
)
)){
      $pagebar['message'] = "Spider successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update Spider.";

}
}
}

elseif ($r['action']=="new"){
if (empty($s['bot_name']) || empty($s['bot_agent'])) {
      $pagebar['message'] = "The name and/or agent for this spider cannot be empty.";

} else {
if (      $sql->prepare('INSERT INTO robots SET
bot_agent=?, bot_name=? ;', array(
$s['bot_agent'], $s['bot_name'],
)
)) {
$r['action'] = "edit";
      $pagebar['message'] = "Spider successfully created.";
}
else {
 $pagebar['message'] = "Unable to create spider.";
}
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Spider','href' => 'editspiders.php'),
    );


if ($id) {
    $t=$sql->fetchp('SELECT * FROM robots WHERE bot_name=?',array($id));
  if (!$t && !$_POST['bot_name']) { noticemsg("Notice", "Invalid spider name"); pagefooter(); die();
  } else {
$pagebar['title'] = $t['bot_name'];
$pagebar['actions'] = array(
    array('title' => 'Delete Spider','href' => 
'editspiders.php?action=del&id='.$id, 
'confirm' 
=> 
true),
);
  }

}
else {
$pagebar['title'] = 'New Spider';

/*$t = array(
  'id' => $_POST['text'],
);*/
}
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editspiders.php', array(
      'action' => $r['action'],
      'id' => $t['bot_name'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Spider Metadata',
      'fields' => array(
        'bot_name' => array(
          'title' => 'Name',
          'type' => 'text',
          'length' => 255,
          'size' => 80,
'value' => $t['bot_name'],
        ),
        'bot_agent' => array(
          'title' => 'Agent',
          'type' => 'text',
          'length' => 255,
          'size' => 80,
'value' => $t['bot_agent'],
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($id)?'Update metadata':'Create spider',
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