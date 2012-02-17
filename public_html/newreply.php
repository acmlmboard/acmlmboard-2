<?php
  /* newreply.php ****************************************
    Changelog
0221  blackhole89       related to thread-individual "NEW" display system
0220  blackhole89       added minpower check for displaying the thread's
                        previous contents. (yes, it is possible to make a forum
                        with minpowerreply < minpower and allow users to "reply blindly" now)
  */

  require 'lib/common.php';
  require 'lib/threadpost.php';
  loadsmilies();

  if($act=$_POST[action]){
    $tid=$_POST[tid];

    if($_POST[passenc])
      $pass=$_POST[passenc];
    else
      $pass=md5($_POST[pass].$pwdsalt);

    if($userid=checkuser($_POST[name],$pass))
      $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");
    else {
      $err="    Invalid username or password!<br>
".         "    <a href=thread.php?id=$tid>Back to thread</a> or <a href=newreply.php?id=$tid>try again</a>";
      $user[power]=0;
    }
  }else{
    $user=$loguser;
    $tid=$_GET[id];
  }
  checknumeric($tid);

  needs_login(1);


  if($act!='Submit'){
    $fieldlist='';
    $ufields=array('id','name','displayname','posts','sex','power');
    foreach($ufields as $field)
      $fieldlist.="u.$field u$field,";

    $posts=$sql->query("SELECT $fieldlist p.*, pt1.text "
                      .'FROM posts p '
                      .'LEFT JOIN poststext pt1 ON p.id=pt1.id '
                      .'LEFT JOIN poststext pt2 ON pt2.id=pt1.id AND pt2.revision=(pt1.revision+1) '
                      .'LEFT JOIN users u ON p.user=u.id '
                      ."WHERE p.thread=$tid "
                      ."  AND ISNULL(pt2.id) "
                      .'ORDER BY p.id DESC '
                      ."LIMIT $loguser[ppp]");
  }

  $thread=$sql->fetchq('SELECT t.*, f.title ftitle, f.minpowerreply, f.minpower '
                      .'FROM threads t '
                      .'LEFT JOIN forums f ON f.id=t.forum '
                      ."WHERE t.id=$tid AND t.forum IN ".forums_with_view_perm());

  if($act!="Submit" || $loguser[redirtype]==0){ //We don't render the header for a "Modern" redirect.
    pageheader('New reply',$thread[forum]);
    echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
}
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

  $threadlink="<a href=thread.php?id=$tid>Back to thread</a>";

  if(!$thread) {
//      $err="    The specified thread doesn't exist!";

    thread_not_found();
    }


     else if (!can_create_forum_post($thread[forum])){

       $err="    You have no permissions to create posts in this forum!<br>$forumlink";
    }
