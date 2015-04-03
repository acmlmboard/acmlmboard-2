<?php

require("..lib/common.php");
pageheader();

if(!is_file("themes_serial.txt"))
{
	print "You should run updatethemes.php first.";
	pagefooter();
	die();
}


if(is_file("themes_have_been_done.txt"))
{
	print "Already ran.";
	pagefooter();
	die();
}

$sql->query("ALTER TABLE `users` CHANGE `theme` `theme` VARCHAR( 32 ) NOT NULL DEFAULT 'dailycycle2'");

$themes = array();
$sorted = array();
$t = $sql->query("select * from themes");
while($theme = $sql->fetch($t))
{
	$f = str_replace(".css", "", str_replace(".php", "", $theme['cssfile']));
	$themes[$theme['id']] = $f;
	//$sorted[$theme['ord']] = array($theme['name'], $f);
}

//file_put_contents("themes_serial.txt", serialize($sorted));

$users = $sql->query("select id, theme from users");
while($user = $sql->fetch($users))
{
	if(is_numeric($user['theme']))
		$sql->query("update users set theme = '".$themes[$user['theme']]."' where id = ".$user['id']);
}

file_put_contents("themes_have_been_done.txt", "Yes they have. This file only serves to prevent convertthemesettings.php from running twice, and can be removed along with the rest.");

print "All users have had their theme settings switched from integer (10) to string-based (\"Fish\"). This file can be deleted now, as can the themes table.";

pagefooter();

?>