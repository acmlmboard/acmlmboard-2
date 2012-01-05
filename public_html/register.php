<?php
//  if(strcmp("Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)",$_SERVER['HTTP_USER_AGENT'])==0)
//  die("You fail");
//	header("Location: http://board.acmlm.org/register.php");
//	die("Anonymous does not succeed.");
require 'lib/common.php';

$regdis = $sql->fetchq("SELECT intval FROM misc WHERE field='regdisable'");
if ($regdis[intval] == 1)
{
  pageheader('Register');
  print "$L[TBL1]>$L[TD1c]>
".         "  $L[TRh]>
".         "    $L[TDh] colspan=2>Registration is disabled</td>
".         "  $L[TR]>
".         "    $L[TD1c] width=120>Registration is currently disabled. For more information please read the board annoucments or visit us on <a href=irc.php>IRC</a><br/>
".           "  <a href=./>Back to main</a></td></td>
".      "$L[TBLend]
";
  pagefooter();
  die();
}

  if ($_POST['pass'] == "itsatest") {
	  header("Location: http://board.acmlm.org/register.php");
	  die();
  }

	//[KAWA] Lame. Personally, I'd be inclined to agree but objectively...
	/*
  if($ref == "http://jul.rustedlogic.net/register.php") {
    header("Location: http://jul.rustedlogic.net/register.php?with+best+wishes+from+board2");
    die();
  }
  */





  	//[KAWA] Replacing the CAPTCHA with a simple plain-English mathematics puzzle, as discussed with Emuz.
  	$puzzleAnswer = 42;
  	//$puzzleAnswer = 9001;
  	$puzzleVariations = array(
  		"What is twenty four times two minus 6?",
  		"What is two times twenty four minus 6?",
  		"What is eighty-four divided by two?",
  		"What is twenty one plus twenty one?",
  		"What is six times seven?",
  		"What is seven times six?",
  		"What is fourteen times three?",
  		"What is three times fourteen?",
  		"What is a hundred and twenty six divided by three?",
  	);
  	$puzzle = $puzzleVariations[array_rand($puzzleVariations)];


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
".         "    $L[TD2]><font class='sfont'><h3><b>Notice:</b> Registration may take up to a minute to process.</h3></font>
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
".           fieldrow('Sex'             ,fieldoption('sex',$user[sex],$listsex))."
".           fieldrow('Timezone'      ,fieldselect('timezone',$user['timezone'],$listtimezones))."
".         "  $L[TR]>
".         "    $L[TD1c] width=120>$puzzle</td>
".         "    $L[TD2]>$L[INPt]=puzzle size=13 maxlength=6></td>
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

//    chkproxy();

    if($uname==$cname)
      $err='This username is already taken, please choose another.';
    elseif($name=='' || $cname=='')
      $err='The username must not be empty, please choose one.';
//    elseif($sql -> resultq("SELECT * FROM `users` WHERE `ip` = '". $_SERVER['REMOTE_ADDR'] ."'"))
//      $err='Another user is already using this IP address.';
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
      $sql->query("INSERT INTO users (name,pass,regdate,lastview,ip) VALUES "
                 ."('$_POST[name]','".md5($_POST[pass].$pwdsalt)."',"
                 .ctime().",".ctime().",'$userip')");
      $id=mysql_insert_id();

      $sql->query("UPDATE users SET "
        .setfield('sex').","
        .setfield('timezone')
       . " WHERE `id` = $id"
      );


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

      /* count matches for IP and hash */
      //hash
      $a=$sql->fetchq("SELECT COUNT(*) as c FROM users WHERE pass='".md5($_POST[pass].$pwdsalt)."'");
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

      sendirc("\x0314New user: \x0309".stripslashes($_POST[name])."\x0314 - \x0303{boardurl}?u=$id");
//             ."\x0314 - \x033matches \x0314(\x033#\x0314,\x033/32\x0314,\x033/24\x0314,\x033/16\x0314): \x0314($m_hash\x0314,$m_ip32\x0314,$m_ip24\x0314,$m_ip16\x0314)");

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
