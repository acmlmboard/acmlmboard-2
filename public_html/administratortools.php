<?php
require 'lib/common.php';

//Controls board settings - SquidEmpress
//Uses inspiration from Schezo's version in 1.92.08/Jul.

//Renamed 'Administrator Tools' as non root admins could be given lockdown access per perm system 

if (!has_perm('admin-tools-access')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

if($_POST[action]=="Apply changes") {
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[regdisable])."' WHERE field='regdisable'");
$sql->query("UPDATE misc SET txtval='".$sql->escape($_POST[regdisabletext])."' WHERE field='regdisable'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[lockdown])."' WHERE field='lockdown'");
$sql->query("UPDATE misc SET txtval='".$sql->escape($_POST[txtval])."' WHERE field='lockdown'");
$sql->query("UPDATE misc SET emailaddress='".$sql->escape($_POST[emailaddress])."' WHERE field='boardemail'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[trashid])."' WHERE field='trashid'");
$sql->query("UPDATE misc SET txtval='".$sql->escape($_POST[boardtitle])."' WHERE field='boardtitle'");
$sql->query("UPDATE misc SET txtval='".$sql->escape($_POST[defaulttheme])."' WHERE field='defaulttheme'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[defaultfontsize])."' WHERE field='defaultfontsize'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[avatardimx])."' WHERE field='avatardimx'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[avatardimy])."' WHERE field='avatardimy'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[topposts])."' WHERE field='topposts'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[topthreads])."' WHERE field='topthreads'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[threadprevnext])."' WHERE field='threadprevnext'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[memberlistcolorlinks])."' WHERE field='memberlistcolorlinks'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[badgesystem])."' WHERE field='badgesystem'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[spritesystem])."' WHERE field='spritesystem'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[extendedprofile])."' WHERE field='extendedprofile'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[displayname])."' WHERE field='displayname'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[perusercolor])."' WHERE field='perusercolor'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[usernamebadgeeffects])."' WHERE field='usernamebadgeeffects'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[useshadownccss])."' WHERE field='useshadownccss'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[nickcolorcss])."' WHERE field='nickcolorcss'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[userpgnum])."' WHERE field='userpgnum'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[userpgnumdefault])."' WHERE field='userpgnumdefault'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[alwaysshowlvlbar])."' WHERE field='alwaysshowlvlbar'");
$sql->query("UPDATE misc SET intval='".$sql->escape($_POST[rpglvlbarwidth])."' WHERE field='rpglvlbarwidth'");
$sql->query("UPDATE misc SET txtval='".$sql->escape($_POST[atnname])."' WHERE field='atnname'");
 
header('Location: administratortools.php');
}

pageheader('Administrator Tools');

$rtool  = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="regdisable"'); 
$ltool  = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="lockdown"');
$regdisabletext = $sql->resultq("SELECT txtval FROM misc WHERE field='regdisable'");
$lockdowntext = $sql->resultq("SELECT txtval FROM misc WHERE field='lockdown'");
$boardemail = $sql->resultq("SELECT emailaddress FROM misc WHERE field='boardemail'");

$trashidint=$sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="trashid"');
$boardtitletext=$sql->resultq('SELECT `txtval` FROM `misc` WHERE `field`="boardtitle"');
$defaultthemetext=$sql->resultq('SELECT `txtval` FROM `misc` WHERE `field`="defaulttheme"');
$defaultfontsizeint=$sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="defaultfontsize"');
$avatardimxint=$sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="avatardimx"');
$avatardimyint=$sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="avatardimy"');
$topposts = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="topposts"');
$topthreads = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="topthreads"');
$memberlistcolorlinks = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="memberlistcolorlinks"');
$badgesystem = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="badgesystem"');
$spritesystem  = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="spritesystem"');
$extendedprofile =$sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="extendedprofile"');
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
$atnname  = $sql->resultq('SELECT `txtval` FROM `misc` WHERE `field`="atnname"');

print "<form action='administratortools.php' method='post' enctype='multipart/form-data'>
".        " $L[TBL1]>
".
           catheader('Administrator tools')."
