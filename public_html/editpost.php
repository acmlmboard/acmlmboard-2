<?php
  /* editpost.php ****************************************
    Changelog
0308  blackhole89   forked from newreply.php
0221  blackhole89       related to thread-individual "NEW" display system
0220  blackhole89       added minpower check for displaying the thread's
                        previous contents. (yes, it is possible to make a forum
                        with minpowerreply < minpower and allow users to "reply blindly" now)
  */

  require 'lib/common.php';
  require 'lib/threadpost.php';
  loadsmilies();

  if($act=$_POST[action]){
    $pid=$_POST[pid];  

    if($_POST[passenc])
      $pass=$_POST[passenc];
    else
      $pass=md5($_POST[pass].$pwdsalt);

    if($userid=checkuser($_POST[name],$pass))
      $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");
    else
      $err="    Invalid username or password!<br>
".         "    <a href=thread.php?id=$tid>Back to thread</a> or <a href=editpost.php?id=$tid>try again</a>";
  }else{
    $user=$loguser;
    $pid=$_GET[pid];
  }

  if($_GET[act]=='delete' || $_GET[act]=='undelete') {
    $act=$_GET[act];
    $pid=unpacksafenumeric($pid);
  }

  checknumeric($pid);

  needs_login(1);

  $thread=$sql->fetchq('SELECT t.*, f.title ftitle, f.minpowerreply, f.minpower '
                      .'FROM posts p '
                      .'LEFT JOIN threads t ON t.id=p.thread '
                      .'LEFT JOIN forums f ON f.id=t.forum '
                      ."WHERE p.id=$pid AND t.forum IN ".forums_with_view_perm());


  if (!$thread) $pid = 0;
