<?php
  require 'lib/common.php'; 
  
  if(checkvar('_POST','action') == 'Login'){
    if($userid=checkuser($_POST[name],md5($pwdsalt2.$_POST[pass].$pwdsalt))){
      switch($_POST['duration']){
        case 1: $dstr=strtotime('+1 Week'); break;
        case 2: $dstr=strtotime('+1 Month'); break;
        case 3: $dstr=strtotime('+1 Year'); break;
        case 4: $dstr=2147483647; break; //Legacy value
        default: $dstr=0;
      }
      setcookie('user',$userid,$dstr);
      setcookie('pass',packlcookie(md5($pwdsalt2.$_POST[pass].$pwdsalt),implode(".",array_slice(explode(".",$_SERVER['REMOTE_ADDR']),0,2)).".*"),$dstr);
      die(header("Location: ./"));
    }else{
       $err="Invalid username or password, cannot log in.";
    }
    $print="  $L[TD1c]>$print</td>";
  }elseif(checkvar('_POST','action') == 'logout'){
	// Using the default token function now! (horray for consistency)
	check_token($_POST['auth'], "weird");
	setcookie('user',0);
	setcookie('pass','');
	die(header("Location: ./"));
  }

	pageheader('Login');
	print $cookiemsg;
	if(isset($err)) noticemsg("Error", $err);
	print "$L[TBL1]>
<form action=login.php method=post>
".         "  $L[TRh]>
".         "    $L[TDh] colspan=2>Login</td>
".         "  $L[TR]>
".         "    $L[TD1c] width=120>Username:</td>
".         "    $L[TD2]>$L[INPt]=name size=25 maxlength=25></td>
".         "  $L[TR]>
".         "    $L[TD1c]>Password:</td>
".         "    $L[TD2]>$L[INPp]=pass size=13 maxlength=32></td>
".         "  $L[TR]>
".           fieldrow('Duration', fieldoption('duration',0,array(0=> 'Session', '1 Week', '1 Month', '1 Year')))."
".         "  $L[TR1]>
".         "    $L[TD]>&nbsp;</td>
".         "    $L[TD]>$L[INPs]=action value=Login></td>
".         " </form>
".      "$L[TBLend]
";
	pagefooter();