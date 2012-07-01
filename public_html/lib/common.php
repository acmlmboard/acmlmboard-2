<?php
  require 'lib/function.php';

  header ('Content-type: text/html; charset=utf-8');

  if (ini_get('register_globals')) echo '<span style="color: red;"> Warning: register_globals is enabled.</style>';
  $userip=$_SERVER[REMOTE_ADDR];
  $userfwd=addslashes(getenv('HTTP_X_FORWARDED_FOR')); //We add slashes to that because the header is under users' control

  $url=getenv('SCRIPT_NAME');
  if($q=getenv('QUERY_STRING'))
    $url.="?$q";

  require 'lib/login.php';

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
    $loguser[timezone] = "UTC";
    $loguser[fontsize]=70;    //2/22/2007 xkeeper - guests have "normal" by default, like everyone else
    $loguser[dateformat]='m-d-y';
    $loguser[timeformat]='h:i A';
    $loguser[signsep]=0;
    $loguser[theme]=$defaulttheme;
    if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0") !== false)
      $loguser[theme]="minerslament";
    $loguser[blocksprites]=1;
  }
  if($loguser[power]==1) {
    $loguser[modforums]=array();
    $modf=$sql->query("SELECT fid FROM forummods WHERE uid=$loguser[id]");
    while($m=$sql->fetch($modf)) $loguser[modforums][$m[fid]]=1;
  }


  require("lib/timezone.php");

  if($loguser[ppp]<1) $loguser[ppp]=20;
  if($loguser[tpp]<1) $loguser[tpp]=20;

  //2007-02-19 blackhole89 - needs to be here because it requires loguser data
  require 'lib/ipbans.php';

  $dateformat="$loguser[dateformat] $loguser[timeformat]";

  $bota=$sql->query("SELECT bot_agent FROM robots");
  while($robots=$sql->fetch($bota)){
    $bots[]=$robots[bot_agent];
  }
  $bot = 0;

  if (str_replace($bots, "x", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT']) {
    $bot = 1;
  }
  if ($bot) load_bot_permset();
  if(substr($url,0,strlen("$config[path]rss.php"))!="$config[path]rss.php") {

  $sql->query("DELETE FROM guests WHERE ip='$userip' OR date<".(ctime()-300));
   if($log) {
    //AB-SPECIFIC
    if(has_perm('track-ip-change') && ($userip != ($oldip=$sql->resultq("SELECT ip FROM users WHERE id=$loguser[id]")))) {
      sendirc("{irccolor-base}".get_irc_groupname($loguser[group_id])." {irccolor-name}".($loguser[displayname]?$loguser[displayname]:$loguser[name])."{irccolor-base} changed IPs from {irccolor-no}$oldip{irccolor-base} to {irccolor-yes}$userip{irccolor-base}",$config[staffchan]);
    }

    $sql->query("UPDATE users SET lastview=".ctime().",ip='$userip',ipfwd='$userfwd',url='".(isssl()?'!':'').addslashes($url)."', ipbanned=0 WHERE id=$loguser[id]");
  } else
    $sql->query('INSERT INTO guests (date,ip,url,useragent,bot) VALUES ('.ctime().",'$userip','".(isssl()?'!':'').addslashes($url)."', '". addslashes($_SERVER['HTTP_USER_AGENT']) ."', '$bot')");

  //[blackhole89]
  if($config[log]) {
    $postvars="";
    foreach($_POST as $k=>$v) {
      if($k=="pass" || $k=='pass2') $v="(snip)";
      $postvars.="$k=$v ";
    }
    @$sql->query("INSERT DELAYED INTO log VALUES(UNIX_TIMESTAMP(),'$userip','$loguser[id]','".addslashes($_SERVER['HTTP_USER_AGENT'])." :: ".addslashes($url)." :: $postvars')");
  }

  $ref=$_SERVER[HTTP_REFERER];
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
      if(!$bot) sendirc("{irccolor-base}View {irccolor-title}$views{irccolor-base} by ".($log?"{irccolor-name}".get_irc_displayname()."":"{irccolor-name}$userip")."{irccolor-base}");
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
		$logofile = $defaultlogo;

  $feedicons="";

/* Salvaged from "Xkeeper's Nifty Page-o-Hacks". Why? I don't really know, however it's a nice bit of code
   for 'just in case' purposes I guess. Basically it'll get axed when we clean up the other fragments anyway.
   -Emuz

  if(strstr($url,"UNION%20SELECT") && $loguser[power]<3) {
    $sql->query("INSERT INTO ipbans VALUES ('$REMOTE_ADDR',1,'','automatic','UNION SELECT')");
    echo "(insert sound of something blowing up here)";
    die();
  }
*/

  //2/21/2007 xkeeper - todo: add $forumid attribute (? to add "forum user is in" and markread links
  // also added number_format to views
  // also changed the title to be "pagetitle - boardname" and not vice-versa
  function pageheader($pagetitle='',$fid=0){
    global $L,$dateformat,$sql,$log,$loguser,$sqlpass,$views,$botviews,$sqluser,$boardtitle,$extratitle,$boardlogo,$themefile,$logofile,$url,$config,$feedicons,$favicon,$showonusers,$count,$lastannounce,$lastforumannounce;

    // this is the only common.php location where we reliably know $fid.
    if($log) $sql->query("UPDATE users SET lastforum='$fid' WHERE id=$loguser[id]");
    else $sql->query("UPDATE guests SET lastforum='$fid' WHERE ip='$_SERVER[REMOTE_ADDR]'");
    $timezone = new DateTimeZone($loguser['timezone']);
    $tzoff = $timezone->getOffset(new DateTime("now"));
    $themefile.="?tz=$tzoff&minover=$_GET[minover]";

    if($pagetitle)
      $pagetitle.=' - ';

    if(has_perm('edit-attentions-box')) $ae="(<a href='editattn.php'>edit</a>)";
    else $ae="";

    $extratitle="
".              "    $L[TBL1] width=100% align=center>
".              "      $L[TRh]>
".              "         $L[TDh]><span title=\"Compliant with Adobe's bullshit trademark rules\">$config[atnname]</span> $ae</td>
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
	if ($log) {
		$radar = build_postradar($loguser['id']);
	}
    include("lib/sprites.php");
  if ($log) {
    $logbar = $loguser;
    $logbar['showminipic'] = 1;
  }
    print "<!DOCTYPE html>
".        "<html>
".        "<head>
".        "<title>$pagetitle$boardtitle</title>
".        "$config[meta]
".        "<link rel='icon' type='image/png' href='$favicon'>
".        "<style>.spoiler1 { border: 1px dotted rgba(255,255,255,0.5); }.spoiler2 { opacity: 0; }.spoiler2:hover { opacity: 1; }</style>
".        "<link rel='stylesheet' href='css/$themefile'>
".        "<link href='lib/prettify/sunburst.css' type='text/css' rel='stylesheet' />
".        "<script type='text/javascript' src='lib/prettify/prettify.js'></script>
".        "</head>
".        "<body style=font-size:$loguser[fontsize]% onload=\"prettyPrint()\">$dongs
".        "$L[TBL1]>
".        "  $L[TD1c] colspan=3>$boardlogo
".        "  $L[TR2c]>
".        "    $L[TD]><div style=\"width: 150px\">Views: <span title=\"And ".number_format($botviews)." views by search engine spiders.\">".number_format($views)."</span></div></td>
".        "    $L[TD] width=100%><span style='float:right'>$feedicons$ssllnk</span>
".        "      <a href=./>Main</a>
".        "    | <a href=faq.php>FAQ</a>
".        (has_perm('use-uploader')?"    | <a href=\"/uploader\">Uploader</a>":"")."
".        "    | <a href=\"irc.php\">IRC chat</a>
".        "    | <a href=memberlist.php>Memberlist</a>
".        "    | <a href=activeusers.php>Active users</a>
".        "    | <a href=thread.php?time=86400>Latest posts</a>
".        (has_perm('view-calendar')?"    | <a href=calendar.php>Calendar</a>":"")."
".        "    | <a href=stats.php>Stats</a>
".        "    | <a href=online.php>Online users</a>
".        "    | <a href=search.php>Search</a>
".        "    </td>
".        "    $L[TD]><div style=\"width: 150px\">".cdate($dateformat,ctime())."</div></td>
".        "  $L[TR1c]>
".        "    $L[TD] colspan=3>
".        "      ".($log?userlink($logbar):'Guest').": 
";

  if($log){
    //2/25/2007 xkeeper - framework laid out. Naturally, the SQL queries are a -mess-. --;
    $pmsgs=$sql->fetchq("SELECT p.id id, p.date date, u.id uid, u.name uname, u.displayname udisplayname, u.sex usex, u.power upower "
                       ."FROM pmsgs p "
                       ."LEFT JOIN users u ON u.id=p.userfrom "
                       ."WHERE p.userto=$loguser[id] "
                       ."ORDER BY date DESC LIMIT 1");

    $unreadpms=$sql->resultq("SELECT COUNT(*) FROM pmsgs WHERE userto=$loguser[id] AND unread=1 AND del_to=0");
    $totalpms =$sql->resultq("SELECT COUNT(*) FROM pmsgs WHERE userto=$loguser[id] AND del_to=0");

    if($unreadpms){
      $status='<img src=img/status/new.png>';
      $unreadpms=" ($unreadpms new)";
    }else{
      $status='&nbsp;';
      $unreadpms='';
    }

    if($totalpms>0)
       $lastmsg = "";
       
    else
      $lastmsg='';
if (has_perm('view-own-pms')) {
    if ($unreadpms){
      $pmimage = "gfx/pm.png";
    }
    else {
      $pmimage = "gfx/pm-off.png";
    } 
    $pmsgbox=
      "<a href=private.php><img src=$pmimage border=0 alt=\"Private messages\" title=\"Private message\"></a> $unreadpms $lastmsg | ";
}
else {
  $pmsgbox = "";

}
  }
  echo $pmsgbox;



      //mark forum read
      checknumeric($fid);
      if($fid)
        $markread=array('url' => "index.php?action=markread&fid=$fid", 'title' => "Mark Forum Read");
      else
        $markread=array('url' => "index.php?action=markread&fid=all", 'title' => "Mark All Forums Read");

    $userlinks = array();
    $ul=0;

    if (has_perm('register')) 
      $userlinks[$ul++] = array('url' => "register.php", 'title' => 'Register');
    if (has_perm('view-login')) 
      $userlinks[$ul++] = array('url' => "login.php", 'title' => 'Login');
    if (has_perm('logout')) 
      $userlinks[$ul++] = array('url' => "javascript:document.logout.submit()", 'title' => 'Logout');
    if (has_perm('update-own-profile')) 
      $userlinks[$ul++] = array('url' => "editprofile.php", 'title' => 'Edit Profile');
	if (has_perm('post-radar')) 
      $userlinks[$ul++] = array('url' => "postradar.php", 'title' => 'Post Radar');
    if (has_perm('edit-forums')) 
      $userlinks[$ul++] = array('url' => "manageforums.php", 'title' => 'Manage Forums');
    if (has_perm('edit-ip-bans')) 
      $userlinks[$ul++] = array('url' => "ipbans.php", 'title' => 'Manage IP Bans');
    if (has_perm('view-own-sprites')) 
      $userlinks[$ul++] = array('url' => "sprites.php", 'title' => 'My Sprites');
    if (has_perm('edit-sprites')) 
      $userlinks[$ul++] = array('url' => "editsprites.php", 'title' => 'Manage Sprites');
    if (has_perm('update-own-moods')) 
      $userlinks[$ul++] = array('url' => "usermood.php", 'title' => 'Edit Mood Avatars');
    if (has_perm('use-item-shop')) 
      $userlinks[$ul++] = array('url' => "shop.php", 'title' => 'Item Shop');
    if (has_perm('edit-groups')) 
      $userlinks[$ul++] = array('url' => "editgroups.php", 'title' => 'Edit Groups');
    if (has_perm('view-acs-calendar')) 
      $userlinks[$ul++] = array('url' => "frank.php", 'title' => 'Rankings');
    if (has_perm('mark-read')) 
      $userlinks[$ul++] = $markread;

    $c=0;

    foreach ($userlinks as $k => $v) {
      if ($c > 0) echo " | ";
      echo "<a href=".$v['url'].">".$v['title']."</a>";
      $c++;
    }

    print "    </td>
".        "    <form action=login.php method=post name=logout>
".        "      $L[INPh]=action value=logout>
".        "      $L[INPh]=p value=".md5($loguser[pass].$pwdsalt).">
".        "    </form>";
if ($radar) {
echo    "  $L[TR]>
".      "    $L[TD1] colspan=3>
".      "      $L[TBL] width=100%>
".      "        $L[TDn] width=250>
".      "          &nbsp;
".      "        </td>
".      "        $L[TDnc] width=100%>
".      "          $radar
".      "        </td>
".      "      $L[TBLend]";
}

  $hiddencheck  = "AND hidden=0 ";
  if (has_perm('view-hidden-users')) {
    $hiddencheck = "";
  }



    if($fid) {



      $onusers=$sql->query('SELECT id,name,displayname,sex,power,lastpost,lastview,minipic,hidden FROM users '
                          .'WHERE (lastview>'.(ctime()-300).'  '
                              .'OR lastpost>'.(ctime()-300).") $hiddencheck "
           ."AND lastforum='$fid' "
                          .'ORDER BY name');
      $onuserlist='';
      $onusercount=0;
      while($user=$sql->fetch($onusers)){
        $user[showminipic]=1;
        $onuserlog=($user[lastpost]<=$user[lastview]);
        $«=($onuserlog?'':'(');
        $»=($onuserlog?'':')');
        $onuserlist.=($onusercount?', ':'').$«.($user[hidden] ? '('.userlink($user).')' : userlink($user)).$»;
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

echo "</tr>
".          "  $L[TR1]>
".          "    $L[TD1c] colspan=3>$onuserlist
              </td>
              </tr>
";
    }


    else if ($showonusers) { 
  //[KAWA] Copypastadaption from ABXD, with added activity limiter.
  $birthdayLimit = 86400 * 30; //should be 30 days. Adjust if you want.
  $rBirthdays = $sql->query("select birth, id, name, displayname, power, sex from users where birth > 0 and lastview > ".(time()-$birthdayLimit)." order by name");
  $birthdays = array();
  while($user = $sql->fetch($rBirthdays))
  {
    $b = $user['birth'];
    if(date("m-d", $b) == date("m-d",ctime()))
    {
      $y = date("Y") - date("Y", $b);
      $birthdays[] = UserLink($user)." (".$y.")";
    }
  }
  if(count($birthdays))
  {
    $birthdaysToday = implode(", ", $birthdays);
    $birthdaybox =
print   "  $L[TR1c]>
".      "    $L[TD2c] colspan=3>
".      "      Birthdays today: $birthdaysToday
";
}



  $count[d]=$sql->resultq('SELECT COUNT(*) FROM posts WHERE date>'.(ctime()-86400));
  $count[h]=$sql->resultq('SELECT COUNT(*) FROM posts WHERE date>'.(ctime()-3600));
  $lastuser=$sql->fetchq('SELECT id,name,displayname,sex,power FROM users ORDER BY id DESC LIMIT 1');

  $hiddencheck  = "AND hidden=0 ";
  if (has_perm('view-hidden-users')) {
    $hiddencheck = "";
  }

  $onusers=$sql->query('SELECT id,name,displayname,sex,power,lastpost,lastview,minipic,hidden FROM users '
                      .'WHERE (lastview>'.(ctime()-300).' '
                         .'OR lastpost>'.(ctime()-300).") $hiddencheck "
                      .'ORDER BY name');
  $onuserlist='';
  $onusercount=0;
  while($user=$sql->fetch($onusers)){
    $user[showminipic]=1;
    $onuserlog=($user[lastpost]<=$user[lastview]);
    $«=($onuserlog?'':'(');
    $»=($onuserlog?'':')');
    $onuserlist.=($onusercount?', ':'').$«.($user[hidden] ? '('.userlink($user).')' : userlink($user)).$»;
    $onusercount++;
  }

  $maxpostsday =$sql->resultq('SELECT intval FROM misc WHERE field="maxpostsday"');
  $maxpostshour=$sql->resultq('SELECT intval FROM misc WHERE field="maxpostshour"');
  $maxusers    =$sql->resultq('SELECT intval FROM misc WHERE field="maxusers"');

  if($count[d]>$maxpostsday){
    $sql->query("UPDATE misc SET intval=$count[d] WHERE field='maxpostsday'");
    $sql->query("UPDATE misc SET intval=".ctime()." WHERE field='maxpostsdaydate'");
  }
  if($count[h]>$maxpostshour){
    $sql->query("UPDATE misc SET intval=$count[h] WHERE field='maxpostshour'");
    $sql->query("UPDATE misc SET intval=".ctime()." WHERE field='maxpostshourdate'");
  }
  if($onusercount>$maxusers){
    $sql->query("UPDATE misc SET intval=$onusercount WHERE field='maxusers'");
    $sql->query("UPDATE misc SET intval=".ctime()." WHERE field='maxusersdate'");
    $sql->query("UPDATE misc SET txtval='".addslashes($onuserlist)."' WHERE field='maxuserstext'");
  }

  $onuserlist="$onusercount user".($onusercount!=1?'s':'').' online'.($onusercount>0?': ':'').$onuserlist;
  $numguests=$sql->resultq('SELECT count(*) FROM guests WHERE `bot`=0 AND date>'.(ctime()-300));
  if($numguests)
    $onuserlist.=" | $numguests guest".($numguests>1?'s':'');
  $numbots=$sql->resultq('SELECT count(*) FROM guests WHERE `bot`=1 AND date>'.(ctime()-300));
  if($numbots)
    $onuserlist.=" | $numbots bot".($numbots>1?'s':'');

echo "
".      "  $L[TR]>
".      "    $L[TD1] colspan=3>
".      "      $L[TBL] width=100%>
".      "        $L[TDn] width=250>
".      "          &nbsp;
".      "        </td>
".      "        $L[TDnc]><nobr>
".      "          $count[t] threads and $count[p] posts total | 
".      "          $count[d] new posts today, $count[h] last hour.<br />".$sql->resultq("SELECT COUNT(*) FROM `users` WHERE `lastpost` > '". (ctime() - 86400) ."'") ."  active users and  ".$sql->resultq("SELECT COUNT(*) FROM `threads` WHERE `lastdate` > '". (ctime() - 86400) ."'") ." active threads during the last day. <br /> 
 </nobr>
".      "        </td>
".      "        $L[TDnr] width=250>
".      "          $count[u] registered users<br>
".      "          Newest: ".userlink($lastuser)."
".      "        </td>
".      "      $L[TBLend]
".      "  $L[TR]>
".      "    $L[TD2c] colspan=3>
".      "      $onuserlist";


    }




echo "
".        "$L[TBLend]
".        "<br>
";


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
".        "  Acmlmboard v2.5 (7/01/2012)<br>
".        "  &copy; 2005-2012 Acmlm, blackhole89, Xkeeper, Sukasa, Kawa, Bouche, Emuz, et al.
".        "$L[TBLend]";
    pagestats();
//	miscbar(); disabled until needed. -Emuz
  }



?>
