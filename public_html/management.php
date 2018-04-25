<?php
$nourltracker=1;
require 'lib/common.php';

if (!has_perm('manage-board')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

pageheader('Management');

$mlinks = array();
$mlinks[] = array('url' => "updatethemes.php", 'title' => 'Update Themes');
if (has_perm("edit-forums")) 
  $mlinks[] = array('url' => "manageforums.php", 'title' => 'Manage forums');
if (has_perm("edit-ip-bans")) 
  $mlinks[] = array('url' => "ipbans.php", 'title' => 'Manage IP bans');
if (has_perm("edit-calendar-events")) 
  $mlinks[] = array('url' => "editevents.php", 'title' => 'Manage events');
if (has_perm("edit-smilies")) 
  $mlinks[] = array('url' => "editsmilies.php", 'title' => 'Manage Smilies');
if (has_perm("edit-sprites")) 
  $mlinks[] = array('url' => "editsprites.php", 'title' => 'Manage sprites');
if (has_perm("edit-sprites")) 
  $mlinks[] = array('url' => "editspritecategories.php", 'title' => 'Manage sprite categories');
if (has_perm("edit-badges")) 
  $mlinks[] = array('url' => "editbadges.php", 'title' => 'Manage badges');
if (has_perm("edit-groups")) 
  $mlinks[] = array('url' => "editgroups.php", 'title' => 'Manage groups');
if (has_perm("trash-users")) 
  $mlinks[] = array('url' => "trashuser.php", 'title' => 'Trash users');
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

pagefooter();
?>
