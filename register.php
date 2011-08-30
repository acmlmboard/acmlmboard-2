<?php
//  if(strcmp("Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)",$_SERVER['HTTP_USER_AGENT'])==0)
//  die("You fail");
//	header("Location: http://board.acmlm.org/register.php");
//	die("Anonymous does not succeed.");

  if ($_POST['pass'] == "itsatest") {
	  header("Location: http://board.acmlm.org/register.php");
	  die();
  }

  if($ref == "http://jul.rustedlogic.net/register.php") {
    header("Location: http://jul.rustedlogic.net/register.php?with+best+wishes+from+board2");
    die();
  }

  require 'lib/common.php';

  //proxy check
  function chkproxy()
  {
    global $err;
    $f=@fopen("http://".$_SERVER['REMOTE_ADDR']."/","r");
    $d=@fread($f,4096);
    if(strpos($d,"Apache is working on your cPanel")) {
      $err="You appear to be connecting from an open proxy server. If that is not the case, stop any HTTP daemons running on port 80 and retry.";
    }
    @fclose($f);
  }

  function randstr($l)
  {
    $str="";
    $chars="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$+/~";
    for($i=0;$i<$l;++$i) $str.=$chars[rand(0, strlen($chars)-1)];
    return $str;
  }

  // SamuraiHax
  if($_POST['pass'] == "asdf") {
	setcookie("dumb", "1", time()+999999);
	die();
  }

  $act=$_POST[action];
  if(!$act){
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
".         "  $L[TR]>
".         "    $L[TD1c] rowspan=2>Captcha:</td>
".         "    $L[TD2]><input type=hidden name=captcha1 value='$cap'><img border=0 style='margin:3px;margin-right:0px' src='gfx/captcha.php?l=1&a=".urlencode($cap)."'><img style='margin:3px;margin-left:0px' border=0 src='gfx/captcha.php?a=".urlencode($cap)."'></td>
".         "  $L[TR]>
".         "    $L[TD2]>$L[INPt]=captcha2 size=13 maxlength=6></td>
".         "  $L[TR1]>
".         "    $L[TD]>&nbsp;</td>
".         "    $L[TD]>$L[INPs]=action value=Register></td>
".         " </form>
";
  }elseif($act=='Register'){
    $_POST[name]=$name=trim($_POST[name]);

    $cname=strtolower($name);
    $cname=str_replace(' ','',$cname);
    $cname=str_replace(' ','',$cname);

    $users=$sql->query('SELECT name FROM users');
    while($user=$sql->fetch($users)){
      $uname=strtolower($user[name]);
      $uname=str_replace(' ','',$uname);
      $uname=str_replace(' ','',$uname);
      if($uname==$cname)
        break;
    }

    chkproxy();

    $captcha=explode(",",decryptpwd($_POST[captcha1]));

    if($uname==$cname)
      $err='This username is already taken, please choose another.';
    elseif($name=='' || $cname=='')
      $err='The username must not be empty, please choose one.';
    elseif($sql -> resultq("SELECT * FROM `users` WHERE `ip` = '". $_SERVER['REMOTE_ADDR'] ."'"))
      $err='Another user is already using this IP address.';
    elseif(strlen($_POST[pass])<4)
      $err='Your password must be at least 4 characters long.';
    elseif($_POST[pass]!=$_POST[pass2])
      $err="The two passwords you entered don't match.";
    elseif($captcha[0]!=$_SERVER['REMOTE_ADDR'])
      $err="You appear to have switched IPs since loading register.php. $captcha[0]";
    elseif($captcha[1]!=$_POST[captcha2])
      $err="Wrong captcha entered. Try again.";

    if($err){
      $print="  $err<br>
".           "  <a href=./>Back to main</a> or <a href=register.php>try again</a>
";
    }else{
      $sql->query("INSERT INTO users (name,pass,regdate,lastview,ip) VALUES "
                 ."('$_POST[name]','".md5($_POST[pass])."',"
                 .ctime().",".ctime().",'$userip')");
      $id=mysql_insert_id();
      $sql->query("INSERT INTO usersrpg (id) VALUES ($id)");

      /* count matches for IP and hash */
      //hash
      $a=$sql->fetchq("SELECT COUNT(*) as c FROM users WHERE pass='".md5($_POST[pass])."'");
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
        if($c>0) $c="\x0307$c"; else $c="\x0309$c";

      sendirc("\x0314New user: \x0309".stripslashes($_POST[name])."\x0314 - \x0303{boardurl}?u=$id"
             ."\x0314 - \x033matches \x0314(\x033#\x0314,\x033/32\x0314,\x033/24\x0314,\x033/16\x0314): \x0314($m_hash\x0314,$m_ip32\x0314,$m_ip24\x0314,$m_ip16\x0314)");

      $print="  You are now registered!<br>
".           "  ".redirect('login.php','login');
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
