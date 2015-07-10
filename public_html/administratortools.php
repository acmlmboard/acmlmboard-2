<?php

require 'lib/common.php';

//Controls board settings - SquidEmpress
//Uses inspiration from Schezo's version in 1.92.08/Jul.
//Renamed 'Administrator Tools' as non root admins could be given lockdown access per perm system 

if (!has_perm('admin-tools-access'))
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == "Apply changes") {
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['regdisable']) . "' WHERE field='regdisable'");
	$sql->query("UPDATE misc SET txtval='" . $sql->escape($_POST['regdisabletext']) . "' WHERE field='regdisable'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['lockdown']) . "' WHERE field='lockdown'");
	$sql->query("UPDATE misc SET txtval='" . $sql->escape($_POST['lockdowntext']) . "' WHERE field='lockdown'");
	$sql->query("UPDATE misc SET txtval='" . $sql->escape($_POST['boardemail']) . "' WHERE field='boardemail'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['trashid']) . "' WHERE field='trashid'");
	$sql->query("UPDATE misc SET txtval='" . $sql->escape($_POST['boardtitle']) . "' WHERE field='boardtitle'");
	$sql->query("UPDATE misc SET txtval='" . $sql->escape($_POST['defaulttheme']) . "' WHERE field='defaulttheme'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['defaultfontsize']) . "' WHERE field='defaultfontsize'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['avatardimx']) . "' WHERE field='avatardimx'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['avatardimy']) . "' WHERE field='avatardimy'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['topposts']) . "' WHERE field='topposts'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['topthreads']) . "' WHERE field='topthreads'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['threadprevnext']) . "' WHERE field='threadprevnext'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['memberlistcolorlinks']) . "' WHERE field='memberlistcolorlinks'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['badgesystem']) . "' WHERE field='badgesystem'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['spritesystem']) . "' WHERE field='spritesystem'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['extendedprofile']) . "' WHERE field='extendedprofile'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['displayname']) . "' WHERE field='displayname'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['perusercolor']) . "' WHERE field='perusercolor'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['usernamebadgeeffects']) . "' WHERE field='usernamebadgeeffects'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['useshadownccss']) . "' WHERE field='useshadownccss'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['nickcolorcss']) . "' WHERE field='nickcolorcss'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['userpgnum']) . "' WHERE field='userpgnum'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['userpgnumdefault']) . "' WHERE field='userpgnumdefault'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['alwaysshowlvlbar']) . "' WHERE field='alwaysshowlvlbar'");
	$sql->query("UPDATE misc SET intval='" . $sql->escape($_POST['rpglvlbarwidth']) . "' WHERE field='rpglvlbarwidth'");
	$sql->query("UPDATE misc SET txtval='" . $sql->escape($_POST['atnname']) . "' WHERE field='atnname'");

	header('Location: administratortools.php?u=1');
	//exit('You should have been redirected...');
}

pageheader('Administrator Tools');

$updated = isset($_GET['updated']) ? $_GET['updated'] : 0;
if($updated) {
	noticemsg("Success", "The settings have been updated.");
}

// I don't know what was going on with some of these settings... 
// but I've changed them to conform to the standard previously used for settings.
//
// It is possible to use the same field name to store an int and text value, however this
// could cause confusion.
$regdisable			= $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="regdisable"');
$regdisabletext		= $sql->resultq("SELECT `txtval` FROM `misc` WHERE field='regdisabletext'");
$lockdown			= $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="lockdown"');
$lockdowntext		= $sql->resultq("SELECT `txtval` FROM `misc` WHERE field='lockdowntext'");
$boardemail = $sql->resultq("SELECT `txtval` FROM `misc` WHERE field='boardemail'"); // Why was an entirely new email field added to the entire settings table?
$trashidint = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="trashid"');
$boardtitletext = $sql->resultq('SELECT `txtval` FROM `misc` WHERE `field`="boardtitle"');
$defaultthemetext = $sql->resultq('SELECT `txtval` FROM `misc` WHERE `field`="defaulttheme"');
$defaultfontsizeint = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="defaultfontsize"');
$avatardimxint = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="avatardimx"');
$avatardimyint = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="avatardimy"');
$topposts = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="topposts"');
$topthreads = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="topthreads"');
$memberlistcolorlinks = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="memberlistcolorlinks"');
$badgesystem = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="badgesystem"');
$spritesystem = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="spritesystem"');
$extendedprofile = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="extendedprofile"');
$threadprevnext = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="threadprevnext"');
$displayname = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="displayname"');
$perusercolor = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="perusercolor"');
$usernamebadgeeffects = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="usernamebadgeeffects"');
$useshadownccss = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="useshadownccss"');
$nickcolorcss = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="nickcolorcss"');
$userpgnum = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="userpgnum"');
$userpgnumdefault = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="userpgnumdefault"');
$alwaysshowlvlbar = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="alwaysshowlvlbar"');
$rpglvlbarwidth = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="rpglvlbarwidth"');
$atnname = $sql->resultq('SELECT `txtval` FROM `misc` WHERE `field`="atnname"');

