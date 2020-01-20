<?php
//Based off of editsmilies.php

require("lib/common.php");

  $r = request_variables(array('service','action','act'));
  $pagebar = array();
  if($config['extendedprofile']==0) error("Feature Disabled","Extended profile support is not enabled.<br> <a href=./>Back to main</a>");
  if(!has_perm('edit-profileext')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  pageheader("Edit Extended Profile Fields");

  $id = checkvar('r','service');

  if (checkvar('r','action') == "del") {
    unset($r['action']);
        $profileext=$sql->fetchp('SELECT * FROM profileext WHERE service=?',array($id));
        if (!$id) $pagebar['message'] = "Unable to delete extended profile field: invalid extended profile field ID.";
     else if ($sql->prepare('DELETE FROM profileext WHERE id=?',array($id))) {
      $pagebar['message'] = "Extended profile field successfully deleted.";
 }
  }

  if(empty($r['action'])) {


$headers = array
(
	"service" => array //Entry key is used in $data to bind fields
	(
		"caption" => "ID",
		"width" => "32px",
		"align" => "center",
		"color" => 1
	),
	"title" => array("caption"=>"Title",  "align"=>"center", "color"=>2),
	"sortorder" => array("caption"=>"Sortorder", "align"=>"center", "color"=>1),
	"fmt" => array("caption"=>"Format", "align"=>"center", "color"=>2),
	"description" => array("caption"=>"Description", "align"=>"center", "color"=>1),
	"icon" => array("caption"=>"Icon", "align"=>"center", "color"=>2),
	"validation" => array("caption"=>"Validation", "align"=>"center", "color"=>1),
	"example" => array("caption"=>"Example", "align"=>"center", "color"=>2),
	"extrafield" => array("caption"=>"Extra Field", "align"=>"center", "color"=>1),
	"parser" => array("caption"=>"Parser", "align"=>"center", "color"=>2),
  "edit" => array("caption"=>"Actions", "width"=>"100px", "color"=>1),
);

$data = array();
$exfReq = $sql->query("SELECT * FROM profileext");
while($exf = $sql->fetch($exfReq))
{
$actions = array(
  array('title' => 'Edit','href' => 
'editprofileext.php?action=edit&service='.$exf['service']),
  array('title' => 'Delete','href' => 
'editprofileext.php?action=del&service='.$exf['service'], 
'confirm' => true),
);
	
$format = array("%%%VAL%%%", "<", ">");
$format2 = array("<b><i>%%%VAL%%%</i></b>","&lt;","&gt;"); 	
$data[] = array
		(
			"id" => $exf['service'],
			"title" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $exf['title']),
			"sortorder" => $exf['sortorder'],
			"fmt" => str_replace($format, $format2, $exf['fmt']),
			"description" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $exf['description']),
			"icon" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $exf['icon']),
			"validation" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $exf['validation']),
			"example" => str_replace("%%%VAL%%%", "<b><i>%%%VAL%%%</i></b>", $exf['example']),
			"extrafield" => $exf['extrafield'],
			"parser" => $exf['parser'],
      "edit" => RenderActions($actions,1),
		);
}
$pagebar['title'] = 'Edit Extended Profile Fields';
$pagebar['actions'] = array(
    array('title' => 'New Extended Profile Field','href' => 'editprofileext.php?action=new'),
);
RenderPageBar($pagebar);
RenderTable($data, $headers);


}
elseif ($r['action']=="edit" || $r['action']=="new") {
if (!empty($r['act'])) {
      $s =
request_variables(array('service','title','sortorder','fmt','description','icon','validation','example','extrafield','parser',));


if ($r['action']=="edit" && $id) {

if (empty($s['service']) || empty($s['title']) || empty($s['fmt']) || empty($s['description']) || empty($s['validation']) || empty($s['example'])) {
      $pagebar['message'] = "The ID, title, sortorder, format, description, validation, example, and/or extra field for this extended profile field cannot be empty.";

} else {

if(      $sql->prepare('UPDATE profileext SET 
service=?, title=?, sortorder=?, fmt=?, description=?, icon=?, validation=?, example=?, extrafield=?, parser=? WHERE service=?;', array(
$s['service'], $s['title'], $s['sortorder'], $s['fmt'], $s['description'], $s['icon'], $s['validation'], $s['example'], $s['extrafield'], $s['parser'],
$id,
)
)){
      $pagebar['message'] = "Extended profile field successfully updated.";

}
else {
 $pagebar['message'] = "Unable to update Extended Profile Field.";

}
}
}

elseif ($r['action']=="new"){
if (empty($s['service']) || empty($s['title']) || empty($s['fmt']) || empty($s['description']) || empty($s['validation']) || empty($s['example'])) {
      $pagebar['message'] = "The ID, title, sortorder, format, description, validation, example, and/or extra field for this extended profile field cannot be empty.";

} else {
if(      $sql->prepare('INSERT INTO profileext SET 
service=?, title=?, sortorder=?, fmt=?, description=?, icon=?, validation=?, example=?, extrafield=?, parser=? ;', array(
$s['service'], $s['title'], $s['sortorder'], $s['fmt'], $s['description'], $s['icon'], $s['validation'], $s['example'], $s['extrafield'], $s['parser'],
)
)) {
$r['action'] = "edit";
      $pagebar['message'] = "Extended profile field successfully created.";
}
else {
 $pagebar['message'] = "Unable to create extended profile field.";
}
}
}
}
$pagebar['breadcrumb'] = array(
    array('title' => 'Edit Extended Profile Field','href' => 'editprofileext.php'),
    );


