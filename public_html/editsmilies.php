<?php
//Based off of editposticons.php

require("lib/common.php");

  $r = request_variables(array('id','action','act'));
  $pagebar = array();

  if(!has_perm('edit-smilies')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Edit Smilies");

  $id = $r['id'];

  if ($r['action'] == "del") {
    unset($r['action']);
        $smiliey=$sql->fetchp('SELECT * FROM smilies WHERE text=?',array($id));
        if (!$id) $pagebar['message'] = "Unable to delete smiley: invalid smiley code.";
     else if ($sql->prepare('DELETE FROM smilies WHERE text=?',array($id))) {
      $pagebar['message'] = "Smiley successfully deleted.";
 }
  }

  if(empty($r['action'])) {


$headers = array
(
	"id" => array //Entry key is used in $data to bind fields
	(
		"caption" => "Text",
		"width" => "32px",
		"align" => "center",
		"color" => 1
	),
	"image" => array("caption"=>"Image", "width"=>"24px", "align"=>"center", "color"=>2),
	"url" => array("caption"=>"URL", "align"=>"center", "color"=>1),
  "edit" => array("caption"=>"Actions", "width"=>"100px", "color"=>2),
);

$data = array();
$smReq = $sql->query("SELECT * FROM smilies");
while($sm = $sql->fetch($smReq))
{
		$pics = explode("|", $sm['url']);
		$pic = $pics[0];
$actions = array(
  array('title' => 'Edit','href' => 
'editsmilies.php?action=edit&id='.$sm['text']),
  array('title' => 'Delete','href' => 
'editsmilies.php?action=del&id='.$sm['text'], 
confirm => true),
);
		
$data[] = array
		(
			"id" => $sm['text'],
			"image" => "<img src=\"".$pic."\" alt=\"\" />",
			"url" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $sm['url']),
      "edit" => RenderActions($actions,1),
		);
}
$pagebar['title'] = 'Edit Smilies';
$pagebar['actions'] = array(
    array('title' => 'New Smiley','href' => 'editsmilies.php?action=new'),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $s =
request_variables(array('url','text'));


if ($r['action']=="edit" && $id) {

if (empty($s['text']) || empty($s['url'])) {
      $pagebar['message'] = "The code and/or url for this smiley cannot be empty.";

} else {

if(      $sql->prepare('UPDATE smilies SET 
url=?, text=? WHERE text=?;', array(
$s['url'], $s['text'],
$id,
)
)){
      $pagebar['message'] = "Smiley successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update Smiley.";

}
}
}

elseif ($r['action']=="new"){
if (empty($s['text']) || empty($s['url'])) {
      $pagebar['message'] = "The code and/or url for this smiley cannot be empty.";

} else {
if (      $sql->prepare('INSERT INTO smilies SET
url=?, text=? ;', array(
$s['url'], $s['text'],
)
)) {
$r['action'] = "edit";
      $pagebar['message'] = "Smiley successfully created.";
}
else {
 $pagebar['message'] = "Unable to create smiley.";
}
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Smiley','href' => 'editsmilies.php'),
    );


if ($id) {
    $t=$sql->fetchp('SELECT * FROM smilies WHERE text=?',array($id));
  if (!$t && !$_POST['text']) { noticemsg("Notice", "Invalid smiley code"); pagefooter(); die();
  } else {
$pagebar['title'] = $t['text'].' Smiley';
$pagebar['actions'] = array(
    array('title' => 'Delete Smiley','href' => 
'editsmilies.php?action=del&id='.$id, 
'confirm' 
=> 
true),
);
  }

}
else {
$pagebar['title'] = 'New Smiley';

}
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editsmilies.php', array(
      'action' => $r['action'],
      'id' => $t['text'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Smiley Metadata',
      'fields' => array(
        'text' => array(
          'title' => 'Code',
          'type' => 'text',
          'length' => 10,
          'size' => 7,
'value' => $t['text'],
        ),
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
          'title' => ($id)?'Update metadata':'Create smiley',
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