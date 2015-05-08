<?php
//uses editbadges.php

require("lib/common.php");

  $r = request_variables(array('id','action','act'));
  $pagebar = array();
  checknumeric($r['id']);

  if(!has_perm('edit-post-icons')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Edit Post Icons");

  $id = $r['id'];

  if ($r['action'] == "del") {
    unset($r['action']);
    if ($id > 0) {
        $posticon=$sql->fetchp('SELECT * FROM posticons WHERE id=?',array($id));
        if (!$posticon) $pagebar['message'] = "Unable to delete post icon: invalid post icon ID.";
     else if ($sql->prepare('DELETE FROM posticons WHERE id=?',array($id))) {
      $pagebar['message'] = "Post icon successfully deleted.";
 }
else {
 $pagebar['message'] = "Unable to delete post icon.";
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
	"url" => array("caption"=>"URL", "align"=>"center", "color"=>1),
  "edit" => array("caption"=>"Actions", "width"=>"100px", "color"=>2),
);

$data = array();
$piReq = $sql->query("SELECT * FROM posticons ORDER BY id ASC");
while($pi = $sql->fetch($piReq))
{
		$pics = explode("|", $pi['url']);
		$pic = $pics[0];
$actions = array(
  array('title' => 'Edit','href' => 
'editposticons.php?action=edit&id='.$pi['id']),
  array('title' => 'Delete','href' => 
'editposticons.php?action=del&id='.$pi['id'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $pi['id'],
			"image" => "<img src=\"".$pic."\" alt=\"\" />",
			"url" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $pi['url']),
      "edit" => RenderActions($actions,1),
		);
}
$pagebar['title'] = 'Edit Post Icons';
$pagebar['actions'] = array(
    array('title' => 'New Post Icon','href' => 'editposticons.php?action=new'),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $s =
request_variables(array('url'));


if ($r['action']=="edit" && $id > 0) {

if(      $sql->prepare('UPDATE posticons SET 
url=? WHERE id=?;', array(
$s['url'],
$id,
)
)){
      $pagebar['message'] = "Post icon successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update Post icon.";

}
}

elseif ($r['action']=="new"){
if (      $sql->prepare('INSERT INTO posticons SET
url=? ;', array(
$s['url'],
)
)) {
$id = $sql->insertid();
$r['action'] = "edit";
      $pagebar['message'] = "Post icon successfully created.";
}
else {
 $pagebar['message'] = "Unable to create post icon.";
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Post Icon','href' => 'editposticons.php'),
    );


if ($id > 0) {
    $t=$sql->fetchp('SELECT * FROM posticons WHERE id=?',array($id));
  if (!$t) { noticemsg("Error", "Invalid post icon ID"); pagefooter(); die();
  } else {
$pagebar['title'] = 'Post Icon ID '.$t['id'];
$pagebar['actions'] = array(
    array('title' => 'Delete Post Icon','href' => 
'editposticons.php?action=del&id='.$id, 
'confirm' 
=> 
true),
);
  }

}
else {
$pagebar['title'] = 'New Post Icon';

$t = array(
  'id' => 0,
);
}
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editposticons.php', array(
      'action' => $r['action'],
      'id' => $t['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Post Icon Metadata',
      'fields' => array(
        'url' => array(
          'title' => 'URL',
          'type' => 'imgref',
'value' => $t['url'],
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($id>0)?'Update metadata':'Create post icon',
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