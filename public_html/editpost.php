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

  if($act=$_POST['action']) {
    check_token($_POST['auth']);
	$pid = (int) $_POST['pid'];  
  } else {
    $pid = (int) $_GET['pid'];
  }
  
  $userid = $loguser['id'];
  $user = $loguser;
 // $pass = md5($pwdsalt2.$loguser['pass'].$pwdsalt);

	if ($_GET['act'] == 'delete' || $_GET['act'] == 'undelete') {
		$act = $_GET['act'];
		check_token($_GET['auth'], $pid);
		//$pid=unpacksafenumeric($pid);
	}

  checknumeric($pid);

  needs_login(1);

  $thread=$sql->fetchq('SELECT p.user puser, t.*, f.title ftitle, f.private fprivate, f.readonly freadonly '
                      .'FROM posts p '
                      .'LEFT JOIN threads t ON t.id=p.thread '
                      .'LEFT JOIN forums f ON f.id=t.forum '
                      ."WHERE p.id=$pid AND t.forum IN ".forums_with_view_perm());


  if (!$thread) $pid = 0;
if($act!="Submit"){ //Classical Redirect
  echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
}
  $toolbar= posttoolbar();

  if ($thread[closed] && !can_edit_forum_posts($thread[forum])) {
      $err="    You can't edit a post in closed threads!<br>
".         "    $threadlink";
  }
  else if (!can_edit_post(array('user'=>$thread['puser'], 'tforum' => $thread['forum']))) {
      $err="    You do not have permission to edit this post.<br>$threadlink";
  }
  elseif($pid==-1){
      $err="    Your PID code is invalid!<br>
".         "    $threadlink";
  }

  if($act=='Submit'){
    $message = $_POST[message];
    if(strlen($message)>60000)  // Protection against huge posts getting cut off
      $err="    This post is too long. Maximum length: 60000 characters. <br>
".         "    $threadlink";
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

  if(@$sql->numrows($res)<1)
    $err="    That post does not exist.";

  $post=$sql->fetch($res);
  $quotetext=htmlval($post[text]);
if($act=="Submit" && $post['text']==$_POST[message]){
      $err="    No changes detected.<br>
".         "    $threadlink";
}

  if($err){
if($loguser[redirtype]==1 && $act=="Submit"){ pageheader('Edit post',$thread[forum]); }
  pageheader('Edit post',$thread[forum]);
    print "$top - Error";
    noticemsg("Error", $err);
  }elseif(!$act){
  pageheader('Edit post',$thread[forum]);
    print "$top
".        "<br><br>
".        "$L[TBL1]>
".        " <form action=editpost.php method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Edit Post</td>
".        "    ".auth_tag();

    if($loguser[posttoolbar]!=1)
    print "  $L[TR]>
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
";
print     "  $L[TR]>
".        "    $L[TD1c] width=120>Post:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=20 cols=80>$quotetext</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPh]=pid value=$pid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        "      $L[INPl]=mid>".moodlist($post[mood], $post[user])."
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($post[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
";
    if(can_edit_forum_threads($thread[forum]) && !$thread[announce])
    print "     ".(!$thread[closed] ? "$L[INPc]=close id=close value=1 ".($_POST[close]?"checked":"")."><label for=close>Close thread</label>" : "")."
                 ".(!$thread[sticky] ? "$L[INPc]=stick id=stick value=1 ".($_POST[stick]?"checked":"")."><label for=stick>Stick thread</label>" : "")."
                 ".($thread[closed] ? "$L[INPc]=open id=open value=1 ".($_POST[open]?"checked":"")."><label for=open>Open thread</label>" : "")."
                 ".($thread[sticky] ? "$L[INPc]=unstick id=unstick value=1 ".($_POST[unstick]?"checked":"")."><label for=unstick>Unstick thread</label>" : "")."
";
    print "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Preview'){
    $_POST[message]=stripslashes($_POST[message]);
    $euser=$sql->fetchq("SELECT * FROM users WHERE id=$post[id]");
    $post[date]=ctime();
    $post[ip]=$userip;
    $post[num]=++$euser[posts];
    $post[mood]=(isset($_POST[mid]) ? (int)$_POST[mid] : -1);
    $post[nolayout]=$_POST[nolayout];
    $post[close]=$_POST[close];
    $post[stick]=$_POST[stick];
    $post[open]=$_POST[open];
    $post[unstick]=$_POST[unstick];
    $post[text]=$_POST[message];
    foreach($euser as $field => $val)
      $post[u.$field]=$val;
    $post[ulastpost]=ctime();

  pageheader('Edit post',$thread[forum]);
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
";
     if($loguser[posttoolbar]!=1)
print     "  $L[TR]>
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
";
print     "  $L[TR]>
".        "    $L[TD1c] width=120>Post:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=10 cols=80>".htmlval($_POST[message])."</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPh]=auth value=\"".htmlval($_POST['auth'])."\">
".        "      $L[INPh]=pid value=$pid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        "      $L[INPl]=mid>".moodlist($post[mood], $post[user])."
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($post[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
";
    if(can_edit_forum_threads($thread[forum]) && !$thread[announce])
    print "     ".(!$thread[closed] ? "$L[INPc]=close id=close value=1 ".($post[close]?"checked":"")."><label for=close>Close thread</label>" : "")."
                 ".(!$thread[sticky] ? "$L[INPc]=stick id=stick value=1 ".($post[stick]?"checked":"")."><label for=stick>Stick thread</label>" : "")."
                 ".($thread[closed] ? "$L[INPc]=open id=open value=1 ".($post[open]?"checked":"")."><label for=open>Open thread</label>" : "")."
                 ".($thread[sticky] ? "$L[INPc]=unstick id=unstick value=1 ".($post[unstick]?"checked":"")."><label for=unstick>Unstick thread</label>" : "")."
";
    print "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Submit'){
    $message = $_POST['message'];
    $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");

    $rev=$sql->fetchq("SELECT MAX(revision) m FROM poststext WHERE id=$pid");
    $rev=$rev[m];
    $mid=(isset($_POST[mid])?(int)$_POST[mid]:-1);
    $nolayout=$_POST['nolayout'];
    checknumeric($mid);
    checknumeric($nolayout);
    if(can_edit_forum_threads($thread['forum'])){
    	checknumeric($_POST['close']);
    	checknumeric($_POST['stick']);
        checknumeric($_POST['open']);
        checknumeric($_POST['unstick']);
   	if($_POST['close']) $modext="closed=1";
	if($_POST['stick']){ if(isset($modext)){ $modext.=",sticky=1"; } else { $modext="sticky=1"; } }
  	if($_POST['open']) $modext="closed=0";
	if($_POST['unstick']){ if(isset($modext)){ $modext.=",sticky=0"; } else { $modext="sticky=0"; } }
    }
    ++$rev;
    $sql->query("INSERT INTO poststext (id,text,revision,user,date) VALUES ($pid,'$message',$rev,$userid,".ctime().")");
    $sql->query("UPDATE posts SET mood='$mid',nolayout='$nolayout' WHERE id='$pid'");
    if(isset($modext)) $sql->query("UPDATE threads SET $modext WHERE id='$thread[id]'");
    
    if($config['log'] >= '2') $sql->query("INSERT INTO log VALUES(UNIX_TIMESTAMP(),'".$_SERVER['REMOTE_ADDR']."','$loguser[id]','ACTION: ".addslashes("post edit ".$pid." rev ".$rev)."')");

    $chan = $sql->resultp("SELECT a.chan FROM forums f LEFT JOIN announcechans a ON f.announcechan_id=a.id WHERE f.id=?",array($thread['forum']));

    if ($thread[announce]) {
      if ($thread[forum] == 0) {
    sendirc("{irccolor-base}Announcement edited by {irccolor-name}".get_irc_displayname()."{irccolor-url} ({irccolor-title}$thread[title]{irccolor-url}){irccolor-base} - {irccolor-url}{boardurl}?p=$pid{irccolor-base}",$config['pubchan']);
      }
      else {
    sendirc("{irccolor-base}Announcement edited by {irccolor-name}".get_irc_displayname()."{irccolor-url} ({irccolor-title}$thread[ftitle]{irccolor-url}: {irccolor-name}$thread[title]{irccolor-url}){irccolor-base} - {irccolor-url}{boardurl}?p=$pid{irccolor-base}",$chan);
      }
    }
    else {
    sendirc("{irccolor-base}Post edited by {irccolor-name}".get_irc_displayname()."{irccolor-url} ({irccolor-title}$thread[ftitle]{irccolor-url}: {irccolor-name}$thread[title]{irccolor-url} ({irccolor-base}\x02\x02$thread[id]{irccolor-url})){irccolor-base} - {irccolor-url}{boardurl}?p=$pid{irccolor-base}",$chan);

    }
/*if($loguser[redirtype]==0){ //Classical Redirect
  $loguser['blocksprites']=1;
  pageheader('Edit post',$thread[forum]);
    print "$top - Submit
".        "<br><br>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Post edited!<br>
".        "    ".redirect("thread.php?pid=$pid#$pid",htmlval($thread[title]))."
".        "$L[TBLend]
";
} else { //Modern redirect*/
  redirect("thread.php?pid=$pid#edit","-1");
//}
  }elseif($act=='delete' ||$act=='undelete'){
    if(!(can_delete_forum_posts($thread[forum]))) {
  pageheader('Edit post',$thread[forum]);
      print "$top - Error";
      noticemsg("Error", "You do not have the permission to do this.");
    } else {
      $sql->query("UPDATE posts SET deleted=".($act=='delete'?1:0)." WHERE id='$pid'");
  /*$loguser['blocksprites']=1;
  pageheader('Edit post',$thread[forum]);
      print "$top - ".($act=='delete'?'Delete':'Undelete')." Post
".          "<br><br>
".          "$L[TBL1]>
".          "  $L[TD1c]>
".          "    Post ".$act."d!<br>
".          "    ".redirect("thread.php?pid=$pid#$pid",htmlval($thread[title]))."
".          "$L[TBLend]
";*/
  redirect("thread.php?pid=$pid#edit",-1);
    }
  }
  //Shamelessly taken from newreply.php - SquidEmpress
  if($act!='Submit' && $act!='delete' && $act!='undelete' && !$err && !$thread[announce] && can_view_forum($thread)){
    $posts=$sql->query("SELECT ".userfields('u','u').",u.posts AS uposts, p.*, pt1.text, t.forum tforum "
                      .'FROM posts p '
					  .'LEFT JOIN threads t ON t.id=p.thread '
                      .'LEFT JOIN poststext pt1 ON p.id=pt1.id '
                      .'LEFT JOIN poststext pt2 ON pt2.id=pt1.id AND pt2.revision=(pt1.revision+1) '
                      .'LEFT JOIN users u ON p.user=u.id '
                      ."WHERE p.thread=$thread[id] "
                      ."  AND ISNULL(pt2.id) "
                      .'ORDER BY p.id DESC '
                      ."LIMIT $loguser[ppp]");
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
".        "    $L[TD1]>The full thread can be viewed <a href=thread.php?id=$thread[id]>here</a>.
".        "$L[TBLend]
";
    }
}


  pagefooter();
?>