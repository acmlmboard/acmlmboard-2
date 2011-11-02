<?php

require("lib/spritelib.php");
require("lib/common.php");

  $r = request_variables(array('id','action','act'));

  checknumeric($r['id']);

  pageheader("Edit Sprites");

  acl_or_die("edit-sprites");

  if(empty($r['action']) || $r['id'] == 0) {


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
elseif ($r['action']=="edit") {

if (!empty($r['act'])) {

      $s = 
request_variables(array('name','franchiseid','pic','alt','anchor','title','flavor'));

      $sql->prepare('UPDATE sprites SET 
name=?,franchiseid=?,pic=?,alt=?,anchor=?,title=?,flavor=? WHERE id=?;', array(
$s['name'],
$s['franchiseid'],
$s['pic'],
$s['alt'],
$s['anchor'],
$s['title'],
$s['flavor'],
$r['id'],
)
);

}

    $t=$sql->fetchp('SELECT * FROM sprites WHERE id=?',array($r['id']));

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
