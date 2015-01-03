<?php
require 'lib/common.php';
 
pageheader('Administrator Tools');
//Controls the disable registrations and lockdown settings - SquidEmpress
//Uses inspiration from Schezo's version in 1.92.08/Jul.

//Renamed 'Administrator Tools' as non root admins could be given lockdown access per perm system 

if (!has_perm('admin-tools-access')) no_perm();

if($_POST[action]=="Apply changes") {
$sql->query("UPDATE misc SET intval='".$_POST[regdisable]."' WHERE field='regdisable'");
$sql->query("UPDATE misc SET intval='".$_POST[lockdown]."' WHERE field='lockdown'");
 
print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Changes saved!<br>
".        "    ".redirect("administratortools.php",'the Administrator Tools page')."
".        "$L[TBLend]
";
die(pagefooter());
}

$rtool  = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="regdisable"'); 
$ltool  = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="lockdown"');

print "<form action='administratortools.php' method='post' enctype='multipart/form-data'>
".        " $L[TBL1]>
".
           catheader('Administrator tools')."
".    fieldrow('Disable Registering', fieldoption('regdisable',$rtool['regdisable'],array('Enable', 'Disable')))."
".    fieldrow('Enable Lockdown', fieldoption('lockdown',$ltool['lockdown'],array('Do not set', 'Set')))."
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>$L[INPs]=action value='Apply changes'></td>
".        " $L[TBLend]
";

pagefooter();
?>