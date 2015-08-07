<?php
require 'lib/common.php';

if (!has_perm('manage-board')) {
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
}

pageheader('Management');

$mlinks = array();
$mlinks[] = array('url' => "updatethemes.php", 
	'title' => 'Update Themes', 
	'desc' => 'Refresh, or disable existing themes.');

if (has_perm("edit-forums")) {
	$mlinks[] = array('url' => "manageforums.php", 
		'title' => 'Manage forums',
		'desc' => 'Add, remove, and modify forums and permissions.');
}
if (has_perm("edit-ip-bans")) {
	$mlinks[] = array('url' => "ipbans.php", 
		'title' => 'Manage IP bans',
		'desc' => 'Add, remove, and modify IP bans.');
}
if (has_perm("edit-spiders")) {
	$mlinks[] = array('url' => "editspiders.php", 
		'title' => 'Manage spiders', 
		'desc' => 'Add, remove, and modify user-agents for detecting web crawlers and spiders.');
}
if (has_perm("edit-calendar-events")) {
	$mlinks[] = array('url' => "editevents.php", 
		'title' => 'Manage events',
		'desc' => 'Add, remove, and modify board events.');
}
if (has_perm('edit-smilies')) {
	$mlinks[] = array('url' => "editsmilies.php",
		'title' => 'Manage smilies',
		'desc' => 'Add, remove, and modify post smilies.');
}
if (has_perm("edit-post-icons")) {
	$mlinks[] = array('url' => "editposticons.php", 
		'title' => 'Manage post icons',
		'desc' => 'Add, remove, and modify post icons.');
}
if (has_perm('edit-profileext')) {
	$mlinks[] = array('url' => "editprofileext.php", 'title' => 'Manage extended profile fields');
}
if (has_perm("edit-sprites")) {
	$mlinks[] = array('url' => "editsprites.php", 'title' => 'Manage sprites');
}
if (has_perm("edit-sprites")) {
	$mlinks[] = array('url' => "editspritecategories.php", 'title' => 'Manage sprite categories');
}
if (has_perm("edit-badges")) {
	$mlinks[] = array('url' => "editbadges.php", 'title' => 'Manage badges');
}
if (has_perm("edit-groups")) {
	$mlinks[] = array('url' => "editgroups.php", 'title' => 'Manage groups');
}
if (has_perm("admin-tools-access")) {
	$mlinks[] = array('url' => "administratortools.php", 'title' => 'Administrator Tools');
}

print "<table cellspacing=\"0\" class=\"c1\">
	<tr class=\"h\">
		<td class=\"b\">Board management tools</td>
	</tr>";
$i = 0;
foreach ($mlinks as $l) {
	$row = ($i++ % 2) == 0 ? 'n1' : 'n2';
	print "<tr><td class=\"b $row\">\n";
	print "<a href=\"{$l['url']}\">{$l['title']}</a><br />\n";
	if (isset($l['desc'])) {
		print "{$l['desc']}\n";
	}
	print "</td></tr>\n";
}
print "</table>";

pagefooter();
?>
