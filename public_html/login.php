<?php
  require 'lib/common.php';

  $postuser = addslashes($_POST[name]);
  $postpassword = addslashes($_POST[pass]);

  $act=$_POST[action];
  if(!$act){
    $print=" <form action=login.php method=post>
".         "  $L[TRh]>
".         "    $L[TDh] colspan=2>Login</td>
".         "  $L[TR]>
".         "    $L[TD1c] width=120>Username:</td>
".         "    $L[TD2]>$L[INPt]=name size=25 maxlength=25></td>
".         "  $L[TR]>
".         "    $L[TD1c]>Password:</td>
".         "    $L[TD2]>$L[INPp]=pass size=13 maxlength=32></td>
".         "  $L[TR1]>
".         "    $L[TD]>&nbsp;</td>
".         "    $L[TD]>$L[INPs]=action value=Login></td>
".         " </form>
";
  }elseif($act=='Login'){
    if($userid=checkuser($postuser,md5($pwdsalt2.$postpassword.$pwdsalt))){
      setcookie('user',$userid,2147483647);
      setcookie('pass',packlcookie(md5($pwdsalt2.$postpassword.$pwdsalt),implode(".",array_slice(explode(".",$_SERVER['REMOTE_ADDR']),0,2)).".*"),2147483647);
      $print="  You are now logged in.<br>
".           "  ".redirect('./','main');
    }else{
      $print="  Invalid username or password, cannot log in.<br>
".           "  <a href=./>Back to main</a> or <a href=login.php>try again</a>";
    }
    $print="  $L[TD1c]>$print</td>";
  }elseif($act=='logout'){
    setcookie('user',0);
    setcookie('pass','');
    $print="  $L[TD1c]>
".         "    You are now logged out.<br>
".         "    ".redirect('./','main')
 .         "  </td>";
  }

  pageheader('Login');
  print "$L[TBL1]>
".      "$print
".      "$L[TBLend]
";
  pagefooter();
?>
