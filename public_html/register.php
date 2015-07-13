<?php
//  if(strcmp("Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)",$_SERVER['HTTP_USER_AGENT'])==0)
//  die("You fail");
//	header("Location: http://board.acmlm.org/register.php");
//	die("Anonymous does not succeed.");
require 'lib/common.php';

$regdis = $sql->fetchq("SELECT intval, txtval FROM misc WHERE field='regdisable'");
if ($regdis['intval'] == 1) {
	pageheader('Register');

	if($regdis['txtval'] != "")
		$reason = $regdis['txtval'];
	else 
		$reason = "Registration is currently disabled.";

	print "<table cellspacing=\"0\" class=\"c1\"><td class=\"b n1\" align=\"center\">
	".         "  <tr class=\"h\">
	".         "    <td class=\"b h\" colspan=2>Registration is disabled</td>
	".         "  <tr>
	".         "    <td class=\"b n1\" align=\"center\" width=120>$reason For more information please read the board announcements or visit us on <a href=irc.php>IRC</a><br/>
	".           "  <a href=./>Back to main</a></td></td>
	".      "</table>
	";
	pagefooter();
	die();
}


$boardemailaddress=$sql->resultq("SELECT `emailaddress` FROM `misc` WHERE `field`='boardemail'");
if (isProxy()) {
	pageheader('Register');

	if($regdis['txtval'] != "") 
		$reason = $regdis['txtval'];
	else 
		$reason = "Security Check Failure";

	print "<table cellspacing=\"0\" class=\"c1\"><td class=\"b n1\" align=\"center\">
		".         "  <tr class=\"h\">
		".         "    <td class=\"b h\" colspan=2>Registration is denied</td>
		".         "  <tr>
		".         "    <td class=\"b n1\" align=\"center\" width=120>Our site has detected your IP is either a proxy, or listed as a known spammer. If you feel this is in error contact the board admins at ".($boardemailaddress).".</a></td></td>
		".      "</table>
		";

	pagefooter();
	die();
}

//[KAWA] Replacing the CAPTCHA with a simple plain-English mathematics puzzle, as discussed with Emuz.
//Moved to config.php for easy edit. -Emuz

function randstr($l) {
	$str="";
	$chars="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$+/~";
	for($i = 0; $i < $l; ++$i)
		$str .= $chars[rand(0, strlen($chars)-1)];
	return $str;
}

