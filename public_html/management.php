<?php
require 'lib/common.php';

pageheader('Management');

if (!has_perm('manage-board')) no_perm();

$mlinks = array();
$mlinks[] = array('url' => "updatethemes.php", 'title' => 'Update Themes');
if (has_perm("edit-forums")) 
  $mlinks[] = array('url' => "manageforums.php", 'title' => 'Manage forums');
if (has_perm("edit-ip-bans")) 
  $mlinks[] = array('url' => "ipbans.php", 'title' => 'Manage IP bans');
if (has_perm("edit-sprites")) 
  $mlinks[] = array('url' => "editsprites.php", 'title' => 'Manage sprites');
if (has_perm("edit-badges")) 
  $mlinks[] = array('url' => "editbadges.php", 'title' => 'Manage badges');
if (has_perm("edit-groups")) 
  $mlinks[] = array('url' => "editgroups.php", 'title' => 'Manage groups');
if (has_perm("admin-tools-access")) 
  $mlinks[] = array('url' => "administratortools.php", 'title' => 'Administrator Tools');

//Inspired by Tierage's dashboard.php in Blargboard Plus. - SquidEmpress
$mlinkstext = '';
foreach ($mlinks as $l)
	$mlinkstext .= ($mlinkstext?' ':'')."<a href=\"{$l['url']}\"</a>$L[INPs]=action value='{$l['title']}'></a>";

print "$L[TBL1]>
".    "  $L[TRh]>$L[TD]>Board management tools
".    "  $L[TR]>$L[TD1c]>
".    "    <br>
".    "    $mlinkstext<br>
".    "    <br>
".    "$L[TBLend]
";

// TODO implement some global settings panel in this page so it doesn't feel empty

pagefooter();
?>