".       "  $L[TRg]>
".       "    $L[TDh] colspan=6>General settings</td>
".        "  $L[TR]>
".        "    $L[TD1c]>Board Title:</td>
".        "      $L[TD2]>$L[INPt]='boardtitle' size='40' maxlength='255' value='".$boardtitletext."' class='right'></td>
".           fieldrow('Board Theme'           ,fieldselect('defaulttheme', $defaultthemetext, themelist()))."
".         "  $L[TR]>
".        "    $L[TD1c]>Trash Forum ID:</td>
".        "      $L[TD2]>$L[INPt]='trashid' size='3' maxlength='3' value='".$trashidint."' class='right'></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Board Font Size:</td>
".        "      $L[TD2]>$L[INPt]='defaultfontsize' size='3' maxlength='3' value='".$defaultfontsizeint."' class='right'></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Avatar X Dimension:</td>
".        "      $L[TD2]>$L[INPt]='avatardimx' size='3' maxlength='3' value='".$avatardimxint."' class='right'></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Avatar Y Dimension:</td>
".        "      $L[TD2]>$L[INPt]='avatardimy' size='3' maxlength='3' value='".$avatardimyint."' class='right'></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Projected Date Posts:</td>
".        "      $L[TD2]>$L[INPt]='topposts' size='7' maxlength='7' value='".$topposts."' class='right'></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Projected Date Threads:</td>
".        "      $L[TD2]>$L[INPt]='topthreads' size='7' maxlength='7' value='".$topthreads."' class='right'></td>
".    fieldrow('Memberlist Color Links', fieldoption('memberlistcolorlinks',$memberlistcolorlinks,array('Disable', 'Enable')))."
".    fieldrow('Badge System', fieldoption('badgesystem',$badgesystem,array('Disable', 'Enable')))."
".    fieldrow('Sprite System', fieldoption('spritesystem',$spritesystem,array('Disable', 'Enable')))."
".    fieldrow('Extended Profile Fields (DO NOT USE; EXPERIMENTAL)', fieldoption('extendedprofile',$extendedprofile,array('Disable', 'Enable')))."
".    fieldrow('Thread Prev Next Links', fieldoption('threadprevnext',$threadprevnext,array('Disable', 'Enable')))."
".    fieldrow('Displaynames', fieldoption('displayname',$displayname,array('Disable', 'Enable')))."
".    fieldrow('Custom Username Colors', fieldoption('perusercolor',$perusercolor,array('Disable', 'Enable')))."
".    fieldrow('Username Badge Effects', fieldoption('usernamebadgeeffects',$usernamebadgeeffects,array('Disable', 'Enable')))."
".    fieldrow('Username Shadow', fieldoption('useshadownccss',$useshadownccss,array('Disable', 'Enable')))."
".    fieldrow('Theme Username Colors', fieldoption('nickcolorcss',$nickcolorcss,array('Disable', 'Enable')))."
".    fieldrow('AB1.x Num Graphics', fieldoption('userpgnum',$userpgnum,array('Disable', 'Enable')))."
".    fieldrow('All Themes Num Graphics', fieldoption('userpgnumdefault',$userpgnumdefault,array('Disable', 'Enable')))."
".    fieldrow('EXP Level Bars', fieldoption('alwaysshowlvlbar',$alwaysshowlvlbar,array('Disable', 'Enable')))."
".        "  $L[TR]>
".        "    $L[TD1c]>EXP Bar Size:</td>
".        "      $L[TD2]>$L[INPt]='rpglvlbarwidth' size='3' maxlength='3' value='".$rpglvlbarwidth."' class='right'></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Attention Box Name:</td>
".        "      $L[TD2]>$L[INPt]='atnname' size='40' maxlength='255' value='".$atnname."' class='right'></td>
".       "  $L[TRg]>
".       "    $L[TDh] colspan=6>Miscellaneous settings</td>
".    fieldrow('Disable Registering', fieldoption('regdisable',$rtool['regdisable'],array('Enable', 'Disable')))."
".        "  $L[TR]>
".        "    $L[TD1c]>Disable Registering Message:</td>
".        "      $L[TD2]>$L[TXTa]='regdisabletext' rows=8 cols=120>".$regdisabletext."</textarea></td>
".    fieldrow('Enable Lockdown', fieldoption('lockdown',$ltool['lockdown'],array('Do not set', 'Set')))."
".        "  $L[TR]>
".        "    $L[TD1c]>Lockdown Message (Leave blank for default):</td>
".        "      $L[TD2]>$L[TXTa]='txtval' rows=8 cols=120>".$lockdowntext."</textarea></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Board Email:</td>
".        "      $L[TD2]>$L[INPt]='emailaddress' size='40' maxlength='60' value='".$boardemail."' class='right'></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>$L[INPs]=action value='Apply changes'></td>
".        " $L[TBLend]
";

pagefooter();
?>