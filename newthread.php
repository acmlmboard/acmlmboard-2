<?php
  require 'lib/common.php';
  require 'lib/threadpost.php';
  loadsmilies();

  if($act=$_POST[action]){
    $fid=$_POST[fid];

    if($_POST[passenc])
      $pass=$_POST[passenc];
    else
      $pass=md5($_POST[pass]);

    if($userid=checkuser($_POST[name],$pass))
      $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");
    else
      $err="    Invalid username or password!<br>
".         "    <a href=forum.php?id=$fid>Back to forum</a> or <a href=newthread.php?id=$fid>try again</a>";
  }else{
    $user=$loguser;
    $fid=$_GET[id];
  }
  checknumeric($fid);

  $forum=$sql->fetchq("SELECT * FROM forums WHERE id=$fid");
  pageheader('New thread',$forum[id]);

  echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
  $toolbar= posttoolbutton("message","B","[b]","[/b]")
           .posttoolbutton("message","I","[i]","[/i]")
           .posttoolbutton("message","U","[u]","[/u]")
           .posttoolbutton("message","S","[s]","[/s]")
	   ."$L[TD2]>&nbsp;</td>"
           .posttoolbutton("message","!","[spoiler]","[/spoiler]","sp")
           .posttoolbutton("message","&#133;","[quote]","[/quote]","qt")
           .posttoolbutton("message",";","[code]","[/code]","cd")
           ."$L[TD2]>&nbsp;</td>"
           .posttoolbutton("message","<font face='serif' style='font-size:1em'>&pi;</font>","[math]","[/math]","tx")
           .posttoolbutton("message","%","[svg <WIDTH> <HEIGHT>]","[/svg]","sv")
	   .posttoolbutton("message","<span style='font-weight:normal;font-size:2em;line-height:50%'>&#x21AF;</span>","[swf <WIDTH> <HEIGHT>]","[/swf]","fl");

  $tagsin="";
  $t=$sql->query("SELECT * FROM tags WHERE fid=$fid");
  while($tt=$sql->fetch($t)) {
    if($tagsin=="") $tagsin=
          "$L[TR]>
".        "  $L[TD1c]>Thread tags:</td>
".        "  $L[TD2]>
";
    $tagsin.="<input type='checkbox' name='tag$tt[bit]' id='tag$tt[bit]' value='1' ".($_POST["tag$tt[bit]"]?"checked":"")."><label for='tag$tt[bit]'>$tt[name]</label> ";
  }
  if($tagsin!="") $tags.="</tr>";

  $forumlink="<a href=forum.php?id=$fid>Back to forum</a>";

  if(!$forum)
    $err="    The specified forum doesn't exist!";

  else if($forum[minpowerthread]>$user[power]){
    if(isbanned())
      $err="    You can't post when you are banned!<br>
".         "    $forumlink";
    else
      $err="    You can't post in this restricted forum!<br>
".         "    $forumlink";
  }

  else if($user[lastpost]>ctime()-30 && $act=='Submit')
      $err="    Don't post threads so fast, wait a little longer.<br>
".         "    $forumlink";

  //2007-02-19 //blackhole89 - table breach protection
  if($act=='Submit'){
    if(($tdepth=tvalidate($message))!=0)
      $err="    This post would disrupt the board's table layout! The calculated table depth is $tdepth.<br>
".         "    $forumlink";
    if($title=="")
      $err="    You must enter a thread title.<br>
".         "    $forumlink";
    if($ispoll && ($_POST[numopts]<1 || !isset($_POST[numopts])))
      $err="    You must add options to your poll.<br>
".         "    $forumlink";
    else if($ispoll) {
      for($i=0;$i<$_POST[numopts];++$i)
        if($_POST["opt$i"]=="" || $_POST["r$i"]=="" || $_POST["g$i"]=="" || $_POST["b$i"]=="")
          $err="You must fill in all poll options' fields.<br>
".             "$forumlink";
    }
  }

  $top="<a href=./>Main</a> - <a href=forum.php?id=$fid>$forum[title]</a> - New thread";

  $i=1;
  $icons=$sql->query('SELECT * FROM posticons ORDER BY id');
  while($icon=$sql->fetch($icons))
    $iconlist.=
          "      $L[INPr]=iconid value=$i> <img src=$icon[url]>&nbsp; &nbsp;".(!($i++%10)?'<br>':'')."
";
  $iconlist.=
          "      $L[INPr]=iconid value=0 checked> None&nbsp; &nbsp;