if($loguser[redirtype]==0 || $act!="Submit"){ //Classical Redirect
  pageheader('Edit post',$thread[forum]);
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
           .posttoolbutton("message","YT","[youtube]","[/youtube]","yt")
           .posttoolbutton("message","%","[svg]","[/svg]","sv");

//  if($thread[minpowerreply]>$user[power]){
//    if(isbanned())
//      $err="    You can't edit a post when you are banned!<br>
//".         "    $threadlink";
//    else
//      $err="    You can't edit a post in this restricted forum!<br>
//".         "    $threadlink";
//  }

  if ($thread[closed] && !can_edit_forum_posts($thread[forum])) {
      $err="    You can't edit a post in closed threads!<br>
".         "    $threadlink";
  }
  else if (!can_edit_post($pid)) {
      $err="    You do not have permission to edit this post.<br>$threadlink";
  }
  elseif($pid==-1){
      $err="    Your PID code is invalid!<br>
".         "    $threadlink";
  }

  if($act=='Submit'){
    //2007-02-19 //blackhole89 - table breakdown protection
    if(($tdepth=tvalidate($message))!=0)
      $err="    This post would disrupt the board's table layout! The calculated table depth is $tdepth.<br>
".         "    $threadlink";
  }

  $top='<a href=./>Main</a> '
    ."- <a href=forum.php?id=$thread[forum]>$thread[ftitle]</a> "
    ."- <a href=thread.php?id=$thread[id]>".htmlval($thread[title]).'</a> '
    .'- Edit post';

  $res=$sql->query  ("SELECT u.id, p.user, p.mood, p.nolayout, pt.text "
                    ."FROM posts p "
                    ."LEFT JOIN poststext pt ON p.id=pt.id "
                    ."JOIN ("
                      ."SELECT id,MAX(revision) toprev FROM poststext GROUP BY id"
                    .") as pt2 ON pt2.id=pt.id AND pt2.toprev=pt.revision "
                    ."LEFT JOIN users u ON p.user=u.id "
                    ."WHERE p.id=$pid");

  if(@mysql_num_rows($res)<1)
    $err="    That post does not exist.";

  $post=$sql->fetch($res);
  $quotetext=str_replace("&","&amp",$post[text]);
if($act=="Submit" && $quotetext==$_POST[message]){
      $err="    No changes detected.<br>
".         "    $threadlink";
}
/*  if($post[id] != $loguser[id] && !ismod($thread[forum]))
    $err="    You may not edit this post.<br>
".       "    $threadlink";*/

  if($err){
if($loguser[redirtype]==1 && $act=="Submit"){ pageheader('Edit post',$thread[forum]); }
    print "$top - Error
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
".        " <form action=editpost.php method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Edit Post</td>
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
".        "    $L[TD1c] width=120>Post:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=20 cols=80>$quotetext</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPh]=pid value=$pid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        "      $L[INPl]=mid>".moodlist($post[mood])."
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($post[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Preview'){
    $_POST[message]=stripslashes($_POST[message]);
    $euser=$sql->fetchq("SELECT * FROM users WHERE id=$post[id]");
    $post[date]=ctime();
    $post[ip]=$userip;
    $post[num]=++$euser[posts];
    $post[mood]=(isset($_POST[mid]) ? $_POST[mid] : -1);
    $post[nolayout]=$_POST[nolayout];
    $post[text]=$_POST[message];
    foreach($euser as $field => $val)
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
".        " <form action=editpost.php method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Post</td>
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
".        "      $L[INPh]=pid value=$pid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        "      $L[INPl]=mid>".moodlist($post[mood])."
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($post[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Submit'){
    $message = $_POST['message'];
    $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");

    $rev=$sql->fetchq("SELECT MAX(revision) m FROM poststext WHERE id=$pid");
    $rev=$rev[m];
    $mid=(isset($_POST[mid])?$_POST[mid]:-1);
    checknumeric($mid);
    checknumeric($nolayout);
    ++$rev;
    $sql->query("INSERT INTO poststext (id,text,revision,user,date) VALUES ($pid,'$message',$rev,$userid,".ctime().")");
    $sql->query("UPDATE posts SET mood='$mid',nolayout='$nolayout' WHERE id='$pid'");

    $chan = $sql->resultp("SELECT a.chan FROM forums f LEFT JOIN announcechans a ON f.announcechan_id=a.id WHERE f.id=?",array($thread['forum']));

    if ($thread[announce]) {
      if ($thread[forum] == 0) {
    sendirc("{irccolor-base}Announcement edited by {irccolor-name}".get_irc_displayname()."{irccolor-url} ({irccolor-title}$thread[title]{irccolor-url}){irccolor-base} - {irccolor-url}{boardurl}?p=$pid{irccolor-base}",$chan);
      }
      else {
    sendirc("{irccolor-base}Announcement edited by {irccolor-name}".get_irc_displayname()."{irccolor-url} ({irccolor-title}$thread[ftitle]{irccolor-url}: {irccolor-name}$thread[title]{irccolor-url}){irccolor-base} - {irccolor-url}{boardurl}?p=$pid{irccolor-base}",$chan);
      }
    }
    else {
    sendirc("{irccolor-base}Post edited by {irccolor-name}".get_irc_displayname()."{irccolor-url} ({irccolor-title}$thread[ftitle]{irccolor-url}: {irccolor-name}$thread[title]{irccolor-url} ({irccolor-base}\x02\x02$thread[id]{irccolor-url})){irccolor-base} - {irccolor-url}{boardurl}?p=$pid{irccolor-base}",$chan);

    }
if($loguser[redirtype]==0){ //Classical Redirect
    print "$top - Submit
".        "<br><br>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Post edited!<br>
".        "    ".redirect("thread.php?pid=$pid#$pid",htmlval($thread[title]))."
".        "$L[TBLend]
";
} else { //Modern redirect
  redir2("thread.php?pid=$pid#edit","-1");
}
  }elseif($act=='delete' ||$act=='undelete'){
    if(!(can_delete_forum_posts($thread[forum]))) {
      print "$top - Error
".          "<br><br>
".          "$L[TBL1]>
".          "  $L[TD1c]>
".          "    You do not have the permission to do this.
".          "$L[TBLend]
";
    } else {
      $sql->query("UPDATE posts SET deleted=".($act=='delete'?1:0)." WHERE id='$pid'");
      print "$top - (Un)delete Post
".          "<br><br>
".          "$L[TBL1]>
".          "  $L[TD1c]>
".          "    Post ".$act."d!<br>
".          "    ".redirect("thread.php?pid=$pid#$pid",htmlval($thread[title]))."
".          "$L[TBLend]
";
    }
  }


  pagefooter();

  function moodlist($mid = -2) { //2009-09 Sukasa This has now been replicated three times.  It should probably be moved to function.php.
                        //But since that'll involved breaking part of the board for a bit, I'll do it later.
    global $sql, $loguser, $post;
    if ($mid == -2)
      $mid = (isset($_POST[mid]) ? $_POST[mid] : -1);
    $moods = $sql->query("select '-Normal Avatar-' label, -1 id union select label, id from mood where user=$post[id]");
    $moodst="";
    while ($mood=$sql->fetch($moods))
      $moodst.="<option value=\"$mood[id]\"".($mood[id]==$mid?" selected=\"selected\"":"").">$mood[label]</option>";
    $moodst.="</select>";
    return $moodst;
  }

?>