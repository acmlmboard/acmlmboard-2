<?php

require("lib/spritelib.php");
require("lib/common.php");
  if($id=$_GET[id])
    checknumeric($id);
  else $id=0;

  pageheader("Edit Sprites");

  if(!acl("edit-sprites")) {
     print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    You do not have the permissions to do this.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
     die();
  }

  if($action=="") {


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
	"flavor" => array("caption"=>"Description", "color"=>2),
  "edit" => array("caption"=>"Actions","color"=>1),
);

$data = array();
$monReq = $sql->query("SELECT * FROM sprites ORDER BY id ASC");
while($mon = $sql->fetch($monReq))
{
		$pics = explode("|", $mon['pic']);
		$pic = $pics[0];
		$data[] = array
		(
			"id" => $mon['id'],
			"img" => "<img src=\"img/b2sprites/".$pic."\" title=\"".$mon['title']."\" alt=\"\" />",
			"name" => $mon['name'],
			"flavor" => $mon['flavor'],
      "edit" => '<a href="?action=edit&id='.$mon['id'].'">Edit</a>',
		);
}

RenderTable($data, $headers);


}
elseif ($action=="edit") {
if (isset($_POST['act'])) {

      $sql->query("UPDATE sprites SET 
      name='".addslashes($name)."', 
      franchiseid='".addslashes($franchiseid)."',
      pic='".addslashes($pic)."', 
      alt='".addslashes($alt)."', 
      anchor='".addslashes($anchor)."', 
      title='".addslashes($title)."', 
      flavor='".addslashes($flavor)."'
      WHERE id='".addslashes($id)."'");


}

    $t=$sql->fetchq("SELECT * FROM sprites WHERE id=$id");

    print "<a href=./>Main</a> - <a href='editsprites.php'>Edit Sprites</a> - 
$t[name]<br><br>";

$form = array(
  'action' =>
    urlcreate('editsprites.php', array(
      'action' => 'edit',
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
          'type' => 'numeric',
          'length' => 4,
'value' => $t['franchiseid'],
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
          'title' => 'Update metadata',
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