".        "      Custom: $L[INPt]=iconurl size=40 maxlength=100>
";

  if($err){
    print "$top - Error
".        "<br><br>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "$err
".        "$L[TBLend]
";
  }elseif(!$act){
    if($ispoll){
      $pollin=
          "$L[TR]>
".        "  $L[TD1c]>Poll question:</td>
".        "  $L[TD2]>$L[INPt]=question size=60 maxlength=60 value=\"".htmlval($_POST[question])."\"></td>
".        "$L[TR]>
".        "  $L[TD1c]>Number of options:</td>
".        "  $L[TD2]>$L[INPt]=numopts size=2 maxlength=2 value=\"".htmlval($_POST[numopts])."\"><br><font class=sfont>Press Preview to update the number of fields displayed.</font></td>
".        "  $L[INPh]=noptcache value=0>
".        "$L[TR]>
".             "  $L[TD1c]>Options:</td>
".             "  $L[TD2]>$L[INPc]=multivote value=1 id=mv><label for=mv>Allow multiple voting</label> | $L[INPc]=changeable checked value=1 id=ch><label for=ch>Allow changing one's vote</label>
";
    }
    print "$top
".        "<br><br>
".        "$L[TBL1]>
".        " <form action=newthread.php?ispoll=$ispoll method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Thread</td>
";
    if(!$log)
    print "  $L[TR]>
".        "    $L[TD1c]>Username:</td>
".        "    $L[TD2]>$L[INPt]=name size=25 maxlength=25></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Password:</td>
".        "    $L[TD2]>$L[INPp]=pass size=13 maxlength=32></td>
";
    else
    print "  $L[INPh]=name value=\"".htmlval($loguser[name])."\">
".        "  $L[INPh]=passenc value=$loguser[pass]>
";
    print "  $L[TR]>
".        "    $L[TD1c]>Thread title:</td>
".        "    $L[TD2]>$L[INPt]=title size=60 maxlength=60></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Thread icon:</td>
".        "    $L[TD2]>
".        "$iconlist
".        "    </td>
".$tagsin."
".$pollin."
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Post:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=20 cols=80></textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPh]=fid value=$fid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        // 2009-07 Sukasa: Newthread mood selector, just in the place I put it in mine
          "      $L[INPl]=mid>".moodlist()."
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($_POST[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Preview'){
    $_POST[title]  =stripslashes($_POST[title]);
    $_POST[message]=stripslashes($_POST[message]);

    $post[date]=ctime();
    $post[ip]=$userip;
    $post[num]=++$user[posts];
    $post[text]=$_POST[message];
    $post[mood] = (isset($_POST[mid]) ? $_POST[mid] : -1); // 2009-07 Sukasa: Newthread preview
    $post[nolayout]=$_POST[nolayout];
    foreach($user as $field => $val)
      $post[u.$field]=$val;
    $post[ulastpost]=ctime();

    if($ispoll){
      $_POST[question]=stripslashes($_POST[question]);
      $numopts=$_POST[numopts];
      checknumeric($numopts);
      $pollin=
          "$L[TR]>
".        "  $L[TD1c]>Poll question:</td>
".        "  $L[TD2]>$L[INPt]=question size=60 maxlength=60 value=\"".htmlval($_POST[question])."\"></td>
".        "$L[TR]>
".        "  $L[TD1c]>Number of options:</td>
".        "  $L[TD2]>$L[INPt]=numopts size=2 maxlength=2 value=\"".htmlval($_POST[numopts])."\"><br><font class=sfont>Press Preview to update the number of fields displayed.</font></td>
".        "  $L[INPh]=noptcache value=$numopts>
";
      for($i=$noptcache;$i<$numopts;++$i){
        $_POST["r$i"]=rand(0,255);
        $_POST["g$i"]=rand(0,255);
        $_POST["b$i"]=rand(0,255);
      }
      for($i=0;$i<$numopts;++$i){
        $_POST["opt$i"]=stripslashes($_POST["opt$i"]);
        $pollin.="$L[TR]>$L[TD1c]>Option ".($i+1).":</td>"
                       ."$L[TD2] >$L[INPt]=opt$i size=40 maxlength=40 value=\"".htmlval($_POST["opt$i"])."\">"
                                ." - RGB color: $L[INPt]=r$i size=3 maxlength=3 value=\"".htmlval($_POST["r$i"])."\">"
                                              ."$L[INPt]=g$i size=3 maxlength=3 value=\"".htmlval($_POST["g$i"])."\">"
                                              ."$L[INPt]=b$i size=3 maxlength=3 value=\"".htmlval($_POST["b$i"])."\">";
      }
      $pollin.="$L[TR]>
".             "  $L[TD1c]>Options:</td>
".             "  $L[TD2]>$L[INPc]=multivote ".($_POST[multivote]?"checked":"")." value=1 id=mv><label for=mv>Allow multiple voting</label> | $L[INPc]=changeable ".($_POST[changeable]?"checked":"")." value=1 id=ch><label for=ch>Allow changing one's vote</label>
";
    }

    print "$top - Preview
".        "<br>
".        "$L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Post preview
".        "$L[TBLend]
".         threadpost($post,0)."
".        "<br>
".        "$L[TBL1]>
".        " <form action=newthread.php?ispoll=$ispoll method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Thread</td>
".        "  $L[TR]>
".        "    $L[TD1c]>Thread title:</td>
".        "    $L[TD2]>$L[INPt]=title size=60 maxlength=60 value=\"".htmlval($_POST[title])."\"></td>
".$tagsin."
".$pollin."
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Post:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=10 cols=80>".htmlval($_POST[message])."</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPh]=name value=\"".htmlval(stripslashes($_POST[name]))."\">
".        "      $L[INPh]=passenc value=$pass>
".        "      $L[INPh]=fid value=$fid>
".        "      $L[INPh]=iconid value=$_POST[iconid]>
".        "      $L[INPh]=iconurl value=$_POST[iconurl]>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        // 2009-07 Sukasa: Newthread mood selector, just in the place I put it in mine
          "      $L[INPl]=mid>".moodlist()."
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($post[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Submit'){
    if(!($iconurl=$_POST[iconurl]))
      $iconurl=$sql->resultq("SELECT url FROM posticons WHERE id=$_POST[iconid]");

    checknumeric($nolayout);

    $iconurl=str_replace("\"","",$iconurl);
    $iconurl=str_replace("\'","&quot;",$iconurl);

    $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");
    $user[posts]++;

    $tagsum=0;
    for($i=0;$i<32;++$i) if($_POST["tag$i"]) $tagsum|=(1<<$i);
    
    $mid=(isset($_POST[mid]) ? $_POST[mid] : -1); //2009/07 Sukasa: Last I checked, there was a magic_quotes_gpc in effect on board2, which makes this okay

    $sql->query("UPDATE users SET posts=posts+1,threads=threads+1,lastpost=".ctime()." "
               ."WHERE id=$userid");
    $sql->query("INSERT INTO threads (title,forum,user,lastdate,lastuser,icon,tags) "
               ."VALUES ('$_POST[title]',$fid,$userid,".ctime().",$userid,'$iconurl',$tagsum)");
    $tid=mysql_insert_id();
    $sql->query("INSERT INTO posts (user,thread,date,ip,num,mood,nolayout) "
               ."VALUES ($userid,$tid,".ctime().",'$userip',$user[posts],$mid,$nolayout)");
    $pid=mysql_insert_id();
    $sql->query("INSERT INTO poststext (id,text) VALUES ($pid,'$message')");
    $sql->query("UPDATE forums SET threads=threads+1,posts=posts+1,lastdate=".ctime().",lastuser=$userid,lastid=$pid "
               ."WHERE id=$fid");
    $sql->query("UPDATE threads SET lastid=$pid WHERE id=$tid");

    if($ispoll)
    {
//      $multivote=0;
      $sql->query("INSERT INTO polls (id,question,multivote,changeable) VALUES ($tid,'".htmlentities($question)."','$multivote','$changeable')");
      for($i=0;$i<$numopts;++$i)
        $sql->query("INSERT INTO polloptions (`poll`,`option`,r,g,b) VALUES ($tid,'".htmlentities($_POST["opt$i"])."','".htmlentities($_POST["r$i"])."','".htmlentities($_POST["g$i"])."','".htmlentities($_POST["b$i"])."')");
    }

    // bonus shit
    $c = rand(250,750);
    $sql->query("UPDATE `usersrpg` SET `spent` = `spent` - '$c' WHERE `id` = '$userid'");

//    if($forum[minpower]<=0) sendirc("\x0314New thread by \x0309$user[name]\x0314 in \x0307". $forum[title] ."\x0314: \x0307".stripslashes($_POST[title])."\x0314 - \x0303{boardurl}?t=$tid");
//    else sendirc("S\x0314New thread by \x0309$user[name]\x0314 in \x0307". $forum[title] ."\x0314: \x0307".stripslashes($_POST[title])."\x0314 - \x0303{boardurl}?t=$tid");
    if($forum[minpower]<=0) sendirc("\x036New thread by \x0313$user[name]\x036 in \x0313$forum[title]\x034: \x0313".stripslashes($_POST[title])."\x036 - \x034{boardurl}?t=$tid");
    else sendirc("S\x036New thread by \x0313$user[name]\x036 in \x0313$forum[title]\x034: \x0313".stripslashes($_POST[title])."\x036 - \x034{boardurl}?t=$tid");

    print "$top - Submit
".        "<br><br>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Posted! (Gained $c bonus coins)<br>
".        "    ".redirect("thread.php?id=$tid",'the thread')."
".        "$L[TBLend]
";
  }

  pagefooter();

  function moodlist() { // 2009-07 Sukasa: It occurred to me that this would be better off in function.php, but last I checked
                        // it was owned by root.
    global $sql, $loguser;
    $mid = (isset($_POST[mid]) ? $_POST[mid] : -1);
    $moods = $sql->query("select '-Normal Avatar-' label, -1 id union select label, id from mood where user=$loguser[id]");
    $moodst="";
    while ($mood=$sql->fetch($moods))
      $moodst.= "<option value=\"$mood[id]\"".($mood[id]==$mid?"selected=\"selected\"":"").">$mood[label]</option>";
    $moodst.= "</select>";
    return $moodst;
  }
?>