/*  elseif($thread[minpowerreply]>$user[power]){
    if(isbanned())
      $err="    You can't post when you are banned!<br>
".         "    $threadlink";
    else
      $err="    You can't post in this restricted forum!<br>
".         "    $threadlink";
  }
  elseif($thread[minpower]>$user[power]){
    $err="      You can't post in this restricted forum!<br>
".       "      $threadlink";
  }*/
  elseif($thread[closed]){
      $err="    You can't post in closed threads!<br>
".         "    $threadlink";
  }

  if($act=='Submit'){
    $message = $_POST[message];
    if($thread[lastuser]==$userid && $thread[lastdate]>=(ctime()-86400) && !has_perm('consecutive-posts'))  // admins can double post all they want
      $err="    You can't double post until it's been at least one day!<br>
".         "    $threadlink";
    //2007-02-19 //blackhole89 - table breakdown protection
    if(($tdepth=tvalidate($message))!=0)
      $err="    This post would disrupt the board's table layout! The calculated table depth is $tdepth.<br>
".         "    $threadlink";
    if(strlen(trim($message))==0)
      $err="    Your post is empty! Enter a message and try again.<br>
".         "    $threadlink";
    if($user[regdate]>(ctime()-60))
      $err="    You must wait 60 seconds before posting on a freshly registered account.<br>
".         "    $threadlink";
  }

  $top='<a href=./>Main</a> '
    ."- <a href=forum.php?id=$thread[forum]>$thread[ftitle]</a> "
    ."- <a href=thread.php?id=$thread[id]>".htmlval($thread[title]).'</a> '
    .'- New reply';


  if($pid=$_GET[pid]){
    checknumeric($pid);  //nice way of adding security, really. int_val doesn't really do it (floats and whatnot), so heh
    $post=$sql->fetchq("SELECT IF(u.displayname='',u.name,u.displayname) name, p.user, pt.text, f.minpower, p.thread "
                      ."FROM posts p "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
          ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) "
                      ."LEFT JOIN users u ON p.user=u.id "
          ."LEFT JOIN threads t ON t.id=p.thread "
          ."LEFT JOIN forums f ON f.id=t.forum "
                      ."WHERE p.id=$pid AND ISNULL(pt2.id)");
  
  //does the user have reading access to the quoted post?
  if(!can_view_forum(getforumbythread($post[thread]))) $post[text]="";

  $quotetext="[quote=\"$post[name]\" id=\"$pid\"]$post[text][/quote]";
  }

  //spambot logging [blackhole89]
  if($act=='Submit' && $_SERVER['HTTP_USER_AGENT'] == "Opera/9.0 (Windows NT 5.1; U; en)") {
    $sql->query("INSERT INTO ipbans (ipmask,expire) VALUES ('$userip',0)");
    $sql->query("INSERT INTO spambotlog VALUES ('$userip','$name','$_POST[pass]','$title','$message')");
  }

  if($err){
    if($loguser[redirtype]==1) pageheader('New reply',$thread[forum]);
    //print "$top - Error
    print "<a href=./>Main</a> - Error
".        "<br><br>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "$err
".        "$L[TBLend]
";
  }elseif(!$act){
    print "$top
".        "<br><br>
".        "$L[TBL1]>
".        " <form action=newreply.php method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Reply</td>
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
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Reply:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=20 cols=80>$quotetext</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPh]=tid value=$tid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        // 2009-07 Sukasa: Newreply mood selector, just in the place I put it in mine
          "      $L[INPl]=mid>".moodlist()." 
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($_POST[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Preview'){
    $_POST[message]=stripslashes($_POST[message]);

    $postfix=""; $prefix=""; $valid="";
    if(($a=tvalidate($message))>0) {
      for($i=0;$i<$a;++$i) $postfix.="</table>";
      $valid="$L[TR]> $L[TD1c] width=120>Table depth: $L[TD2]><font color=red><b>+$a</b></font> (You are opening more table tags than you are closing.)";
    }
    if(($a=tvalidate($message))<0) {
      for($i=0;$i<$a;++$i) $prefix.="<table>";
      $valid="$L[TR]> $L[TD1c] width=120>Table depth: $L[TD2]><font color=red><b>-x</b></font> (You are opening fewer table tags than you are closing.)";
    }


    $post[date]=ctime();
    $post[ip]=$userip;
    $post[num]=++$user[posts];
    $post[text]=$prefix.$_POST[message].$postfix;
    $post[mood] = (isset($_POST[mid]) ? $_POST[mid] : -1); // 2009-07 Sukasa: Newthread preview
    $post[nolayout]=$_POST[nolayout];
    foreach($user as $field => $val)
      $post[u.$field]=$val;
    $post[ulastpost]=ctime();

    print "$top - Preview
".        "<br>
".        "$L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Post preview
".        "$L[TBLend]
".         threadpost($post,0)."
".        "<br>
".        "$L[TBL1]>
".        " <form action=newreply.php method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Reply</td>
".           $valid."
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Reply:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=10 cols=80>".htmlval($_POST[message])."</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPh]=name value=\"".htmlval(stripslashes($_POST[name]))."\">
".        "      $L[INPh]=passenc value=$pass>
".        "      $L[INPh]=tid value=$tid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        // 2009-07 Sukasa: Newreply mood selector, just in the place I put it in mine
          "      $L[INPl]=mid>".moodlist()." 
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($post[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Submit'){
    checknumeric($nolayout);
    $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");
    $user[posts]++;
    $mid=(isset($_POST[mid]) ? $_POST[mid] : -1); //2009/07 Sukasa: Last I checked, there was a magic_quotes_gpc in effect on board2, which makes this okay
    $sql->query("UPDATE users SET posts=posts+1,lastpost=".ctime()." WHERE id=$userid");
    $sql->query("INSERT INTO posts (user,thread,date,ip,num,mood,nolayout) "
               ."VALUES ($userid,$tid,".ctime().",'$userip',$user[posts],$mid,$nolayout)");
    $pid=mysql_insert_id();
    $sql->query("INSERT INTO poststext (id,text) VALUES ($pid,'$message')");
    $sql->query("UPDATE threads SET replies=replies+1,lastdate=".ctime().",lastuser=$userid,lastid=$pid WHERE id=$tid");
    $sql->query("UPDATE forums SET posts=posts+1,lastdate=".ctime().",lastuser=$userid,lastid=$pid WHERE id=$thread[forum]");

    //2007-02-21 //blackhole89 - nuke entries of this thread in the "threadsread" table
    $sql->query("DELETE FROM threadsread WHERE tid='$thread[id]' AND NOT (uid='$userid')");

  // bonus shit
    $c = rand(100, 500);
    $sql->query("UPDATE `usersrpg` SET `spent` = `spent` - '$c' WHERE `id` = '$userid'");

   $chan = $sql->resultp("SELECT a.chan FROM forums f LEFT JOIN announcechans a ON f.announcechan_id=a.id WHERE f.id=?",array($thread['forum']));




//    if ($thread[minpower]<=0) sendirc("\x0314New reply by \x0309$user[name]\x0314 (\x0303$thread[ftitle]\x0314: \x0307$thread[title]\x0314 (\x0303$tid\x0314) (+$c)) - \x0303{boardurl}?p=$pid");
//    else sendirc("S\x0314New reply by \x0309$user[name]\x0314 (\x0303$thread[ftitle]\x0314: \x0307$thread[title]\x0314 (\x0303$tid\x0314) (+$c)) - \x0303{boardurl}?p=$pid");
/*    if ($thread[minpower]<=0) sendirc("\x036New reply by \x0313$user[name]\x034 (\x036$thread[ftitle]\x034: \x0313$thread[title]\x034 (\x036\x02\x02$tid\x034) (\x036+$c\x034))\x036 - \x034{boardurl}?p=$pid");
    else sendirc("S\x036New reply by \x0313$user[name]\x034 (\x036$thread[ftitle]\x034: \x0313$thread[title]\x034 (\x036\x02\x02$tid\x034) (\x036+$c\x034))\x036 - \x034{boardurl}?p=$pid");*/

sendirc("\x036New reply by \x0313".($user[displayname]?$user[displayname]:$user[name])."\x034 (\x036$thread[ftitle]\x034: \x0313$thread[title]\x034 (\x036\x02\x02$tid\x034) (\x036+$c\x034))\x036 - \x034{boardurl}?p=$pid",$chan);

if($loguser[redirtype]==0){ //Classical Redirect
    print "$top - Submit
".        "<br><br>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Posted! (Gained $c bonus coins)<br>
".        "    ".redirect("thread.php?pid=$pid#$pid",htmlval($thread[title]))."
".        "$L[TBLend]
";
} else { //Modern redirect
  redir2("thread.php?pid=$pid#$pid",$c);
}

  }

  if($act!='Submit' && !$err && $thread[minpower]<=$loguser[power]){
    print "<br>
".        "$L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Thread preview
".        "$L[TBLend]
";
    while($post=$sql->fetch($posts)){
      $exp=calcexp($post[uposts],ctime()-$post[uregdate]);
      print threadpost($post,1);
    }

    if($thread[replies]>=$loguser[ppp]){
    print "<br>
".        "$L[TBL1]>
".        "  $L[TR]>
".        "    $L[TD1]>The full thread can be viewed <a href=thread.php?id=$tid>here</a>.
".        "$L[TBLend]
";
    }
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