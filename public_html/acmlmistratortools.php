<?php
require 'lib/common.php';
 
pageheader('Acmlmistrator Tools');
//Controls the disable registrations and lockdown settings - SquidEmpress
//Uses inspiration from Schezo's version in 1.92.08/Jul.

if (!has_perm('no-restrictions')) no_perm();

if($_POST[action]=="Apply changes") {
$sql->query("UPDATE misc SET intval='".$_POST[regdisable]."' WHERE field='regdisable'");
$sql->query("UPDATE misc SET intval='".$_POST[lockdown]."' WHERE field='lockdown'");
 
print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Changes saved!<br>
".        "    ".redirect("acmlmistratortools.php",'the Acmlmistrator Tools page')."
".        "$L[TBLend]
";
die(pagefooter());
}

$rtool  = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="regdisable"'); 
$ltool  = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="lockdown"');

print "<form action='acmlmistratortools.php' method='post' enctype='multipart/form-data'>
".        " $L[TBL1]>
".
           catheader('Acmlmistrator tools')."
".    fieldrow('Disable Registering', fieldoption('regdisable',$rtool['regdisable'],array('Enable', 'Disable')))."
".    fieldrow('Enable Lockdown', fieldoption('lockdown',$ltool['lockdown'],array('Do not set', 'Set')))."
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>$L[INPs]=action value='Apply changes'></td>
".        " $L[TBLend]
";

pagefooter();
?>