if ($id) {
    $t=$sql->fetchp('SELECT * FROM profileext WHERE service=?',array($id));
  if (!$t && !$_POST['service']) { noticemsg("Notice", "Invalid extended profile field ID"); pagefooter(); die();
  } else {
$pagebar['title'] = $t['title'];
$pagebar['actions'] = array(
    array('title' => 'Delete Extended Profile Field','href' => 
'editprofileext.php?action=del&id='.$id, 
'confirm' 
=> 
true),
);
  }

}
else {
$pagebar['title'] = 'New Extended Profile Field';

}
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editprofileext.php', array(
      'action' => checkvar('r','action'),
      'service' => checkvar('t','service'),
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Extended Profile Field Metadata',
      'fields' => array(
        'service' => array(
          'title' => 'Service ID',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => checkvar('t','service'),
        ),
        'title' => array(
          'title' => 'Title',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => checkvar('t','title'),
        ),
        'sortorder' => array(
          'title' => 'Sortorder',
          'type' => 'numeric',
          'length' => 2,
'value' => checkvar('t','sortorder'),
        ),
        'fmt' => array(
          'title' => 'Format',
          'type' => 'text',
          'length' => 255,
          'size' => 80,
'value' => checkvar('t','fmt'),
        ),
        'description' => array(
          'title' => 'Description',
          'type' => 'text',
          'length' => 255,
          'size' => 80,
'value' => checkvar('t','description'),
        ),
        'icon' => array(
          'title' => 'Icon',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => checkvar('t','icon'),
        ),
        'validation' => array(
          'title' => 'Validation',
          'type' => 'text',
          'length' => 255,
          'size' => 80,
'value' => checkvar('t','validation'),
        ),
        'example' => array(
          'title' => 'Example',
          'type' => 'text',
          'length' => 60,
          'size' => 40,
'value' => checkvar('t','example'),
        ),
        'extrafield' => array(
          'title' => 'Extra Field',
          'type' => 'numeric',
          'length' => 2,
'value' => checkvar('t','extrafield'),
        ),
        'parser' => array(
          'title' => 'Parser',
          'type' => 'dropdown',
          'choices' => array(
              '' => '',
              'email' => 'email',
              ),
'value' => checkvar('t','parser'),
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($id)?'Update metadata':'Create extended profile field',
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