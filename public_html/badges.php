<?php

/*Copied from sprites.php */
require("lib/common.php");

pageheader();
if(!isset($_GET['uid']))
{
    noticemsg("Error", "No User Requested.<br> <a href=./>Back to main</a>");
    pagefooter();
    die();
}
$userID = (int)$_GET['uid'];

$headers = array
(
	"id" => array //Entry key is used in $data to bind fields
	(
		"caption" => "#",
		"width" => "32px",
		"align" => "center",
		"hidden"=>true,
		"color" => 2
	),
	"image" => array("caption"=>"Image", "width"=>"25px", "align"=>"center", "color"=>1),
	"name" => array("caption"=>"Name", "align"=>"center", "color"=>2),
	"description" => array("caption"=>"Description", "color"=>1),
	"type" => array("caption"=>"Type", "align"=>"center", "color"=>2),

	//Hidden flag could be used for admin-only columns.
);

$data = array();
//$bdgReq = $sql->query("SELECT * FROM badges ORDER BY priority DESC");
$bdgReq = $sql->query("SELECT * FROM `badges`
                       RIGHT JOIN `user_badges` ON `badges`.`id` = `user_badges`.`badge_id`
                       WHERE `user_badges`.`user_id`='$userID' ORDER BY `priority` DESC");

while($bdg = $sql->fetch($bdgReq))
{

	$pics = explode("|", $bdg['image']);
	$pic = $pics[0];
	$data[] = array
	(
		"id" => $bdg['id'],
		"image" => "<img src=\"".$pic."\" alt=\"\" />",
		"name" => str_replace("%%%VAL%%%", $bdg['badge_var'], $bdg['name']),
		"description" => str_replace("%%%VAL%%%", $bdg['badge_var'], $bdg['description']),
		"type" => $bdg['type'],
	);

}

if(has_perm('edit-user-badges'))
	{
		$pagebar['actions'] = array(
    	array('title' => 'Assign User Badge','href' => 'assignbadges.php?uid='.$userID),
	);
	}
RenderPageBar($pagebar);
RenderTable($data, $headers);

pagefooter();

?>
