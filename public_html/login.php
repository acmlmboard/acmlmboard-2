<?php
  require 'lib/common.php'; 

$rdmsg="";
  if($_COOKIE['pstbon']){
	header("Set-Cookie: pstbon=".$_COOKIE['pstbon']."; Max-Age=1; Version=1");
 $rdmsg="<script language=\"javascript\">
	function dismiss()
	{
		document.getElementById(\"postmes\").style['display'] = \"none\";
	}
</script>
	<div id=\"postmes\" onclick=\"dismiss()\" title=\"Click to dismiss.\"><br>
".      "$L[TBL1] width=\"100%\" id=\"edit\">$L[TRh]>$L[TDh]>";
if($_COOKIE['pstbon']==-1){
	$rdmsg.="You are now registered!<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
".	"<tr>$L[TD1l]>Please login.</td></tr></table></div>"; }
}

  $act=$_POST[action];
  if($act=='Login'){
    if($userid=checkuser($_POST[name],md5($pwdsalt2.$_POST[pass].$pwdsalt))){
      setcookie('user',$userid,2147483647);
      setcookie('pass',packlcookie(md5($pwdsalt2.$_POST[pass].$pwdsalt),implode(".",array_slice(explode(".",$_SERVER['REMOTE_ADDR']),0,2)).".*"),2147483647);
      die(header("Location: ./"));
    }else{
       $err="Invalid username or password, cannot log in.";
    }
    $print="  $L[TD1c]>$print</td>";
  }elseif($act=='logout'){
	// Using the default token function now! (horray for consistency)
	check_token($_POST['auth'], "weird");
	setcookie('user',0);
	setcookie('pass','');
	die(header("Location: ./"));
  }

  pageheader('Login');
  if($_COOKIE['pstbon']){ print $rdmsg;}
 if($err) noticemsg("Error", $err);
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
".         "  $L[TR1]>
".         "    $L[TD]>&nbsp;</td>
".         "    $L[TD]>$L[INPs]=action value=Login></td>
".         " </form>
".      "$L[TBLend]
";
  pagefooter();
?>
