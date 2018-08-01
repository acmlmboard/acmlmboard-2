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

  	//--
	if ($act = $_POST['action']) {
		check_token($_POST['auth']);
		$tid = (int) $_POST['tid'];
	} else {
		$tid = (int) $_GET['id'];
	}
	$user   = $loguser;
	$userid = $loguser['id'];
	//--
	
  checknumeric($tid);

  needs_login(1);


  if($act!='Submit'){
    $posts=$sql->query("SELECT ".userfields('u','u').",u.posts AS uposts, p.*, pt1.text, t.forum tforum "
                      .'FROM posts p '
					  .'LEFT JOIN threads t ON t.id=p.thread '
                      .'LEFT JOIN poststext pt1 ON p.id=pt1.id '
                      .'LEFT JOIN poststext pt2 ON pt2.id=pt1.id AND pt2.revision=(pt1.revision+1) '
                      .'LEFT JOIN users u ON p.user=u.id '
                      ."WHERE p.thread=$tid "
                      ."  AND ISNULL(pt2.id) "
                      .'ORDER BY p.id DESC '
                      ."LIMIT $loguser[ppp]");
  }

  $thread=$sql->fetchq('SELECT t.*, f.title ftitle, f.private fprivate, f.readonly freadonly '
                      .'FROM threads t '
                      .'LEFT JOIN forums f ON f.id=t.forum '
                      ."WHERE t.id=$tid AND t.forum IN ".forums_with_view_perm());

  if($act!="Submit"){
    $extjs="<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
}
  $toolbar= posttoolbar();

  $threadlink="<a href=thread.php?id=$tid>Back to thread</a>";

  if(!$thread) {
	error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
    }


     else if (!can_create_forum_post(array('id'=>$thread['forum'], 'private'=>$thread['fprivate'], 'readonly'=>$thread['readonly']))){

       $err="    You have no permissions to create posts in this forum!<br>$forumlink";
    }
  elseif($thread[closed] && !can_create_locked_posts($thread['forum'], $thread['id'])){
      $err="    You can't post in closed threads!<br>
".         "    $threadlink";
  }//needs function to test for perm based on $faccess /*!has_perm('create-closed-forum-post')*/

  if($act=='Submit'){
    $message = $_POST[message];
    if(strlen($message)>60000)  // Protection against huge posts getting cut off
      $err="    This post is too long. Maximum length: 60000 characters. <br>
".         "    $threadlink";
    if($thread[lastuser]==$userid && $thread[lastdate]>=(ctime()-86400) && !can_post_consecutively($thread['forum']))  // admins can double post all they want
      $err="    You can't double post until it's been at least one day!<br>
".         "    $threadlink";
    if($thread[lastuser]==$userid && $thread[lastdate]>=(ctime()-$config[secafterpost]) && can_post_consecutively($thread['forum']))  // Protection against double-submit
      $err="    You must wait $config[secafterpost] seconds before posting consecutively.<br>
".         "    $threadlink";
    //2007-02-19 //blackhole89 - table breakdown protection
    if(($tdepth=tvalidate($message))!=0)
      $err="    This post would disrupt the board's table layout! The calculated table depth is $tdepth.<br>
".         "    $threadlink";
    if(strlen(trim($message))==0)
      $err="    Your post is empty! Enter a message and try again.<br>
".         "    $threadlink";
    if($user[regdate]>(ctime()-$config[secafterpost]))
      $err="    You must wait $config[secafterpost] seconds before posting on a freshly registered account.<br>
".         "    $threadlink";
  }

  $top='<a href=./>Main</a> '
    ."- <a href=forum.php?id=$thread[forum]>$thread[ftitle]</a> "
    ."- <a href=thread.php?id=$thread[id]>".htmlval($thread[title]).'</a> '
    .'- New reply';


  if($pid=$_GET[pid]){
    checknumeric($pid);  //nice way of adding security, really. int_val doesn't really do it (floats and whatnot), so heh
    $post=$sql->fetchq("SELECT IF(u.displayname='',u.name,u.displayname) name, p.user, pt.text, f.id fid, f.private fprivate, p.thread "
                      ."FROM posts p "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
          ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) "
                      ."LEFT JOIN users u ON p.user=u.id "
          ."LEFT JOIN threads t ON t.id=p.thread "
          ."LEFT JOIN forums f ON f.id=t.forum "
                      ."WHERE p.id=$pid AND ISNULL(pt2.id)");
  
  //does the user have reading access to the quoted post?
  if(!can_view_forum(array('id'=>$post['fid'], 'private'=>$post['fprivate']))) { $post['name'] = 'your overlord'; $post[text]=""; }

  $quotetext="[quote=\"$post[name]\" id=\"$pid\"]".$post[text]."[/quote]";
  }

  if($err){
    pageheader('New reply',$thread[forum]);
    print "$extjs $top - Error";
    noticemsg("Error", $err);
  }elseif($act=='Preview' || !$act){
    if($act=='Preview'){
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
      }


    $post[date]=ctime();
    $post[ip]=$userip;
    $post[num]=++$user[posts];
    if($act=='Preview') $post[text]=$prefix.$_POST[message].$postfix;
    else $post[text]=$quotetext;
    $post[mood] = (isset($_POST[mid]) ? (int)$_POST[mid] : -1); // 2009-07 Sukasa: Newthread preview
    if($act=='Preview') $post[moodlist]=moodlist($_POST[mid]);
    else $post[moodlist]=moodlist();
    $post[nolayout]=$_POST[nolayout];
    $post[close]=$_POST[close];
    $post[stick]=$_POST[stick];
    $post[open]=$_POST[open];
    $post[unstick]=$_POST[unstick];
    foreach($user as $field => $val)
      $post[u.$field]=$val;
    $post[ulastpost]=ctime();

 if($act=='Preview') {
    pageheader('New reply',$thread[forum]);
    print "$extjs $top - Preview
".        "<br>
".        "$L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Post preview
".        "$L[TBLend]
".         threadpost($post,0)."
".        "<br>
"; 
} else {
    pageheader('New reply',$thread[forum]);
    print "$extjs $top 
".        "<br><br> 
"; }
print 
        "$L[TBL1]> 
".        " <form action=newreply.php method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Reply</td>
".           $valid."
";
     if($loguser[posttoolbar]!=1)
print     "  $L[TR]>
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
";
print     "  $L[TR]>
".        "    $L[TD1c] width=120>Reply:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=10 cols=80>".htmlval($post[text])."</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      ".auth_tag()."
".        "      $L[INPh]=tid value=$tid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        // 2009-07 Sukasa: Newreply mood selector, just in the place I put it in mine
          "      $L[INPl]=mid>".$post[moodlist]." 
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($post[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
";
    if(can_edit_forum_threads($thread[forum]))
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
    checknumeric($_POST[nolayout]);
//Make sure these controls are only usable by those with moderation rights!
    if(can_edit_forum_threads($thread['forum'])){
    	checknumeric($_POST['close']);
	checknumeric($_POST['stick']);
     	checknumeric($_POST['open']);
     	checknumeric($_POST['unstick']);
     	if($_POST['close']) $modext=",closed=1";
 	if($_POST['stick']) $modext.=",sticky=1";
     	if($_POST['open']) $modext=",closed=0";
 	if($_POST['unstick']) $modext.=",sticky=0";
    }
    $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");
    $user[posts]++;
    $mid=(isset($_POST[mid]) ? (int)$_POST[mid] : -1);

    $sql->query("UPDATE users SET posts=posts+1,lastpost=".ctime()." WHERE id=$userid");
    $sql->query("INSERT INTO posts (user,thread,date,ip,num,mood,nolayout) "
               ."VALUES ($userid,$tid,".ctime().",'$userip',$user[posts],$mid,$_POST[nolayout])");
    $pid=$sql->insertid();
    $sql->query("INSERT INTO poststext (id,text) VALUES ($pid,'$message')");
    $sql->query("UPDATE threads SET replies=replies+1,lastdate=".ctime().",lastuser=$userid,lastid=$pid$modext WHERE id=$tid");
    $sql->query("UPDATE forums SET posts=posts+1,lastdate=".ctime().",lastuser=$userid,lastid=$pid WHERE id=$thread[forum]");

    //2007-02-21 //blackhole89 - nuke entries of this thread in the "threadsread" table
    $sql->query("DELETE FROM threadsread WHERE tid='$thread[id]' AND NOT (uid='$userid')");

  // bonus shit
    $c = rand(100, 500);
    $sql->query("UPDATE `usersrpg` SET `spent` = `spent` - '$c' WHERE `id` = '$userid'");

   $chan = $sql->resultp("SELECT a.chan FROM forums f LEFT JOIN announcechans a ON f.announcechan_id=a.id WHERE f.id=?",array($thread['forum']));


sendirc("{irccolor-base}New reply by {irccolor-name}".get_irc_displayname()."{irccolor-url} ({irccolor-title}$thread[ftitle]{irccolor-url}: {irccolor-name}$thread[title]{irccolor-url} ({irccolor-base}\x02\x02$tid{irccolor-url}) ({irccolor-base}+$c{irccolor-url})){irccolor-base} - {irccolor-url}{boardurl}?p=$pid{irccolor-base}",$chan);

/*if($loguser[redirtype]==0){ //Classical Redirect
    $loguser['blocksprites']=1;
    pageheader('New reply',$thread[forum]);
    print "$top - Submit
".        "<br><br>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "    Posted! (Gained $c bonus coins)<br>
".        "    ".redirect("thread.php?pid=$pid#$pid",htmlval($thread[title]))."
".        "$L[TBLend]
";
} else { //Modern redirect*/
  redirect("thread.php?pid=$pid#$pid",$c);
//}

  }

  if($act!='Submit' && !$err && can_view_forum($thread)){
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
?>