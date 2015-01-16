<?php
require 'lib/common.php';

//Alternative to editing users' profiles. Tempbanning is planned to be implemented - SquidEmpress
//Based off of banhammer.php from Blargboard by StapleButter.

$uid = $loguser['id'];
 
  if (isset($_GET['id'])) {
    $temp = $_GET['id'];
    if (checknumeric($temp))
      $uid = $temp;
  }

 if (!has_perm('ban-users'))
   {
     pageheader('No permission');
     no_perm();
   }
   
   //From editperms.php
   $id = (int)$_GET['id'];

    $tuser = $sql->fetchp("SELECT `group_id` FROM users WHERE id=?",array($id));
	if (is_root_gid($tuser[$u.'group_id']) && !has_perm('no-restrictions')) 
	{
		pageheader('No permission');
		no_perm();
	} 
 
   //From editprofile.php
   if(!$user['id'])
    {
     //use error($message, 0) function later!
     pageheader("Profile");
     print "<a href=\"./\">Main</a> - Profile<br><br>
            $L[TBL1]>
              $L[TD1c]>
            This user does not exist!
            $L[TBLend]";
     pagefooter();
     die();
    }

$bannedgroup = $sql->resultq("SELECT id FROM `group` WHERE `banned`=1");
$defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default`=1");

if (isset($_GET['unban']))
{
pageheader('Unban User');
}
else
{
pageheader('Ban User');
}

global $user;
 
  $user = $sql->fetchq("SELECT * FROM users WHERE `id` = $uid");

//Concatenation like in ABXD
if($_POST[banuser]=="Ban User") {
      $banreason="Banned permanently";
      if ($_POST['title']) {
      $banreason .= ': '.htmlspecialchars($_POST['title']);
      }

      $sql->query("UPDATE users SET group_id='$bannedgroup[id]' WHERE id='$user[id]'");
      $sql->query("UPDATE users SET title='$banreason' WHERE id='$user[id]'");

print "<form action='banhammer.php?id=$uid' method='post'>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "    User has been banned.<br>
".        "    ".redirect("profile.php?id=$user[id]",'the user')."
".        "$L[TBLend]
";
die(pagefooter());
    }

elseif($_POST[unbanuser]=="Unban User") {
if ($user['group_id'] != $bannedgroup['id'])
{
print
        "$L[TBL1]>
".      "  $L[TR2]>
".      "    $L[TD1c]>
".      "      This user is not a Banned User.<br> <a href=./>Back to main</a> 
".      "$L[TBLend]
";
      pagefooter();
      die();
}
      $sql->query("UPDATE users SET group_id='$defaultgroup[id]' WHERE id='$user[id]'");
      $sql->query("UPDATE users SET title='' WHERE id='$user[id]'");
      
print "<form action='banhammer.php?id=$uid' method='post'>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "    User has been unbanned.<br>
".        "    ".redirect("profile.php?id=$user[id]",'the user')."
".        "$L[TBLend]
";
die(pagefooter());
    }

if (isset($_GET['unban']))
{
$pagebar = array
  (
	  'breadcrumb' => array(array('href'=>'/.', 'title'=>'Main'), array('href'=>'index.php', 'title'=>'Forums')),
	  'title' => 'Unban User',
	  'actions' => array(),
  	  'message' => $errmsg
  );
}
else
{
$pagebar = array
  (
	  'breadcrumb' => array(array('href'=>'/.', 'title'=>'Main'), array('href'=>'index.php', 'title'=>'Forums')),
	  'title' => 'Ban User',
	  'actions' => array(),
  	  'message' => $errmsg
  );
}
RenderPageBar($pagebar);
  
if (isset($_GET['unban']))
{
print "<form action='banhammer.php?id=$uid' method='post' enctype='multipart/form-data'> 
".    "$L[TBL1]>
".    "  $L[TRh]>$L[TD]>Unban User
".    "  $L[TR]>$L[TD1c]>
".    "    <br>
".        "  $L[TR1]>
".        "    $L[TD1c]>
".        "      $L[INPs]=\"unbanuser\" value=\"Unban User\">
".    "$L[TBLend]
";
}
else
{
print "<form action='banhammer.php?id=$uid' method='post' enctype='multipart/form-data'> 
".    "$L[TBL1]>
".
        catheader('Ban User')."
".        "  $L[TR]>
".        "    $L[TD1c]>Reason:</td>
".        "      $L[TD2]>$L[INPt]='title' class='right'></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPs]=\"banuser\" value=\"Ban User\">
".    "$L[TBLend]
";
}

pagefooter();
?>