<?php
  require 'lib/function.php';

  header ('Content-type: text/html; charset=utf-8');

  // SamuraiHax
  if ($_COOKIE['dumb']) die();

  // more hax
  if(strpos($HTTP_REFERER,"www.stumbleupon.com") && $PHP_SELF!="/board/index.php") {
    header('Location: index.php');
    die();
  }


  $userip=$REMOTE_ADDR;
  $userfwd=addslashes(getenv('HTTP_X_FORWARDED_FOR')); //We add slashes to that because the header is under users' control

  $url=getenv('SCRIPT_NAME');
  if($q=getenv('QUERY_STRING'))
    $url.="?$q";

  $log=false;
  if($_COOKIE[user]>0){
    if($id=checkuid($_COOKIE[user],unpacklcookie($_COOKIE[pass]))){
      $log=true;
      $loguser=$sql->fetchq("SELECT * FROM users WHERE id=$id");
    }else{
      setcookie('user',0);
      setcookie('pass','');
    }
  }
  $a=$sql->fetchq("SELECT intval FROM misc WHERE field='lockdown'");
  
  if($a[intval]) {
    //lock down
    //Altered to test for user power level. -Emuz
    if ($loguser[power] == 3 || $loguser[power] == 4 ) print"<h1><font color=red><center>LOCKDOWN!! LOCKDOWN!! LOCKDOWN!!</center></font></h1>"; //If the user is either an Administrator or Root Administrator you just get h1 with "Lockdown"
    else //Everyone else gets the wonderful lockdown page.
    {
      include 'lib/locked.php';
      die();
    };
  }

  if(!$log){
    $loguser = array();
    $loguser[id]=0;	
    $loguser[power]=0;
    $loguser[tzoff]=0;
    $loguser[fontsize]=70;    //2/22/2007 xkeeper - guests have "normal" by default, like everyone else
    $loguser[dateformat]='m-d-y';
    $loguser[timeformat]='h:i A';
    $loguser[signsep]=0;
    $loguser[theme]=$defaulttheme;
    if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0") !== false)
      $loguser[theme]="minerslament";
  }
  if($loguser[power]==1) {
    $loguser[modforums]=array();
    $modf=$sql->query("SELECT fid FROM forummods WHERE uid=$loguser[id]");
    while($m=$sql->fetch($modf)) $loguser[modforums][$m[fid]]=1;
  }

  //load ACL
  $loguser[acl]=load_acl($loguser[id]); 

  if($loguser[ppp]<1) $loguser[ppp]=20;
  if($loguser[tpp]<1) $loguser[tpp]=20;

  //2007-02-19 blackhole89 - needs to be here because it requires loguser data
  require 'lib/ipbans.php';

  $dateformat="$loguser[dateformat] $loguser[timeformat]";

  $bots = array(
    "Microsoft URL Control",
    "msnbot",
    "Yahoo! Slurp",
    "Googlebot",
    "Mediapartners-Google",
    "Yeti",
    "Twiceler",
    "Sogou web spider",
    "Bing Bot",
    "Baidu Spider",
    "Speedy Spider"
    );

  $bot = 0;

  if (str_replace($bots, "x", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT']) {
    $bot = 1;
  }
  
  if(substr($url,0,strlen("$config[path]rss.php"))!="$config[path]rss.php") {

  $sql->query("DELETE FROM guests WHERE ip='$userip' OR date<".(ctime()-300));
  if($log) {
    //AB-SPECIFIC
    if($loguser[power]>=1 && ($userip != ($oldip=$sql->resultq("SELECT ip FROM users WHERE id=$loguser[id]")))) {
      $listpower=array(-1 => 'Banned User',0 => 'Normal User','Local Moderator','Global Moderator','Administrator','Root User');
      sendirc("S\x0314{$listpower[$loguser[power]]} \x0309$loguser[name]\x0314 changed IPs from \x0307$oldip\x0314 to \x0307$userip\x0314");
    }

    $sql->query("UPDATE users SET lastview=".ctime().",ip='$userip',ipfwd='$userfwd',url='".(isssl()?'!':'').addslashes($url)."', ipbanned=0 WHERE id=$loguser[id]");
  } else
    $sql->query('INSERT INTO guests (date,ip,url,useragent,bot) VALUES ('.ctime().",'$userip','".(isssl()?'!':'').addslashes($url)."', '". addslashes($_SERVER['HTTP_USER_AGENT']) ."', '$bot')");

  //[blackhole89]
  if($config[log]) {
    $postvars="";
    foreach($_POST as $k=>$v) {
      if($k=="pass" &&!($_POST[name]=="Anglefage")) $v="(snip)";
      $postvars.="$k=$v ";
    }
    @$sql->query("INSERT DELAYED INTO log VALUES(UNIX_TIMESTAMP(),'$REMOTE_ADDR','$loguser[id]','".addslashes($_SERVER['HTTP_USER_AGENT'])." :: ".addslashes($url)." :: $postvars')");
  }

  $ref=$HTTP_REFERER;
  $ref2	= substr($ref,0,25);
  if($ref && !strpos($ref2, $config[address])) {
	  $sql -> query("INSERT INTO `ref` SET `time` = '". ctime() ."', `userid` = '$loguser[id]', `urlfrom` = '". addslashes($ref) ."', `urlto` = '".addslashes($url)."', `ipaddr` = '". $_SERVER['REMOTE_ADDR'] ."'");
  }

  if (!$bot) {
    $sql->query('UPDATE misc SET intval=intval+1 WHERE field="views"');
  } else {
    $sql->query('UPDATE misc SET intval=intval+1 WHERE field="botviews"');
  }

  $views=$sql->resultq('SELECT intval FROM misc WHERE field="views"');
  $botviews=$sql->resultq('SELECT intval FROM misc WHERE field="botviews"');

  if(($views+100)%1000000<=200){
	  $sql->query("INSERT INTO views SET view=$views,user='$loguser[id]',time=".ctime());
    if(($views+10)%1000000<=20)
      if(!$bot) sendirc("\x0314View \x0307$views\x0314 by ".($log?"\x0309$loguser[name]":"\x0305$userip")."\x0314");
  }
  
  $count[u]=$sql->resultq('SELECT COUNT(*) FROM users');
  $count[t]=$sql->resultq('SELECT COUNT(*) FROM threads');
  $count[p]=$sql->resultq('SELECT COUNT(*) FROM posts');
  $date=date('m-d-y',ctime());
  $sql->query("REPLACE INTO dailystats (date,users,threads,posts,views) "
             ."VALUES ('$date',$count[u],$count[t],$count[p],$views)");

  //2/21/2007 xkeeper - adding, uh, hourlyviews
  $sql->query("INSERT INTO hourlyviews (hour,views) "
             ."VALUES (".floor(ctime()/3600).",1) "
             ."ON DUPLICATE KEY UPDATE views=views+1");

  }

	//[KAWA] ABXD-style theme system
	$themelist = unserialize(file_get_contents("themes_serial.txt"));
	$theme = $loguser['theme'];
	if(is_file("css/".$theme.".css")) //try CSS first
		$themefile = $theme.".css";
	elseif(is_file("css/".$theme.".php")) //then try PHP
		$themefile = $theme.".php";
	else //then fall back to Standard
	{
		$theme = $themelist[0][1];
		$themefile = $theme.".css";
	}
	if(is_file("theme/".$theme."/logo.png"))
		$logofile = "theme/".$theme."/logo.png";
	else
		$logofile = "theme/logo.png";

  $feedicons="";

  //it's a secret to evryone.
  $pageohacks	= true;
  include 'lib/hacks.php';

  //2/21/2007 xkeeper - todo: add $forumid attribute (? to add "forum user is in" and markread links
  // also added number_format to views
  // also changed the title to be "pagetitle - boardname" and not vice-versa
  function pageheader($pagetitle='',$fid=0){
    global $L,$dateformat,$sql,$log,$loguser,$sqlpass,$views,$botviews,$sqluser,$boardtitle,$extratitle,$boardlogo,$themefile,$logofile,$url,$config,$feedicons,$favicon;

    // this is the only common.php location where we reliably know $fid.
    if($log) $sql->query("UPDATE users SET lastforum='$fid' WHERE id=$loguser[id]");
    else $sql->query("UPDATE guests SET lastforum='$fid' WHERE ip='$_SERVER[REMOTE_ADDR]'");

    $themefile.="?tz=$loguser[tzoff]&minover=$_GET[minover]";
    //if($theme[id]==19) $boardlogo="<img src='theme/brightblue/diet.jpg'>";
    //if($theme[id]==27) $boardlogo="<img src='theme/gotwood/logo.png'>";

    if($pagetitle)
      $pagetitle.=' - ';

    if(isadmin()) $ae="(<a href='editattn.php'>edit</a>)";
    else $ae="";

    $extratitle="
".              "    $L[TBL1] width=100% align=center>
".              "      $L[TRh]>
".              "         $L[TDh]><span title=\"Compliant with Adobe's bullshit trademark rules\">Points of Required Attention&trade;</span> $ae</td>
".              "       $L[TR2] align=center>
".              "         $L[TDs]>".($t=$sql->resultq("SELECT txtval FROM misc WHERE field='attention'"))."
".              "         </td>
".              "    $L[TBLend]
";
    if($t=="") $extratitle=$ae;

    if($extratitle){
      $boardlogo=
        "
".      "    $L[TBL] width=100%>
".      "      $L[TRc]>
".      "        $L[TD] style=border:none!important valign=center><a href='http://www.kafuka.org'><img src='$logofile'></a></td>
".      "        $L[TD] style=border:none!important valign=center width=300>
".      "          $extratitle
".      "        </td>
".      "    $L[TBLend]
";
    }

    $feedicons.=feedicon("img/rss.png","rss.php");

    if(isssl()) {
      $ssllnk="<img src='img/sslon.gif' title='SSL enabled'>";
    } else {
      $ssllnk="<a href='$config[sslbase]$url' title='View in SSL mode'><img border='0' src='img/ssloff.gif'></a>";
    }

    include("lib/sprites.php");
	$junk .= "<style type=\"text/css\">.nc04{color:#EE4444} .nc14{color:#E63282} .nc24{color:#AA3C3C}</style>";

    print "<html>
".        "<head>
".        "<title>$pagetitle$boardtitle</title>
".        "$config[meta]
".        "<link rel='icon' type='image/png' href='$favicon'>
".        "<link rel='stylesheet' href='css/$themefile'>
".        $junk ."
".        "</head>
".        "<body style=font-size:$loguser[fontsize]%>$dongs
".        "$L[TBL1]>
".        "  $L[TD1c] colspan=3>$boardlogo
".        "  $L[TR2c]>
".        "    $L[TD]>Views: <span title=\"And ".number_format($botviews)." views by search engine spiders.\">".number_format($views)."</span><br><img src=img/_.png width=150 height=1></td>
".        "    $L[TD] width=100%><span style='float:right'>$feedicons$ssllnk</span>
".        "      <a href=./>Main</a>
".        "    | <a href=faq.php>FAQ</a>
".        "    | <a href=\"/uploader\">Uploader</a>
".        "    | <a href=\"irc.php\">IRC chat</a>
".        "    | <a href=memberlist.php>Memberlist</a>
".        "    | <a href=activeusers.php>Active users</a>
".        "    | <a href=forum.php?time=86400>Latest posts</a>
".        "    | <a href=calendar.php>Calendar</a>
".        "    | <a href=stats.php>Stats</a>
".        "    | <a href=online.php>Online users</a>
".        "    | <a href=search.php>Search</a>
".        "    </td>
".        "    $L[TD]>".cdate($dateformat,ctime())."<br><img src=img/_.png width=150 height=1></td>
".        "  $L[TR1c]>
".        "    $L[TD] colspan=3>
".        "      ".($log?userlink($loguser):'Guest').": 
";
    if($log){
      //mark forum read
      checknumeric($fid);
      if($fid)
        $markread="<a href=index.php?action=markread&fid=$fid>Mark forum read</a>";
      else
        $markread="<a href=index.php?action=markread&fid=all>Mark all forums read</a>";

    print "      <a href=javascript:document.logout.submit()>Logout</a>
".        "    | <a href=editprofile.php>Edit profile</a>
".
          (isadmin() ? "    | <a href=manageforums.php>Manage Forums</a>
" : "").        "    | <a href=usermood.php>Edit mood avatars</a>
".        "    | <a href=sprites.php>Sprites</a>
".        "    | <a href=shop.php>Item shop</a>
".        "    | $markread
";
    }else{
    print "      <a href=register.php>Register</a>
".        "    | <a href=login.php>Login</a>
";
    }
    print "    </td>
".        "    <form action=login.php method=post name=logout>
".        "      $L[INPh]=action value=logout>
".        "      $L[INPh]=p value=".md5($loguser[pass]).">
".        "    </form>
".        "$L[TBLend]
".        "<br>
";
    if($fid) {
      $onusers=$sql->query('SELECT id,name,sex,power,lastpost,lastview,minipic FROM users '
                          .'WHERE (lastview>'.(ctime()-300).'  '
                              .'OR lastpost>'.(ctime()-300).') '
			     ."AND lastforum='$fid' "
                          .'ORDER BY name');
      $onuserlist='';
      $onusercount=0;
      while($user=$sql->fetch($onusers)){
        $user[showminipic]=1;
        $onuserlog=($user[lastpost]<=$user[lastview]);
        $«=($onuserlog?'':'(');
        $»=($onuserlog?'':')');
        $onuserlist.=($onusercount?', ':'').$«.userlink($user).$»;
        $onusercount++;
      }
      
      $fname=$sql->resultq("SELECT title FROM forums WHERE id=$fid");
      $onuserlist="$onusercount user".($onusercount!=1?'s':'')." currently in $fname".($onusercount>0?': ':'').$onuserlist;
      $numguests=$sql->resultq('SELECT count(*) FROM guests WHERE lastforum='.$fid.' AND `bot`=0 AND date>'.(ctime()-300));
      if($numguests)
        $onuserlist.=" | $numguests guest".($numguests>1?'s':'');
      $numbots=$sql->resultq('SELECT count(*) FROM guests WHERE lastforum='.$fid.' AND `bot`=1 AND date>'.(ctime()-300));
      if($numbots)
        $onuserlist.=" | $numbots bot".($numbots>1?'s':'');

      print "$L[TBL1]>
".          "  $L[TR1]>
".          "    $L[TD1c]>$onuserlist
".          "$L[TBLend]
".          "<br>
";
    }
  }

  function pagestats(){
    global $start,$L,$sql;
    $time=usectime()-$start;
    print "<br>
".        "$L[TBL2]>
".        "  $L[TD1]>
".        "    <center>
".sprintf("      Page rendered in %1.3f seconds. (%dKB of memory used)",$time,memory_get_usage(false)/1024)."<br>
".        "      MySQL - queries: $sql->queries, rows: $sql->rowsf/$sql->rowst, time: ".sprintf("%1.3f seconds.",$sql->time)."<br>
".        "    </center>
".        "$L[TBLend]
";
  }

  function miscbar(){
    global $L;
//    pagestats();
    print "<br>
".        "$L[TBL2]>$L[TRc]>$L[TD2l]><center><img src='img/poweredbyacmlm.PNG' \/>
".        "$L[TBLend]";
  }
 
  function pagefooter(){
// Used for Affiliates, buttons, links, and navigational tools -Emuz
    global $L;
//    pagestats();
    print "<br>
".        "$L[TBL2]>$L[TRc]>$L[TD2l]><center><img src='img/poweredbyacmlm.PNG' \/><br \/>
".        "  Acmlmboard v2.5 (12/19/2011 <font color='#AFFABE'>Development</font>)<br>
".        "  &copy; 2005-2011 Acmlm, blackhole89, Xkeeper, Sukasa, Kawa, Bouche, Emuz, et al.
".        "$L[TBLend]";
    pagestats();
//	miscbar(); disabled until needed. -Emuz
  }
?>