print "<form action='administratortools.php' method='post' enctype='multipart/form-data'>
" . " <table cellspacing=\"0\" class=\"c1\">
" .
		catheader('Administrator tools') . "
" . "  <tr class=\"c\">
" . "    <td class=\"b h\" colspan=6>General settings</td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Board Title:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name=\"boardtitle\" size=\"40\" maxlength=\"255\" value=\"" . htmlentities($boardtitletext) . "\" class='right'></td>
" . fieldrow('Board Theme', fieldselect('defaulttheme', $defaultthemetext, themelist())) . "
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Trash Forum ID:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='trashid' size='3' maxlength='3' value='" . $trashidint . "' class='right'></td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Board Font Size:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='defaultfontsize' size='3' maxlength='3' value='" . $defaultfontsizeint . "' class='right'></td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Avatar X Dimension:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='avatardimx' size='3' maxlength='3' value='" . $avatardimxint . "' class='right'></td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Avatar Y Dimension:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='avatardimy' size='3' maxlength='3' value='" . $avatardimyint . "' class='right'></td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Projected Date Posts:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='topposts' size='7' maxlength='7' value='" . $topposts . "' class='right'></td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Projected Date Threads:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='topthreads' size='7' maxlength='7' value='" . $topthreads . "' class='right'></td>
" . fieldrow('Memberlist Color Links', fieldoption('memberlistcolorlinks', $memberlistcolorlinks, array('Disable', 'Enable'))) . "
" . fieldrow('Badge System', fieldoption('badgesystem', $badgesystem, array('Disable', 'Enable'))) . "
" . fieldrow('Sprite System', fieldoption('spritesystem', $spritesystem, array('Disable', 'Enable'))) . "
" . fieldrow('Extended Profile Fields (DO NOT USE; EXPERIMENTAL)', fieldoption('extendedprofile', $extendedprofile, array('Disable', 'Enable'))) . "
" . fieldrow('Thread Prev Next Links', fieldoption('threadprevnext', $threadprevnext, array('Disable', 'Enable'))) . "
" . fieldrow('Displaynames', fieldoption('displayname', $displayname, array('Disable', 'Enable'))) . "
" . fieldrow('Custom Username Colors', fieldoption('perusercolor', $perusercolor, array('Disable', 'Enable'))) . "
" . fieldrow('Username Badge Effects', fieldoption('usernamebadgeeffects', $usernamebadgeeffects, array('Disable', 'Enable'))) . "
" . fieldrow('Username Shadow', fieldoption('useshadownccss', $useshadownccss, array('Disable', 'Enable'))) . "
" . fieldrow('Theme Username Colors', fieldoption('nickcolorcss', $nickcolorcss, array('Disable', 'Enable'))) . "
" . fieldrow('AB1.x Num Graphics', fieldoption('userpgnum', $userpgnum, array('Disable', 'Enable'))) . "
" . fieldrow('All Themes Num Graphics', fieldoption('userpgnumdefault', $userpgnumdefault, array('Disable', 'Enable'))) . "
" . fieldrow('EXP Level Bars', fieldoption('alwaysshowlvlbar', $alwaysshowlvlbar, array('Disable', 'Enable'))) . "
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">EXP Bar Size:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='rpglvlbarwidth' size='3' maxlength='3' value='" . $rpglvlbarwidth . "' class='right'></td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Attention Box Name:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='atnname' size='40' maxlength='255' value='" . $atnname . "' class='right'></td>
" . "  <tr class=\"c\">
" . "    <td class=\"b h\" colspan=6>Miscellaneous settings</td>
" . fieldrow('Enable Registration', fieldoption('regdisable', $regdisable, array('Enable', 'Disable'))) . "
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Disable Registering Message:</td>
" . "      <td class=\"b n2\"><textarea wrap=\"virtual\" name='regdisabletext' rows=8 cols=120>" . $regdisabletext . "</textarea></td>
" . fieldrow('Enable Lockdown', fieldoption('lockdown', $lockdown, array('Do not set', 'Set'))) . "
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Lockdown Message (Leave blank for default):</td>
" . "      <td class=\"b n2\"><textarea wrap=\"virtual\" name='lockdowntext' rows=8 cols=120>" . $lockdowntext . "</textarea></td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Board Email:</td>
" . "      <td class=\"b n2\"><input type=\"text\" name='boardemail' size='40' maxlength='60' value='" . $boardemail . "' class='right'></td>
" . "  <tr class=\"n1\">
" . "    <td class=\"b\">&nbsp;</td>
" . "    <td class=\"b\"><input type=\"submit\" class=\"submit\" name=action value='Apply changes'></td>
" . " </table>
";

pagefooter();
?>