<?php
  /* thread.php ****************************************
    Changelog
0224  Sukasa          Removed hack.
0223  Sukasa          added small threadid==4650 hack for banner (will remove)
                      it's near the end of the document, specifically just below $modlink=...
0222  blackhole89     added support for mark forum read from here
0221  blackhole89     updating the threadsread table when a logged on user
                      uses this
0220  blackhole89     readded check for forum minpower; this appears
                      to have been lost in the process of merging before
  ****************************************************/

  require 'lib/common.php';
  require 'lib/threadpost.php';

    function timelink($time){
      global $timeval;
      if ($timeval == $time) return " ".timeunits2($time)." ";
      else return " <a href=thread.php?time=$time>".timeunits2($time).'</a> ';
    }

  loadsmilies();
  
  $page = checkvar('_REQUEST','page');

  if(!$page)
    $page=1;

  $fieldlist='';
  $ufields=array('posts','regdate','lastpost','lastview','location','rankset','title','usepic','head','sign','signsep', 'minipic');
  foreach($ufields as $field)
    $fieldlist.="u.$field u$field,";

//  [DJ Bouche] What the fuck?
//  if($tid=($_POST[id]?$_POST[id]:$_GET[id])) {
  if ($ppp=checkvar('_REQUEST','ppp')) {
  checknumeric($ppp);
  }
  else $ppp = $loguser['ppp'];

  if ($tid=checkvar('_REQUEST','id')) {
    checknumeric($tid);
    $viewmode = "thread";
  }
  elseif($uid=checkvar('_GET','user')) {
    checknumeric($uid);
    $viewmode = "user";
  }
  elseif($timeval=checkvar('_GET','time')) {
    checknumeric($timeval);
    $viewmode = "time";
  }
  elseif(isset($_GET['announce'])) {
    $announcefid = $_GET['announce'];
    checknumeric($announcefid);
    $viewmode = "announce";
  }  
  // "link" support (i.e., thread.php?pid=999whatever)
  elseif($pid=$_GET['pid']){
    checknumeric($pid);
        $numpid =$sql->fetchq("SELECT t.id tid FROM posts p LEFT JOIN threads t ON p.thread=t.id WHERE p.id=$pid");
        if (!$numpid) {
      error("Error", "Thread post does not exist. <br> <a href=./>Back to main</a>");
    }
    $isannounce = $sql->resultq("SELECT announce FROM posts WHERE id=$pid");
    if ($isannounce) {
      $pinf =$sql->fetchq("SELECT t.forum fid, t.id tid FROM posts p LEFT JOIN threads t ON p.thread=t.id WHERE p.id=$pid");
      $announcefid = $pinf['fid'];
      $atid = $pinf['tid'];

      $page=floor($sql->resultq("SELECT COUNT(*) FROM threads WHERE announce=1 AND forum=$announcefid AND id>$atid")/$ppp)+1;
      $viewmode = "announce";      
    }
    else {
      $tid =$sql->resultq("SELECT thread FROM posts WHERE id=$pid");
      $page=floor($sql->resultq("SELECT COUNT(*) FROM posts WHERE thread=$tid AND id<$pid")/$ppp)+1;
      $viewmode = "thread";      
    }
  }
  else
  {
	error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
  }

  if ($viewmode == "thread") 
    $threadcreator=$sql->resultq("SELECT user FROM threads WHERE id=$tid");
  else $threadcreator=0;
  $action='';
  //Sukasa 2009-14-09: Laid some of the groundwork to allow users to rename their own threads
  if($tid && (can_edit_forum_threads(getforumbythread($tid)) ||
     ($loguser['id'] == $threadcreator && checkvar('_POST','action') == "rename" && has_perm('rename-own-thread')))) {
    $act=checkvar('_POST','action');
	if ($act) {
      check_token($_POST['auth'], "qmod");
    }
    if($act=='stick'  ) $action=',sticky=1';
    if($act=='unstick') $action=',sticky=0';
    if($act=='close'  ) $action=',closed=1';
    if($act=='open'   ) $action=',closed=0';
    if($act=='filter'  ) $action=',filter=1';
    if($act=='unfilter') $action=',filter=0';
    if($act=='trash' && $trashid>=1 )
      editthread($tid,'',$trashid,'',1);
    if($act=='showcase' && $showcaseid>=1 )
      editthread($tid,'',$showcaseid,'',1,0);
    if($act=='rename' )
      editthread($tid,$_POST[arg],0,'');
    if($act=='move'   )
      editthread($tid,'',$_POST[arg],'');
    if($act=='tag'    )
      $action=',tags=tags^'.(1<<$_POST[arg]);

    if($config['log'] >= '2') $sql->query("INSERT INTO log VALUES(UNIX_TIMESTAMP(),'".$_SERVER['REMOTE_ADDR']."','$loguser[id]','ACTION: ".addslashes($act." ".$tid." ".$_POST[arg])."')");
  }

  checknumeric($_GET['pin']);
  checknumeric($_GET['rev']);
  //determine string for revision pinning
  if($_GET['pin'] && $_GET['rev'] && can_view_forum_post_history(getforumbythread($tid))) {
    $pinstr="AND (pt2.id<>".$_GET['pin']." OR pt2.revision<>(".$_GET['rev']."+1)) ";
  } else $pinstr="";

  if($viewmode == "thread"){
    if (!$tid) $tid=0;
    $sql->query("UPDATE threads "
               ."SET views=views+1 $action "
               ."WHERE id=$tid");

    $thread=$sql->fetchq("SELECT t.*, NOT ISNULL(p.id) ispoll, p.question, p.multivote, p.changeable, f.title ftitle, t.forum fid".($log?', r.time frtime':'').' '
                        ."FROM threads t LEFT JOIN forums f ON f.id=t.forum "
                  .($log?"LEFT JOIN forumsread r ON (r.fid=f.id AND r.uid=$loguser[id]) ":'')
		  	."LEFT JOIN polls p ON p.id=t.id "
                        ."WHERE t.id=$tid AND t.forum IN ".forums_with_view_perm());

    if(!isset($thread['id']))
    {
      error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
    }
    if($config['threadprevnext'])
    {
      //AB1 style next/prev thread. Based off of AB1's code
      if($tnext=$sql->resultq("SELECT min(t.lastdate), t.forum fid FROM threads t LEFT JOIN forums f ON f.id=t.forum WHERE f.id=$thread[fid] AND t.lastdate>$thread[lastdate]"))
      {
        $tnext=$sql->resultq("SELECT id FROM threads WHERE lastdate=$tnext");
      }

      if($tprev=$sql->resultq("SELECT max(t.lastdate), t.forum fid FROM threads t LEFT JOIN forums f ON f.id=t.forum WHERE f.id=$thread[fid] AND t.lastdate<$thread[lastdate]"))
      {
        $tprev=$sql->resultq("SELECT id FROM threads WHERE lastdate=$tprev");
      }
      if($tnext) $nextnewer="<a href=thread.php?id=$tnext>Next newer thread</a>";
      if($tprev) $nextolder="<a href=thread.php?id=$tprev>Next older thread</a>";
      if($nextnewer and $nextolder) $nextnewer.=" | ";
      $nextoldnew = "$nextnewer $nextolder";
      $userbar .= "<div style='text-align: right;'>".$nextoldnew."</div>";
    }
      
    else
    {
       $nextnewer="";
       $nextolder="";
    }
      
    

	if($thread['ispoll'])
    {
      if(checkvar('_GET','act')=="vote" && $log)
      {
		$vote = (int) $_GET['vote']; //unpacksafenumeric($_GET['vote']);
		check_token($_GET['auth'], "{$tid}_{$vote}");
        if($vote > -1) 
		{
          if($thread[multivote]){
            if($thread[changeable]) {
              //changeable multivotes toggle
              $res=$sql->query("DELETE FROM pollvotes WHERE user='$loguser[id]' AND id='$vote'");
              if(!$sql->affectedrows()) $sql->query("REPLACE INTO pollvotes VALUES($vote,$loguser[id])");
            } else $sql->query("REPLACE INTO pollvotes VALUES($vote,$loguser[id])");
          } else if($thread[changeable]) {
            $sql->query("DELETE v FROM pollvotes v LEFT JOIN polloptions o ON o.id=v.id WHERE v.user=$loguser[id] AND o.poll=$tid");
            $sql->query("INSERT INTO pollvotes VALUES($vote,$loguser[id])");
          } else {
	    $res=$sql->resultq("SELECT COUNT(*) FROM pollvotes v LEFT JOIN polloptions o ON o.id=v.id WHERE v.user='$loguser[id]' AND o.poll=$tid"); 
	    if(!$res) $sql->query("INSERT INTO pollvotes VALUES($vote,$loguser[id])");
          }
		  
		  $redir = 'Location: thread.php?';
		  if ($pid) $redir .= "pid={$pid}#{$pid}";
		  else 
		  {
			$redir .= 'id='.$tid;
			if (isset($_REQUEST['page'])) $redir .= '&page='.$_REQUEST['page'];
		  }
		  die(header($redir));
        }
      }
	}

    $feedicons.=feedicon("img/rss3.png","rss.php?thread=$thread[id]","RSS feed for this thread");
    $feedicons.=feedicon("img/rss2.png","rss.php?forum=$thread[forum]","RSS feed for this section");
    
    if(!isset($pid)) $meta=threadformeta($tid); else $meta=postformeta($pid);
    //append thread's title to page title
    pageheader($thread['title'],$thread['fid'],$meta);
	
	// Print thread creation / edit messages (they lack a 'pid')
	if (!isset($_REQUEST['pid'])) print $cookiemsg;

    //mark thread as read // 2007-02-21 blackhole89
    if($log && $thread['lastdate']>$thread['frtime'])
      $sql->query("REPLACE INTO threadsread VALUES ($loguser[id],$thread[id],".ctime().")");

    //check for having to mark the forum as read too
    if($log) {
      $readstate=$sql->fetchq("SELECT ((NOT ISNULL(r.time)) OR t.lastdate<'$thread[frtime]') n "
                             ."FROM threads t "
			     ."LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id]) "
			     ."WHERE t.forum=$thread[fid] "
			     ."GROUP BY ((NOT ISNULL(r.time)) OR t.lastdate<'$thread[frtime]') ORDER BY n ASC");
      //if $readstate[n] is 1, MySQL did not create a group for threads where ((NOT ISNULL(r.time)) OR t.lastdate<'$thread[frtime]') is 0;
      //thus, all threads in the forum are read. Mark it as such.
      if($readstate['n'] == 1) $sql->query("REPLACE INTO forumsread VALUES (".$loguser['id'].",".$thread['fid'].",".ctime().')');
    }

    //select top revision // 2007-03-08 blackhole89
    $posts=$sql->query("SELECT ".userfields('u','u').", ".$fieldlist." p.*, pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.forum tforum "
                      ."FROM posts p "
					  ."LEFT JOIN threads t ON t.id=p.thread "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
//		      ."JOIN ("
//		        ."SELECT a.id,MAX(a.revision) toprev FROM poststext a GROUP BY a.id"
//		      .") as pt2 ON pt2.id=pt.id AND pt2.toprev=pt.revision "
                      ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr " //SQL barrel roll
                      ."LEFT JOIN users u ON p.user=u.id "
                      ."WHERE p.thread=$tid AND ISNULL(pt2.id) "
		      ."GROUP BY p.id "
                      ."ORDER BY p.id "
                      ."LIMIT ".(($page-1)*$ppp).",".$ppp);

    //load tags
    $tags=array();
    $t=$sql->query("SELECT * FROM tags WHERE fid=$thread[fid]");
    while($tt=$sql->fetch($t)) $tags[]=$tt;

  }elseif($viewmode == "user"){
    $user=$sql->fetchq("SELECT * "
                      ."FROM users "
                      ."WHERE id=$uid ");
    //title
    pageheader("Posts by ".($user[displayname] ? $user[displayname] : $user[name]),0,"Posts by ".($user[displayname] ? $user[displayname] : $user[name]));
    $posts=$sql->query("SELECT ".userfields('u','u').",$fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, f.id fid, f.private fprivate, t.title ttitle, t.forum tforum "
                      ."FROM posts p "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
//		      ."JOIN ("
//                        ."SELECT id,MAX(revision) toprev FROM poststext GROUP BY id"
//                      .") as pt2 ON pt2.id=pt.id AND pt2.toprev=pt.revision "
		      ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr "
                      ."LEFT JOIN users u ON p.user=u.id "
                      ."LEFT JOIN threads t ON p.thread=t.id "
                      ."LEFT JOIN forums f ON f.id=t.forum "
                      ."LEFT JOIN categories c ON c.id=f.cat "
                      ."WHERE p.user=$uid AND ISNULL(pt2.id) "
                      ."ORDER BY p.id "
                      ."LIMIT ".(($page-1)*$ppp).",".$ppp);

    $thread[replies]=$sql->resultq("SELECT count(*) "
                                  ."FROM posts p "
                                  ."LEFT JOIN threads t ON p.thread=t.id "
                                  ."LEFT JOIN forums f ON f.id=t.forum "
                                  ."LEFT JOIN categories c ON c.id=f.cat "
                                  ."WHERE p.user=$uid ");
  }
  elseif($viewmode == "announce") {
	if ($announcefid) { // Forum announcement
		$announceftitle = $sql->resultp("SELECT title FROM forums WHERE id = ? AND id IN ".forums_with_view_perm(), array($announcefid));
		if (!$announceftitle) {
			error("Error", "Forum does not exist.<br> <a href=./>Back to main</a>");     
		}
		pageheader('Announcements', $announcefid);
	}
    else {
      $showonusers = 1;
      pageheader('Announcements');
    }

    $posts=$sql->query("SELECT ".userfields('u','u').",$fieldlist p.*, pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, f.id fid, t.title ttitle, t.forum tforum, p.announce isannounce, f.cat cat, c.private cprivate "
                      ."FROM posts p "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
                      ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr " //SQL barrel roll
                      ."LEFT JOIN users u ON p.user=u.id "
                      ."LEFT JOIN threads t ON p.thread=t.id "
                      ."LEFT JOIN forums f ON f.id=t.forum "
                      ."LEFT JOIN categories c ON c.id=f.cat "
                      ."WHERE t.forum=$announcefid AND p.announce=1 AND t.announce=1 AND ISNULL(pt2.id) GROUP BY pt.id "
                      ."ORDER BY p.id DESC "
                      ."LIMIT ".(($page-1)*$ppp).",".$ppp);



    $thread['replies']=$sql->resultq("SELECT count(*) "
                                  ."FROM posts p "
                                  ."LEFT JOIN threads t ON p.thread=t.id "
                                  ."LEFT JOIN forums f ON f.id=t.forum "
                                  ."LEFT JOIN categories c ON c.id=f.cat "
                                  ."WHERE  f.id=$announcefid AND p.announce=1 AND t.announce=1  "
                      ) -1;
  }
  elseif($viewmode == "time") {
    checknumeric($timeval);
    $mintime=ctime()-$timeval;

    pageheader('Latest posts');


    $posts=$sql->query("SELECT ".userfields('u','u').",$fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, f.id fid, f.private fprivate, t.title ttitle, t.forum tforum, f.cat cat, c.private cprivate, f.private fprivate "
                      ."FROM posts p "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
          ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr "
                      ."LEFT JOIN users u ON p.user=u.id "
                      ."LEFT JOIN threads t ON p.thread=t.id "
                      ."LEFT JOIN forums f ON f.id=t.forum "
                      ."LEFT JOIN categories c ON c.id=f.cat "
                      ."WHERE p.date>$mintime AND ISNULL(pt2.id) AND t.filter=0 "
                      ."ORDER BY p.date DESC "
                      ."LIMIT ".(($page-1)*$ppp).",".$ppp);

    $thread['replies']=$sql->resultq("SELECT count(*) "
                                  ."FROM posts p "
                                  ."LEFT JOIN threads t ON p.thread=t.id "
                                  ."LEFT JOIN forums f ON f.id=t.forum "
                                  ."LEFT JOIN categories c ON c.id=f.cat "
                                  ."WHERE p.date>$mintime "
                      );
  }
  else {
    pageheader();
  }
  if($thread['replies']<$ppp){
    $pagelist=''; $pagebr='';
  }else{
    $pagelist='<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">Pages:';
    for($p=1;$p<=1+floor($thread[replies]/$ppp);$p++)
      if($p==$page)
        $pagelist.=" $p";
      elseif($viewmode == "thread")
        $pagelist.=" <a href=thread.php?id=$tid&page=$p>$p</a>";
      elseif($viewmode == "user")
        $pagelist.=" <a href=thread.php?user=$uid&page=$p>$p</a>";
      elseif($viewmode == "time")
        $pagelist.=" <a href=thread.php?time=$timeval&page=$p>$p</a>";
      elseif($viewmode == "announce")
        $pagelist.=" <a href=thread.php?announce=$announcefid&page=$p>$p</a>";
    $pagebr='<br>';
    $pagelist.='</div>';
  }

  if($viewmode=="thread"){

	$faccess = $sql->fetch($sql->query("SELECT id,private,readonly FROM forums WHERE id=".(int)$thread['forum']));
	$newreply = "";
	if (can_create_forum_post($faccess)) {
		if (has_perm('track-deleted-posts') && can_edit_forum_posts($thread['forum']))
			$newreply .= "<a href='deletedposts.php?t={$tid}'>Deleted posts</a> | ";
		
		if (can_create_locked_posts($thread['forum'], $thread['id']) && $thread['closed'])
			$newreply.="<b><i>Thread closed</i></b> | <a href=\"newreply.php?id=$tid\" class=\"newreply\">New reply</a>"; //needs function to test for perm based on $faccess
		else if ($thread['closed'])
			$newreply.="Thread closed";
		else
			$newreply.="<a href=\"newreply.php?id=$tid\" class=\"newreply\">New reply</a>";
	}
    if($thread['ispoll'])
    {
      $poll=
          "<br>$L[TBL1]>
".        "  $L[TR1]>
".        "    $L[TD1] colspan=2>".htmlval($thread['question'])."
";
      $opts=$sql->query("SELECT o.*,(COUNT(*) & (NOT ISNULL(v.user))*1023) c,((NOT ISNULL(w.user))*1) s FROM polloptions o LEFT JOIN pollvotes v ON v.id=o.id LEFT JOIN pollvotes w ON w.user='$loguser[id]' AND w.id=o.id WHERE poll=$tid GROUP BY o.id");
      $total=$sql->resultq("SELECT COUNT(DISTINCT v.user) FROM polloptions o, pollvotes v WHERE o.poll=$tid AND v.id=o.id");
      $mytotal=$log?$sql->resultq("SELECT COUNT(*) FROM polloptions o, pollvotes v WHERE o.poll=$tid AND v.id=o.id AND v.user='$loguser[id]'"):0;
      while($opt=$sql->fetch($opts))
      {
        $h=$opt['s']?"*":"";
	$cond=$log&&(($thread['multivote']&&!$opt['s'])||$thread['changeable']||!$mytotal);
        $poll.="$L[TR2]>$L[TD2]>".($cond?("<a href=thread.php?id=$tid&act=vote&vote={$opt['id']}".auth_url("{$tid}_{$opt['id']}").">"):"").htmlval($opt['option']).($cond?"</a>":"")." $h$L[TD3]><img src=\"gfx/bargraph.php?z=$opt[c]&n=$total&r=$opt[r]&g=$opt[g]&b=$opt[b]\">";
      }
      $poll.=
          "  $L[TR2]>$L[TDs] colspan=2>Multiple voting is ".($thread['multivote']?"":"not")." allowed. Changing your vote is ".($thread['changeable']?"":"not")." allowed. $total ".($total==1?"user has":"users have")." voted so far.
".        "$L[TBLend]
";
    }

//[KAWA] Thread +1
if(isset($_GET['thumbsup']))
{
  check_token($_GET['auth'], "xvt");
  if (!has_perm('rate-thread')) { noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>"); pagefooter(); die(); }
	$sql->query("INSERT IGNORE INTO threadthumbs VALUES (".$loguser['id'].", ".$tid.")");
	$isThumbed = true;
}
else if(isset($_GET['thumbsdown']))
{
  check_token($_GET['auth'], "xvt");
  if (!has_perm('rate-thread')) { noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>"); pagefooter(); die(); }
	$sql->query("DELETE FROM threadthumbs WHERE uid = ".$loguser['id']." AND tid = ".$tid);
	$isThumbed = false;
}
else
{
	$isThumbed = $sql->resultq("SELECT COUNT(*) FROM threadthumbs WHERE uid=".$loguser['id']." AND tid=".$tid) == 1;
}

$thumbsUp = "";
if (has_perm('rate-thread') && $thread['user'] != $loguser['id']) {
  $authval = auth_url("xvt");
  if(!$isThumbed)
  	$thumbsUp = "<a href=\"thread.php?id=$tid&amp;thumbsup$authval\" class=\"threadthumbsup\">+1</a>";
  else
  	$thumbsUp = "<a href=\"thread.php?id=$tid&amp;thumbsdown$authval\" class=\"threadthumbsdown\">-1</a>";
}

$thumbCount = $sql->resultq("SELECT COUNT(*) FROM threadthumbs WHERE tid=".$tid);
if ($thumbCount) $thumbsUp .= " (".$thumbCount.")";

    $topbot=
          "$L[TBL] width=100%>$L[TR]>
".        "  $L[TDn]><a href=./>Main</a> - <a href=forum.php?id=$thread[forum]>$thread[ftitle]</a> - ".htmlval($thread['title'])." $thumbsUp</td>
".        "  $L[TDnr]>
".        "  $newreply
".        "  </td>
".        "$L[TBLend]
";

  }elseif($viewmode=="user"){
    $topbot=
          "$L[TBL] width=100%>
".        "  $L[TDn]><a href=./>Main</a> - Posts by ".userlink($user,"",$config[showminipic])."</td>
".        "$L[TBLend]
";
  }
  elseif($viewmode=="announce") {
    if (can_create_forum_announcements($announcefid)) {
      $newreply="<a href=newthread.php?id=$announcefid&announce=1>New announcement</a>";
    }
    else {
      $newreply = "";
    }

    $topbot=
          "$L[TBL] width=100%>$L[TR]>
".        "  $L[TDn]><a href=./>Main</a> ".($announcefid? "- <a href=forum.php?id=$announcefid>$announceftitle</a> ":"")."- Announcements</td>
".        "  $L[TDnr]>
".        "    $newreply
".        "  </td>
".        "$L[TBLend]
";
  }
elseif($viewmode=="time"){
    $topbot=
          "$L[TBL] width=100%>
".        "  $L[TDn]><a href=./>Main</a> - Latest posts</td>
".        "$L[TBLend]
";
  }
  else
  {
	noticemsg("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
        pagefooter();
        die();
  }
  
  
  $modlinks='<br>';
  if($tid && 
    (can_edit_forum_threads($thread['forum']) || 
      ($loguser['id'] == $thread['user'] && !$thread['closed'] && (has_perm('edit-thread') || has_perm('rename-own-thread'))))) {
    $link="<a href=javascript:submitmod";
    if (can_edit_forum_threads($thread['forum'])) {
      if($thread['sticky'])
        $stick="$link('unstick')>Unstick</a>";
      else
        $stick="$link('stick')>Stick</a>";

      if($thread['closed'])
        $close="| $link('open')>Open</a>";
      else
        $close="| $link('close')>Close</a>";

      if($thread['filter'])
        $filtr="| $link('unfilter')>Remove Filter</a>";
      else
        $filtr="| $link('filter')>Add Filter</a>";
if($trashid>=1){ //Now only enabled if ID is a valid forum ID.
      if($thread['forum']!=$trashid)
        $trash="| $link('trash')>Trash</a> ";
}
if($showcaseid>=1){ //Only enabled if ID is a valid forum ID.
      if($thread['forum']!=$showcaseid)
        $showcase="| $link('showcase')>Showcase</a> |";
      else
        $showcase='| ';
}
      $edthr="| <a href=editthread.php?id=$tid>Edit Thread</a> ";

      $retag=sizeof($tags)?"<a href=javascript:showtbox()>Tag</a> | ":"";
      $edit="<a href=javascript:showrbox()>Rename</a> | $retag <a href=javascript:showmove()>Move</a>";
    
		//KAWA: Made it a dropdown list. The change isn't alone in this file, but it's clear where it starts and ends if you want to put this on 2.1+delta.
		$r=$sql->query("SELECT c.title ctitle,c.private cprivate,f.id,f.title,f.cat,f.private FROM forums f LEFT JOIN categories c ON c.id=f.cat ORDER BY c.ord,c.id,f.ord,f.id");
		$fmovelinks="<select onchange=\"submitmove(this.options[this.selectedIndex].value);\">";
		$c = -1;
		while($d=$sql->fetch($r))
		{
			if (!can_view_forum($d)) continue;
			
			if ($d['cat'] != $c)
			{
				if ($c != -1) $fmovelinks .= '</optgroup>';
				$c = $d['cat'];
				$fmovelinks .= '<optgroup label="'.$d['ctitle'].'">';
			}
			$fmovelinks.="<option value=\"".$d['id']."\"".($d['id']==$thread['forum']?" selected=\"selected\"":"").">".$d['title']."</option>";
		}
		$fmovelinks.="</optgroup></select>";
		$fmovelinks=addslashes($fmovelinks);

      $opt="Moderating";
    } else {
      $fmovelinks="";
      $close = $stick = $filtr = $edthr = $trash = $showcase = "";
      $retag = sizeof($tags) ? "<a href=javascript:showtbox()>Tag</a> | " : "";
      if($loguser['id'] == $thread['user'] && !$thread['closed'] && has_perm('rename-own-thread')){
        $canrnbar="| ";
        $edit = "<a href=javascript:showrbox()>Rename</a>";
      }
      if($loguser['id'] == $thread['user'] && !$thread['closed'] && has_perm('edit-thread')) $edthr="$canrnbar <a href=editthread.php?id=$tid>Edit ".(($thread['ispoll']==1)? 'Poll' : 'Icon')."</a> ";
      $opt = "Thread";
    }
    $taglinks="";
    for($i=0;$i<sizeof($tags);++$i) {
      $t=$tags[$i];
      if(!($thread['tags'] & (1<<$t['bit']))) $taglinks.="<a href=javascript:submittag('".$t['bit']."')>".$t['tag']."</a> ";
    }
    $taglinks.="| Remove: ";
    for($i=0;$i<sizeof($tags);++$i) {
      $t=$tags[$i];
      if(isset($thread['tag']) && (1<<$t['bit'])) $taglinks.="<a href=javascript:submittag('".$t['bit']."')>".$t['tag']."</a> ";
    }
    $taglinks=addslashes($taglinks);

    $modlinks=
          "<form action=thread.php method=post name=mod>
".        "$L[TBL2]>$L[TR2]>
".        "  $L[TD3]>
".        "    <span id=moptions>
".        "    $opt options:
".        "    $stick
".        "    $close
".        "    $trash
".        "    $showcase
".        "    $edit
".        "    $filtr
".        "    $edthr
".        "    </span>
".        "    <span id=mappend>
".        "    <input type=hidden name=tmp style='width:80%!important;border-width:0px!important;padding:0px!important' onkeypress=\"submit_on_return(event,'rename')\" value=\"".htmlentities($thread['title'],ENT_COMPAT | ENT_HTML401,'UTF-8')."\" maxlength=100>
".        "    </span>
".        "    <script type=text/javascript>
".        "      function submitmod(act){
".        "        document.mod.action.value=act;
".        "        document.mod.submit();
".        "      }
".        "      function submitmove(fid){
".        "        document.mod.arg.value=fid;
".        "        submitmod('move')
".        "      }
".        "      function submittag(bit){
".        "        document.mod.arg.value=bit;
".        "        submitmod('tag')
".        "      }
".        "      function showrbox(){
".        "        document.getElementById('moptions').innerHTML='Rename thread:';
".(strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")?
/* IE: scripts can add/remove form elements, type of existing element can't be changed
 * Firefox, Opera: addition/removal of form elements = they vanish, type can be changed
 * Konqueror: both work */
          "        document.getElementById('mappend').innerHTML='<input type=text name=tmp style=\'width:80%!important;border-width:0px!important;padding:0px!important\' onkeypress=\"submit_on_return(event,\'rename\')\" value=\"".addslashes(htmlentities($thread[title]))."\">';":
          "        document.mod.tmp.type='text';")."        
".        "      }
".        "      function showtbox(){
".        "        document.getElementById('moptions').innerHTML='Add:';
".        "        document.getElementById('mappend').innerHTML='$taglinks';
".        "      }
".        "      function showmove(){
".        "        document.getElementById('moptions').innerHTML='Move to: ';
".        "        document.getElementById('mappend').innerHTML='$fmovelinks';
".        "      }
".        "      function submit_on_return(event,act){
".        "        a=event.keyCode?event.keyCode:event.which?event.which:event.charCode;
".        "        document.mod.action.value=act;
".        "        document.mod.arg.value=document.mod.tmp.value;
".        "        if(a==13) document.mod.submit();
".        "      }
".        "    </script>
".        "    <input type=hidden name=arg value=''>
".        "    <input type=hidden name=id value=$tid>
".        "    <input type=hidden name=action value=''>
".        "    ".auth_tag("qmod")."
".        "  </td>
".        "$L[TBLend]
".        "  </form>
";
  }
  if(!isset($userbar)) $userbar="";
  print   "$topbot$userbar";

  if(checkvar('timeval')) {
    print "<div style=\"margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block\">
          <a href=forum.php?time=$timeval>By Threads</a> | By Posts</a></div><br>"; 
    print '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">'.
         timelink(900).'|'.timelink(3600).'|'.timelink(86400).'|'.timelink(604800)
   ."</div>";


 }


print "$modlinks
".        "$pagelist
".        checkvar('poll')."
";
  while($post=$sql->fetch($posts)){
    if (isset($post['fid'])) {
      if (!can_view_forum(array('id'=>$post['fid'], 'private'=>$post['fprivate'], 'cprivate'=>$post['cprivate'], 'cat'=>$post['cat']))) continue;
    }
    if(isset($uid) || isset($timeval)){
      $pthread['id']=checkvar('post','tid');
      $pthread['title']=checkvar('post','ttitle');
    }
    if($post['id']!=$_GET['pin']){
      $post['maxrevision']=$post['revision']; // not pinned, hence the max. revision equals the revision we selected
    } else {
      $post['maxrevision']=$sql->resultq("SELECT MAX(revision) FROM poststext WHERE id=".$_GET['pin']);
    }
    if(can_edit_forum_posts(checkvar('post','fid')) && $post['id']==$_GET['pin']) $post['deleted']=false;
	// After a creating or editing a post
	if ($post['id'] == checkvar('_REQUEST','pid')) print $cookiemsg;
   if(!isset($pthread)) $pthread='';
    print "<br>
".         threadpost($post,0,$pthread);
  }


  print   "$pagelist$pagebr
".        "<br>";

  if(isset($thread['id']) && can_create_forum_post($faccess) && !$thread['closed']) {
  echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
  $toolbar= posttoolbar($loguser['posttoolbar']);

      //lol so hacky please organise this into the right place soon.

    print "<script language=javascript>
        function updatequickreplystatus(ishidden)
        {
          x = new XMLHttpRequest();
          x.open('GET', 'userpref.php?field=hidequickreply&value='+ishidden+'&x_ajax=1');
          x.send(null);
        }
        function togglequickreply()
        {
          var table = document.getElementById('quickreply');
          var rows = table.getElementsByTagName('tr');
          var ishidden = 0;
          for(var i = 1; i < rows.length; i++)
          {
            if(rows[i].className == 'toolbar') continue;
            if(rows[i].style['display'] == 'none') {
              rows[i].style['display'] = '';
              ishidden = 0;
            }
            else {
              rows[i].style['display'] = 'none';
              ishidden = 1;
            }
          }
          updatequickreplystatus(ishidden);
        }
    </script>
    ";


    if ($loguser['hidequickreply']) {
      $quickreplydisplay = " style='display: none' ";
    }
    else {
      $quickreplydisplay = "";
    }

    print "
".        "
".        " <form action=newreply.php method=post>
".        "$L[TBL1] name=quickreply id=quickreply>
".        "  $L[TRh] onclick='togglequickreply();' style='cursor: pointer'>
".        "    $L[TDh] colspan=2>".$config['quickreply']."</a></td>
";

     if($loguser['posttoolbar']!=1)
    print "  $L[TR] $quickreplydisplay >
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR] class='toolbar'>$toolbar$L[TBLend]";
    print "  $L[TR] $quickreplydisplay >
".        "    $L[TD1c] width=120>Reply:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=8 cols=80>".checkvar('quotetext')."</textarea></td>
".        "  $L[TR1] $quickreplydisplay >
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      ".auth_tag()."
".        "      $L[INPh]=tid value=$tid>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        // 2009-07 Sukasa: Newreply mood selector, just in the place I put it in mine
          "      $L[INPl]=mid>".moodlist()." 
".        "      $L[INPc]=nolayout id=nolayout value=1 ><label for=nolayout>Disable post layout</label>
".        "      $L[INPc]=nosmile id=nosmile value=1 ><label for=nosmile>Disable smilies</label>
";
    if(can_edit_forum_threads($thread['forum']))
    print "     $L[INPc]=close id=close value=1 ><label for=close>Close thread</label>
                ".(!$thread['sticky'] ? "$L[INPc]=stick id=stick value=1><label for=stick>Stick thread</label>" : "")."
                ".($thread['sticky'] ? "$L[INPc]=unstick id=unstick value=1><label for=unstick>Unstick thread</label>" : "")."
";
    print "    </td>
".        "$L[TBLend]<br>
".        " </form>
";
  }

print        "$userbar$topbot";

  pagefooter();
?>
                                                                                                   
