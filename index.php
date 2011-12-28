<?php
  /* index.php ****************************************
    Changelog
    Xkeeper     fixed what blackhole89 broke, which was mostly nothing
    blackhole89 moved mark forum /all forums read here
    blackhole89 added consideration of minpower for forum/category display
    Xkeeper     added support for category ordering
  ****************************************************/


  if($p=$_GET[p]) return header("Location:thread.php?pid=$p#$p");
  if($t=$_GET[t]) return header("Location:thread.php?id=$t");
  if($u=$_GET[u]) return header("Location:profile.php?id=$u");

  require 'lib/common.php';

  //mark forum read
  if($log && $_GET[action]=='markread'){
    if($fid!='all'){
      checknumeric($fid);
      //delete obsolete threadsread entries
      $sql->query("DELETE r "
                 ."FROM threadsread r "
                 ."LEFT JOIN threads t ON t.id=r.tid "
                 ."WHERE t.forum=$fid "
                 ."AND r.uid=$loguser[id]");
      //add new forumsread entry
      $sql->query("REPLACE INTO forumsread VALUES ($loguser[id],$fid,".ctime().')');
    } else {
      //mark all read
      $sql->query("DELETE FROM threadsread WHERE uid=$loguser[id]");
      $sql->query("REPLACE INTO forumsread (uid,fid,time) SELECT $loguser[id],f.id,".ctime()." FROM forums f");
    }

    // remove nasty GET strings so that refreshers like me don't mark things read over and over and burp
    header('Location: index.php');
  }
       
  // Moved pageheader here so that we can do header()s without fucking everything up again
  pageheader();


	//[KAWA] Copypastadaption from ABXD, with added activity limiter.
	$birthdayLimit = 86400 * 30; //should be 30 days. Adjust if you want.
	$rBirthdays = $sql->query("select birth, id, name, power, sex from users where birth > 0 and lastview > ".(time()-$birthdayLimit)." order by name");
	$birthdays = array();
	while($user = $sql->fetch($rBirthdays))
	{
		$b = $user['birth'];
		if(gmdate("m-d", $b) == gmdate("m-d"))
		{
			$y = gmdate("Y") - gmdate("Y", $b);
			$birthdays[] = UserLink($user)." (".$y.")";
		}
	}
	if(count($birthdays))
	{
		$birthdaysToday = implode(", ", $birthdays);
		$birthdaybox =
        "$L[TBL1]>
".      "  $L[TR1c]>
".      "    $L[TD2c]>
".      "      Birthdays today: $birthdaysToday
".      "  $L[TBLend]
".      "  <br>
";
}

  if($log){
    //2/25/2007 xkeeper - framework laid out. Naturally, the SQL queries are a -mess-. --;
    $pmsgs=$sql->fetchq("SELECT p.id id, p.date date, u.id uid, u.name uname, u.sex usex, u.power upower "
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
      $lastmsg="<br>
".      "      <font class=sfont><a href=showprivate.php?id=$pmsgs[id]>Last message</a> from ".userlink($pmsgs,'u').' on '.cdate($dateformat,$pmsgs[date]).'.</font>';
    else
      $lastmsg='';

    $pmsgbox=
        "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] colspan=2>Private Messages</td>
".      "  $L[TR]>
".      "    $L[TD1] width=17>$status</td>
".      "    $L[TD2]>
".      "      <a href=private.php>Private messages</a> -- You have $totalpms private message".($totalpms!=1?'s':'')."$unreadpms.$lastmsg
".      "  $L[TBLend]
".      "  <br>
";
  }

  $categs=$sql->query("SELECT * "
                     ."FROM categories "
                     ."WHERE minpower <= ". ($loguser['power'] < 0 ? 0 : $loguser['power']) ." "
                     ."ORDER BY ord");
  while($c=$sql->fetch($categs))
    $categ[$c[id]]=$c;

	//[KAWA] ABXD does ignores with a very nice SQL trick that I think Mega-Mario came up with one day.
	//Unfortunately, this place is too hairy to add the trick to so I'll have to use a third query to collect the ignores. The first is categories. The second is the forum list itself.
	$ignores = array();
	$ignoreQ = $sql->query("SELECT * FROM ignoredforums WHERE uid = ".$loguser['id']);
	while($i = $sql->fetch($ignoreQ))
		$ignores[$i['fid']] = true;

  $forums=$sql->query("SELECT f.*".($log?", r.time rtime":'').", u.id uid, u.name uname, u.sex usex, u.power upower "
                     ."FROM forums f "
                     ."LEFT JOIN users u ON u.id=f.lastuser "
                     ."LEFT JOIN categories c ON c.id=f.cat "
               .($log?"LEFT JOIN forumsread r ON r.fid=f.id AND r.uid=$loguser[id] ":'')
                     ."WHERE f.minpower<=". ($loguser['power'] < 0 ? 0 : $loguser['power']) ." "
                     .  "AND c.minpower<=". ($loguser['power'] < 0 ? 0 : $loguser['power']) ." "
                     ."ORDER BY c.ord,ord");
  $cat=-1;

  $count[d]=$sql->resultq('SELECT COUNT(*) FROM posts WHERE date>'.(ctime()-86400));
  $count[h]=$sql->resultq('SELECT COUNT(*) FROM posts WHERE date>'.(ctime()-3600));
  $lastuser=$sql->fetchq('SELECT id,name,sex,power FROM users ORDER BY id DESC LIMIT 1');

  $onusers=$sql->query('SELECT id,name,sex,power,lastpost,lastview,minipic FROM users '
                      .'WHERE lastview>'.(ctime()-300).' '
                         .'OR lastpost>'.(ctime()-300).' '
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

  print "$L[TBL1]>
".      "  $L[TR]>
".      "    $L[TD1]>
".      "      $L[TBL] width=100%>
".      "        $L[TDn] width=250>
".      "          &nbsp;
".      "        </td>
".      "        $L[TDnc]><nobr>
".      "          $count[t] threads and $count[p] posts total<br>
".      "          $count[d] new posts today, $count[h] last hour</nobr>
".      "        </td>
".      "        $L[TDnr] width=250>
".      "          $count[u] registered users<br>
".      "          Newest: ".userlink($lastuser)."
".      "        </td>
".      "      $L[TBLend]
".      "  $L[TR]>
".      "    $L[TD2c]>
".      "      $onuserlist
".      "$L[TBLend]
".      "<br>
".		"$birthdaybox
".      "$pmsgbox
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=17>&nbsp;</td>
".      "    $L[TDh]>Forum</td>
".      "    $L[TDh] width=50>Threads</td>
".      "    $L[TDh] width=50>Posts</td>
".      "    $L[TDh] width=150>Last post</td>
";

  while($forum=$sql->fetch($forums)){
    if($forum[cat]!=$cat){
      $cat=$forum[cat];
        print "  $L[TRg]>
".            "    $L[TD] colspan=5>".($categ[$cat][title])."</td>
";
    }

    if($forum[posts]>0 && $forum[lastdate]>0)
      $lastpost='<nobr>'.cdate($dateformat,$forum[lastdate]).'</nobr><br><font class=sfont>by&nbsp;'.userlink($forum,'u')."&nbsp;<a href='thread.php?pid=$forum[lastid]#$forum[lastid]'>&raquo;</a></font>";
    else
      $lastpost='None';

    if($forum[lastdate]>($log?$forum[rtime]:ctime()-3600))
      $status="<img src=img/status/new.png>";
    else
      $status='&nbsp;';

	if($ignores[$forum['id']])
	{
		$status = "&nbsp;";
		$ignoreFX = "style=\"opacity: 0.5;\"";
	}
	else
		$ignoreFX = "";

    $modstring="";
    $a=$sql->query("SELECT u.name,u.id,u.sex,u.power FROM forummods f, users u WHERE f.fid=$forum[id] AND u.id=f.uid");
    while($mod=$sql->fetch($a)) $modstring.=userlink($mod).", ";
    if($modstring) $modstring=" (moderated by: ".substr($modstring,0,-2).")";

    print
        "  $L[TRc]>
".      "    $L[TD1]>$status</td>
".      "    $L[TD2l]>
".      "      <a href=forum.php?id=$forum[id] $ignoreFX>$forum[title]</a><br>
".      "      <font class=sfont $ignoreFX>". str_replace("%%%SPATULANDOM%%%", $spatulas[$spaturand], $forum[descr]) ."$modstring</font>
".      "    </td>
".      "    $L[TD1]>$forum[threads]</td>
".      "    $L[TD1]>$forum[posts]</td>
".      "    $L[TD2]>$lastpost</td>
";
  }
  print "$L[TBLend]
";
  pagefooter();
?>
