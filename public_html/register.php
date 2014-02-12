<?php
//  if(strcmp("Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)",$_SERVER['HTTP_USER_AGENT'])==0)
//  die("You fail");
//	header("Location: http://board.acmlm.org/register.php");
//	die("Anonymous does not succeed.");
require 'lib/common.php';

$regdis = $sql->fetchq("SELECT intval, txtval FROM misc WHERE field='regdisable'");
if ($regdis['intval'] == 1)
{
  pageheader('Register');
  if($regdis['txtval'] != "") $reason = $regdis['txtval'];
  else $reason = "Registration is currently disabled.";
  print "$L[TBL1]>$L[TD1c]>
".         "  $L[TRh]>
".         "    $L[TDh] colspan=2>Registration is disabled</td>
".         "  $L[TR]>
".         "    $L[TD1c] width=120>$reason For more information please read the board announcements or visit us on <a href=irc.php>IRC</a><br/>
".           "  <a href=./>Back to main</a></td></td>
".      "$L[TBLend]
";
  pagefooter();
  die();
}

  //[KAWA] Replacing the CAPTCHA with a simple plain-English mathematics puzzle, as discussed with Emuz.
  //Moved to config.php for easy edit. -Emuz

  function randstr($l)
  {
    $str="";
    $chars="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$+/~";
    for($i=0;$i<$l;++$i) $str.=$chars[rand(0, strlen($chars)-1)];
    return $str;
  }

  $act=$_POST[action];
  if(!$act){

      $listsex=array('Male','Female','N/A');
      $alltz = $sql->query("SELECT name FROM `timezones`"); 

      $listtimezones = array();
      while ($tz = $sql->fetch($alltz)) {
        $listtimezones[$tz['name']] = $tz['name'];
      }



    $cap=encryptpwd($_SERVER['REMOTE_ADDR'].",".($str=randstr(6)));
    $print=" <form action=register.php method=post>
".         "  $L[TRh]>
".         "    $L[TDh] colspan=2>Register</td>
".         "  $L[TR]>
".         "    $L[TD1c] width=120>&nbsp;</td>
".         "    $L[TD2]><font class='sfont'>Please take a moment to read the <a href='faq.php'>FAQ</a> before registering.</font>
".         "  $L[TR]>
".         "    $L[TD1c] width=120>Username:</td>
".         "    $L[TD2]>$L[INPt]=name size=25 maxlength=25></td>
".         "  $L[TR]>
".         "    $L[TD1c]>Password:</td>
".         "    $L[TD2]>$L[INPp]=pass size=13 maxlength=32></td>
".         "  $L[TR]>
".         "    $L[TD1c]>Password (again):</td>
".         "    $L[TD2]>$L[INPp]=pass2 size=13 maxlength=32></td>
".           fieldrow('Sex'             ,fieldoption('sex',2,$listsex))."
".           fieldrow('Timezone'      ,fieldselect('timezone','UTC',$listtimezones))."
".         "  $L[TR]>
".         "    $L[TD1c] width=120>$puzzle</td>
".         "    $L[TD2]>$L[INPt]=puzzle size=13 maxlength=6></td>
".         "  $L[TR1]>
".         "    $L[TD]>&nbsp;</td>
".         "    $L[TD]>$L[INPs]=action value=Register></td>
".         " </form>
";
  }elseif($act=='Register'){
    $name=trim(stripslashes($_POST[name]));

    $cname=str_replace(array(' ',"\xC2\xA0"),'',$name);
	$cname=strtolower($cname);

    $dupe=$sql->resultp("SELECT COUNT(*) FROM users WHERE LOWER(REPLACE(REPLACE(name,' ',''),0xC2A0,''))=? OR LOWER(REPLACE(REPLACE(displayname,' ',''),0xC2A0,''))=?", array($cname,$cname));
	
	$sex = (int)$_POST['sex'];
	if ($sex < 0 || $sex > 2) $sex = 1;
	
	$timezone = $_POST['timezone'];

    if($dupe)
      $err='This username is already taken, please choose another.';
    elseif($name=='' || $cname=='')
      $err='The username must not be empty, please choose one.';
    elseif(($sql -> resultq("SELECT COUNT(*) FROM `users` WHERE `ip` = '". $_SERVER['REMOTE_ADDR'] ."'")) >= 3)
      $err='Too many users with this IP address.';
    elseif(strlen($_POST[pass])<4)
      $err='Your password must be at least 4 characters long.';
    elseif($_POST[pass]!=$_POST[pass2])
      $err="The two passwords you entered don't match.";
    elseif($_POST[puzzle]!=$puzzleAnswer)
      $err="You are either a bot or very bad at simple mathematics.";

    if($err){
      $print="  $err<br>
".           "  <a href=./>Back to main</a> or <a href=register.php>try again</a>
";
    }else{
	  $name = $sql->escape($name);
	  
      $res = $sql->query("INSERT INTO users (name,pass,regdate,lastview,ip,sex,timezone,fontsize,theme) VALUES "
                 ."('{$name}','".md5($pwdsalt2.$_POST[pass].$pwdsalt)."',"
                 .ctime().",".ctime().",'{$userip}',{$sex},'{$timezone}',{$defaultfontsize},'{$defaulttheme}')");
	  if ($res)
	  {
		  $id=$sql->insertid();


		  $sql->query("INSERT INTO usersrpg (id) VALUES ($id)");
		  
	/*      //[KAWA] Give tokens. WHY WASN'T THIS IN HERE SOONER XD
		  $sql->query("INSERT INTO usertokens (u, t) VALUES ($id, 1)"); */

		  $ugid = 0;
		  if ($id == 1) {
	//        $sql->query("INSERT INTO usertokens (u, t) VALUES ($id, 5)"); 
			  $row = $sql->fetchp("SELECT id FROM `group` WHERE `default`=?",array(-1));
			  $ugid = $row['id'];
		  }

			/*//[KAWA] First gets root. Is that okay or should it be Admin (3)?*/


		  else{ //assign default
			$row = $sql->fetchp("SELECT id FROM `group` WHERE `default`=?",array(1));
			$ugid = $row['id'];
		  }
		   $sql->prepare("UPDATE users SET group_id=? WHERE id=?",array($ugid,$id));
		   
		  // [Mega-Mario] mark existing threads and forums as read
		  $sql->prepare("INSERT INTO threadsread (uid,tid,time) SELECT ?,id,? FROM threads", array($id, ctime()));
		  $sql->prepare("INSERT INTO forumsread (uid,fid,time) SELECT ?,id,? FROM forums", array($id, ctime()));

		  /* count matches for IP and hash */
		  //hash
		  $a=$sql->fetchq("SELECT COUNT(*) as c FROM users WHERE pass='".md5($pwdsalt2.$_POST[pass].$pwdsalt)."'");
		  $m_hash=$a[c]-1;
		  //split the IP
		  $ipparts=explode(".",$userip);
		  // /32 matches
		  $a=$sql->fetchq("SELECT count(*) as c FROM users WHERE ip='$userip'");
		  $m_ip32=$a[c]-1;
		  // /24
		  $a=$sql->fetchq("SELECT count(*) as c FROM users WHERE ip LIKE '$ipparts[0].$ipparts[1].$ipparts[2].%'");
		  $m_ip24=$a[c]-1;
		  // /16
		  $a=$sql->fetchq("SELECT count(*) as c FROM users WHERE ip LIKE '$ipparts[0].$ipparts[1].%'");
		  $m_ip16=$a[c]-1;

		  //fancy colouring (if matches exist, make it red); references to make foreach not operate on copies
		  $clist = array(&$m_hash, &$m_ip32, &$m_ip24, &$m_ip16);
		  foreach($clist as &$c)
			if($c>0) $c="{irccolor-no}$c"; else $c="{irccolor-yes}$c";

		  sendirc("{irccolor-base}New user: \x0309".stripslashes($_POST[name])."{irccolor-base} - {irccolor-url}{boardurl}?u=$id");
		  sendirc("{irccolor-base}New user: \x0309".stripslashes($_POST[name])."{irccolor-base} - {irccolor-url}{boardurl}?u=$id{irccolor-base} - [".$userip." - \x033matches {irccolor-base}(\x033#{irccolor-base},\x033/32{irccolor-base},\x033/24{irccolor-base},\x033/16{irccolor-base}){irccolor-url}: {irccolor-base}($m_hash{irccolor-base},$m_ip32{irccolor-base},$m_ip24{irccolor-base},$m_ip16{irccolor-base})]",$config[staffchan]);

		  $print="  You are now registered!<br>
".           	 "  ".redirect('login.php','login');
	  }
	  else
		$print="Registration failed: ".$sql->error();
    }
    $print="  $L[TD1c]>$print</td>";
  }

  pageheader('Register');
  print "$L[TBL1]>
".      "$print
".      "$L[TBLend]
";
  pagefooter();
?>
