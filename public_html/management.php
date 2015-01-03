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
  
$mlinkstext = '';
foreach ($mlinks as $l)
	$mlinkstext .= ($mlinkstext?' | ':'')."<a href=\"{$l['url']}\">{$l['title']}</a>";

//Inspired by Tierage's dashboard.php in Blargboard Plus.
//Sorta hackish code for management buttons. I commented out $mlinkstexts as buttons may eventually be implemented into $mlinkstext. But for now, this code works. - SquidEmpress
print "$L[TBL1]>
".    "  $L[TRh]>$L[TD]>Board management tools
".    "  $L[TR]>$L[TD1c]>
".    /*"    <br>
".    "    $mlinkstext<br>
".    "    <br>
".    */"
<a href=\"updatethemes.php\"</a>$L[INPs]=action value='Update Themes'></a> 
".(has_perm("edit-forums") ? " <a href=\"manageforums.php\"</a>$L[INPs]=action value='Manage Forums'></a>" : "")." 
".(has_perm("edit-ip-bans") ? " <a href=\"ipbans.php\"</a>$L[INPs]=action value='Manage IP Bans'></a>" : "")."
".(has_perm("edit-sprites") ? " <a href=\"editsprites.php\"</a>$L[INPs]=action value='Manage Sprites'></a>" : "")." 
".(has_perm("edit-badges") ? " <a href=\"editbadges.php\"</a>$L[INPs]=action value='Manage Badges'></a>" : "")." 
".(has_perm("edit-groups") ? " <a href=\"editgroups.php\"</a>$L[INPs]=action value='Manage Groups'></a>" : "")."
".(has_perm("admin-tools-access") ? " <a href=\"administratortools.php\"</a>$L[INPs]=action value='Administrator Tools'></a>"  : "")."
".    "$L[TBLend]
";

// TODO implement some global settings panel in this page so it doesn't feel empty

pagefooter();
?>