$act = isset($_POST['action']) ? $_POST['action'] : '';
if($act == 'Register'){
	$name=trim(stripslashes($_POST['name']));

	$cname=str_replace(array(' ',"\xC2\xA0"),'',$name);
	$cname=strtolower($cname);

	$dupe=$sql->resultp("SELECT COUNT(*) FROM users WHERE LOWER(REPLACE(REPLACE(name,' ',''),0xC2A0,''))=? OR LOWER(REPLACE(REPLACE(displayname,' ',''),0xC2A0,''))=?", array($cname,$cname));
	
	$sex = (int)$_POST['sex'];
	if ($sex < 0 || $sex > 2) $sex = 1;
	
	$timezone = $_POST['timezone'];

	$err = '';
	if($dupe)
		$err = 'This username is already taken, please choose another.';
	elseif($name=='' || $cname=='')
		$err = 'The username must not be empty, please choose one.';
	elseif(($sql->resultq("SELECT COUNT(*) FROM `users` WHERE `ip` = '". $_SERVER['REMOTE_ADDR'] ."'")) >= 3)
		$err = 'Too many users with this IP address.';
	elseif(strlen($_POST['pass']) < 4)
		$err = 'Your password must be at least 4 characters long.';
	elseif($_POST['pass'] != $_POST['pass2'])
		$err = "The two passwords you entered don't match.";
	elseif($config['registrationpuzzle'] && $_POST['puzzle'] != $puzzleAnswer)
		$err = "You are either a bot or very bad at simple mathematics.";

	if(empty($err)){
		$name = $sql->escape($name);
		$salted_password = md5($pwdsalt2 . $_POST['pass'] . $pwdsalt);
		$query_string = sprintf("INSERT INTO users (name,pass,regdate,lastview,ip,sex,timezone,fontsize,theme) VALUES ('%s', '%s', %d, %d, '%s', %d, '%s', %d, '%s');",
		$name, $salted_password, ctime(), ctime(), $userip, $sex, $timezone, $defaultfontsize, $defaulttheme);
		$res = $sql->query($query_string);
		if ($res) {
			$id=$sql->insertid();
			$sql->query("INSERT INTO usersrpg (id) VALUES ($id)");

			$ugid = 0;
			if ($id == 1) {
				$row = $sql->fetchp("SELECT id FROM `group` WHERE `default`=?", array(-1));
				$ugid = $row['id'];
			} else {
				$row = $sql->fetchp("SELECT id FROM `group` WHERE `default`=?", array(1));
				$ugid = $row['id'];
			}
			$sql->prepare("UPDATE users SET group_id=? WHERE id=?",array($ugid,$id));

			// [Mega-Mario] mark existing threads and forums as read
			$sql->prepare("INSERT INTO threadsread (uid,tid,time) SELECT ?,id,? FROM threads", array($id, ctime()));
			$sql->prepare("INSERT INTO forumsread (uid,fid,time) SELECT ?,id,? FROM forums", array($id, ctime()));

			/* count matches for IP and hash */
			//hash
			$a=$sql->fetchq("SELECT COUNT(*) as c FROM users WHERE pass='".md5($pwdsalt2.$_POST[pass].$pwdsalt)."'");
			$m_hash=$a['c']-1;
			//split the IP
			$ipparts=explode(".",$userip);
			// /32 matches
			$a=$sql->fetchq("SELECT count(*) as c FROM users WHERE ip='$userip'");
			$m_ip32=$a['c']-1;
			// /24
			$a=$sql->fetchq("SELECT count(*) as c FROM users WHERE ip LIKE '$ipparts[0].$ipparts[1].$ipparts[2].%'");
			$m_ip24=$a['c']-1;
			// /16
			$a=$sql->fetchq("SELECT count(*) as c FROM users WHERE ip LIKE '$ipparts[0].$ipparts[1].%'");
			$m_ip16=$a['c']-1;

			//fancy colouring (if matches exist, make it red); references to make foreach not operate on copies
			$clist = array(&$m_hash, &$m_ip32, &$m_ip24, &$m_ip16);
			foreach($clist as &$c)
				if($c>0) $c="{irccolor-no}$c"; else $c="{irccolor-yes}$c";

			sendirc("{irccolor-base}New user: \x0309".stripslashes($_POST['name'])."{irccolor-base} - {irccolor-url}{boardurl}?u=$id");
			sendirc("{irccolor-base}New user: \x0309".stripslashes($_POST['name'])."{irccolor-base} - {irccolor-url}{boardurl}?u=$id{irccolor-base} - [".$userip." - \x033matches {irccolor-base}(\x033#{irccolor-base},\x033/32{irccolor-base},\x033/24{irccolor-base},\x033/16{irccolor-base}){irccolor-url}: {irccolor-base}($m_hash{irccolor-base},$m_ip32{irccolor-base},$m_ip24{irccolor-base},$m_ip16{irccolor-base})]",$config['staffchan']);

			redirect('login.php',-1);
		} else {
			$err="Registration failed: ".$sql->error();
		}
	}
}

  pageheader('Register');
     $listsex=array('Male','Female','N/A');
      $alltz = $sql->query("SELECT name FROM `timezones`"); 

      $listtimezones = array();
      while ($tz = $sql->fetch($alltz)) {
        $listtimezones[$tz['name']] = $tz['name'];
      }

    $cap=encryptpwd($_SERVER['REMOTE_ADDR'].",".($str=randstr(6)));
 if(!empty($err)) noticemsg("Error", $err);
  print "<table cellspacing=\"0\" class=\"c1\">
".         " <form action=register.php method=post>
".         "  <tr class=\"h\">
".         "    <td class=\"b h\" colspan=2>Register</td>
".         "  <tr>
".         "    <td class=\"b n1\" align=\"center\" width=120>&nbsp;</td>
".         "    <td class=\"b n2\"><font class='sfont'>Please take a moment to read the <a href='faq.php'>FAQ</a> before registering.</font>
".         "  <tr>
".         "    <td class=\"b n1\" align=\"center\" width=120>Username:</td>
".         "    <td class=\"b n2\"><input type=\"text\" name=name size=25 maxlength=25></td>
".         "  <tr>
".         "    <td class=\"b n1\" align=\"center\">Password:</td>
".         "    <td class=\"b n2\"><input type=\"password\" name=pass size=13 maxlength=32></td>
".         "  <tr>
".         "    <td class=\"b n1\" align=\"center\">Password (again):</td>
".         "    <td class=\"b n2\"><input type=\"password\" name=pass2 size=13 maxlength=32></td>
".           fieldrow('Sex'             ,fieldoption('sex',2,$listsex))."
".           fieldrow('Timezone'      ,fieldselect('timezone','UTC',$listtimezones))."
";
    if($config['registrationpuzzle'])
    print     
           "  <tr>
".         "    <td class=\"b n1\" align=\"center\" width=120>$puzzle</td>
".         "    <td class=\"b n2\"><input type=\"text\" name=puzzle size=13 maxlength=6></td>
";
    print
           "  <tr class=\"n1\">
".         "    <td class=\"b\">&nbsp;</td>
".         "    <td class=\"b\"><input type=\"submit\" class=\"submit\" name=action value=Register></td>
".         " </form>
".      "</table>
";
  pagefooter();
?>