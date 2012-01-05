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

  loadsmilies();

  if(!$page)
    $page=1;

  $fieldlist='';
  $ufields=array('id','name','posts','regdate','lastpost','lastview','location','sex','power','rankset','title','usepic','head','sign','signsep');
  foreach($ufields as $field)
    $fieldlist.="u.$field u$field,";

  if($tid=($_POST[id]?$_POST[id]:$_GET[id]))
    checknumeric($tid);
  elseif($uid=$_GET[user])
    checknumeric($uid);
  // "link" support (i.e., thread.php?pid=999whatever)
  elseif($pid=$_GET[pid]){
    checknumeric($pid);
    $tid =$sql->resultq("SELECT thread FROM posts WHERE id=$pid");
    $page=floor($sql->resultq("SELECT COUNT(*) FROM posts WHERE thread=$tid AND id<$pid")/$loguser[ppp])+1;
  }
  if ($tid) 
    $threadcreator=$sql->resultq("SELECT user FROM threads WHERE id=$tid");
  else $threadcreator=0;
  $action='';
  //Sukasa 2009-14-09: Laid some of the groundwork to allow users to rename their own threads
  if($tid && $_POST[c]==md5($loguser[pass].$pwdsalt) && (can_edit_forum_threads(getforumbythread($tid)) ||
     ($loguser[id] == $threadcreator && $_POST[action] == "rename" && has_perm('rename-own-thread')))) {
    $act=$_POST[action];
    if($act=='stick'  ) $action=',sticky=1';
    if($act=='unstick') $action=',sticky=0';
    if($act=='close'  ) $action=',closed=1';
    if($act=='open'   ) $action=',closed=0';
    if($act=='trash'  )
      editthread($tid,'',$trashid,'',1);
    if($act=='rename' )
      editthread($tid,$_POST[arg],0,'');
    if($act=='move'   )
      editthread($tid,'',$_POST[arg],'');
    if($act=='tag'    )
      $action=',tags=tags^'.(1<<$_POST[arg]);

    $sql->query("INSERT INTO log VALUES(UNIX_TIMESTAMP(),'$REMOTE_ADDR','$loguser[id]','ACTION: ".addslashes($act." ".$tid." ".$_POST[arg])."')");
  }

  checknumeric($_GET[pin]);
  checknumeric($_GET[rev]);
  //determine string for revision pinning
  if($_GET[pin] && $_GET[rev] && can_view_forum_post_history(getforumbythread($tid))) {
    $pinstr="AND (pt2.id<>$_GET[pin] OR pt2.revision<>($_GET[rev]+1)) ";
  } else $pinstr="";

  if(!$uid){
    if (!$tid) $tid=0;
    $sql->query("UPDATE threads "
               ."SET views=views+1 $action "
               ."WHERE id=$tid");

    $thread=$sql->fetchq("SELECT t.*, NOT ISNULL(p.id) ispoll, p.question, p.multivote, p.changeable, f.title ftitle, f.minpower minpower, t.forum fid".($log?', r.time frtime':'').' '
                        ."FROM threads t LEFT JOIN forums f ON f.id=t.forum "
                  .($log?"LEFT JOIN forumsread r ON (r.fid=f.id AND r.uid=$loguser[id]) ":'')
		  	."LEFT JOIN polls p ON p.id=t.id "
                        ."WHERE t.id=$tid AND t.forum IN ".forums_with_view_perm());

    if(!isset($thread[id]))
    {
      pageheader("Thread not found",0);
      thread_not_found();
/*        pageheader("(Invalid Thread)",0);
        print
	  "$L[TBL1]>
".        "  $L[TR2]>
".        "    $L[TD1c]><img src=img/onoz.gif>
".        "$L[TBLend]<br>$L[TBL1]>$L[TR2]>$L[TD1c]>
".        "    This thread is invalid or does not exist.
".        "$L[TBLend]
";
        pagefooter();
        die();*/
    }


    $feedicons.=feedicon("img/rss3.png","rss.php?thread=$thread[id]","RSS feed for this thread");
    $feedicons.=feedicon("img/rss2.png","rss.php?forum=$thread[forum]","RSS feed for this section");
    
    //append thread's title to page title
    pageheader($thread[title],$thread[fid]);

    //mark thread as read // 2007-02-21 blackhole89
    if($log && $thread[lastdate]>$thread[frtime])
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
      if($readstate[n] == 1) $sql->query("REPLACE INTO forumsread VALUES ($loguser[id],$thread[fid],".ctime().')');
    }

    //select top revision // 2007-03-08 blackhole89
    $posts=$sql->query("SELECT $fieldlist p.*, pt.text, pt.date ptdate, pt.user ptuser, pt.revision "
                      ."FROM posts p "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
//		      ."JOIN ("
//		        ."SELECT a.id,MAX(a.revision) toprev FROM poststext a GROUP BY a.id"
//		      .") as pt2 ON pt2.id=pt.id AND pt2.toprev=pt.revision "
                      ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr " //SQL barrel roll
                      ."LEFT JOIN users u ON p.user=u.id "
                      ."WHERE p.thread=$tid AND ISNULL(pt2.id) "
		      ."GROUP BY p.id "
                      ."ORDER BY p.id "
                      ."LIMIT ".(($page-1)*$loguser[ppp]).",".$loguser[ppp]);

    //load tags
    $tags=array();
    $t=$sql->query("SELECT * FROM tags WHERE fid=$thread[fid]");
    while($tt=$sql->fetch($t)) $tags[]=$tt;

  }elseif($uid){
    $user=$sql->fetchq("SELECT * "
                      ."FROM users "
                      ."WHERE id=$uid ");
    //title
    pageheader("Posts by $user[name]");
    $posts=$sql->query("SELECT $fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, f.id fid, t.title ttitle "
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
//                      .  "AND f.minpower<=$loguser[power] "
//                      .  "AND c.minpower<=$loguser[power] "
                      ."ORDER BY p.id "
                      ."LIMIT ".(($page-1)*$loguser[ppp]).",".$loguser[ppp]);

    $thread[replies]=$sql->resultq("SELECT count(*) "
                                  ."FROM posts p "
                                  ."LEFT JOIN threads t ON p.thread=t.id "
                                  ."LEFT JOIN forums f ON f.id=t.forum "
                                  ."LEFT JOIN categories c ON c.id=f.cat "
                                  ."WHERE p.user=$uid "
                                  .  "AND f.minpower<=$loguser[power] "
                                  .  "AND c.minpower<=$loguser[power]");
  }else
    pageheader();

  if($thread[replies]<$loguser[ppp]){
    $pagelist=''; $pagebr='';
  }else{
    $pagelist='<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">Pages:';
    for($p=1;$p<=1+floor($thread[replies]/$loguser[ppp]);$p++)
      if($p==$page)
        $pagelist.=" $p";
      elseif($tid)
        $pagelist.=" <a href=thread.php?id=$tid&page=$p>$p</a>";
      elseif($uid)
        $pagelist.=" <a href=thread.php?user=$uid&page=$p>$p</a>";
    $pagebr='<br>';
    $pagelist.='</div>';
  }

  if($tid){
    if($thread[closed])
      $newreply="Thread closed";
    else
      $newreply="<a href=newreply.php?id=$tid>New reply</a>";

    if($thread[ispoll])
    {
      if($act=="vote" && $log)
      {
	$vote=unpacksafenumeric($vote);
        if($vote==-1) {
//          echo $vote[0].",".$vote[1];
//          die();
        } else {
          if($thread[multivote]){
            if($thread[changeable]) {
              //changeable multivotes toggle
              $res=$sql->query("DELETE FROM pollvotes WHERE user='$loguser[id]' AND id='$vote'");
              if(!mysql_affected_rows()) $sql->query("REPLACE INTO pollvotes VALUES($vote,$loguser[id])");
            } else $sql->query("REPLACE INTO pollvotes VALUES($vote,$loguser[id])");
          } else if($thread[changeable]) {
            $sql->query("DELETE v FROM pollvotes v LEFT JOIN polloptions o ON o.id=v.id WHERE v.user=$loguser[id] AND o.poll=$tid");
            $sql->query("INSERT INTO pollvotes VALUES($vote,$loguser[id])");
          } else {
	    $res=$sql->resultq("SELECT COUNT(*) FROM pollvotes v LEFT JOIN polloptions o ON o.id=v.id WHERE v.user='$loguser[id]' AND o.poll=$tid"); 
	    if(!$res) $sql->query("INSERT INTO pollvotes VALUES($vote,$loguser[id])");
          }
        }
      }
      $poll=
          "<br>$L[TBL1]>
".        "  $L[TR1]>
".        "    $L[TD1] colspan=2>$thread[question]
";
      $opts=$sql->query("SELECT o.*,(COUNT(*) & (NOT ISNULL(v.user))*1023) c,((NOT ISNULL(w.user))*1) s FROM polloptions o LEFT JOIN pollvotes v ON v.id=o.id LEFT JOIN pollvotes w ON w.user='$loguser[id]' AND w.id=o.id WHERE poll=$tid GROUP BY o.id");
      $total=$sql->resultq("SELECT COUNT(DISTINCT v.user) FROM polloptions o, pollvotes v WHERE o.poll=$tid AND v.id=o.id");
      $mytotal=$log?$sql->resultq("SELECT COUNT(*) FROM polloptions o, pollvotes v WHERE o.poll=$tid AND v.id=o.id AND v.user='$loguser[id]'"):0;
      while($opt=$sql->fetch($opts))
      {
        $h=$opt[s]?"*":"";
	$cond=($thread[multivote]&&!$opt[s])||$thread[changeable]||!$mytotal;
        $poll.="$L[TR2]>$L[TD2]>".($cond?("<a href=thread.php?id=$tid&act=vote&vote=".urlencode(packsafenumeric($opt[id])).">"):"").$opt[option].($cond?"</a>":"")." $h$L[TD3]><img src=gfx/bargraph.php?z=$opt[c]&n=$total&r=$opt[r]&g=$opt[g]&b=$opt[b]>";
      }
      $poll.=
          "  $L[TR2]>$L[TDs] colspan=2>Multiple voting is ".($thread[multivote]?"":"not")." allowed. Changing your vote is ".($thread[changeable]?"":"not")." allowed. $total ".($total==1?"user has":"users have")." voted so far.
".        "$L[TBLend]
";
    }

//[KAWA] Thread +1
if(isset($_GET['thumbsup']))
{
  if (!has_perm('rate-thread')) no_perm();
	$sql->query("INSERT IGNORE INTO threadthumbs VALUES (".$loguser['id'].", ".$tid.")");
	$isThumbed = true;
}
else if(isset($_GET['thumbsdown']))
{
  if (!has_perm('rate-thread')) no_perm();
	$sql->query("DELETE FROM threadthumbs WHERE uid = ".$loguser['id']." AND tid = ".$tid);
	$isThumbed = false;
}
else
{
	$isThumbed = $sql->resultq("SELECT COUNT(*) FROM threadthumbs WHERE uid=".$loguser['id']." AND tid=".$tid) == 1;
}
$thumbsUp = "";
if(!$isThumbed)
	$thumbsUp = "<a href=\"thread.php?id=$tid&amp;thumbsup\">+1</a>";
else
	$thumbsUp = "<a href=\"thread.php?id=$tid&amp;thumbsdown\">-1</a>";
if($loguser['power'] > 0)
{
	$thumbCount = $sql->resultq("SELECT COUNT(*) FROM threadthumbs WHERE tid=".$tid);
	$thumbsUp .= " (".$thumbCount.")";
}


    $topbot=
          "$L[TBL] width=100%>$L[TR]>
".        "  $L[TDn]><a href=./>Main</a> - <a href=forum.php?id=$thread[forum]>$thread[ftitle]</a> - ".htmlval($thread[title])."</td>
".        "  $L[TDnr]>
".        "    $thumbsUp |
".        "    $newreply
".        "  </td>
".        "$L[TBLend]
";
  }elseif($uid){
    $topbot=
          "$L[TBL] width=100%>
".        "  $L[TDn]><a href=./>Main</a> - Posts by ".userlink($user)."</td>
".        "$L[TBLend]
";
  }

  
  
  $modlinks='<br>';
  if($tid && 
    (can_edit_forum_threads($thread[forum]) || 
      ($loguser[id] == $thread[user] && !$thread[closed] && has_perm('rename-own-thread')))) {
    $link="<a href=javascript:submitmod";
    if (can_edit_forum_threads($thread[forum])) {
      if($thread[sticky])
        $stick="$link('unstick')>Unstick</a>";
      else
        $stick="$link('stick')>Stick</a>";

      if($thread[closed])
        $close="| $link('open')>Open</a>";
      else
        $close="| $link('close')>Close</a>";

      if($thread[forum]!=$trashid)
        $trash="| $link('trash')>Trash</a> |";
      else
        $trash='| ';

      $retag=sizeof($tags)?"<a href=javascript:showtbox()>Tag</a> | ":"";
      $edit="<a href=javascript:showrbox()>Rename</a> | $retag <a href=javascript:showmove()>Move</a>";
    
		//KAWA: Made it a dropdown list. The change isn't alone in this file, but it's clear where it starts and ends if you want to put this on 2.1+delta.
      //$fmovelinks="";
      //$r=$sql->query("SELECT id,title FROM forums ORDER BY id ASC");
      //while($d=$sql->fetch($r))
      //  $fmovelinks.="<a href=javascript:submitmove('$d[id]') onmouseover=\"javascript:document.getElementById('tn').innerHTML='".addslashes($d[title])."'\">$d[id]</a> ";
      //$fmovelinks.="<span id=tn></span>";
      //$fmovelinks=addslashes($fmovelinks);
		$r=$sql->query("SELECT id,title FROM forums ORDER BY id ASC");
		$fmovelinks="<select onchange=\"submitmove(this.options[this.selectedIndex].value);\">";
		while($d=$sql->fetch($r))
			$fmovelinks.="<option value=\"".$d[0]."\"".($d[0]==$thread['forum']?" selected=\"selected\"":"").">".$d[1]."</option>";
		$fmovelinks.="</select>";
		$fmovelinks=addslashes($fmovelinks);

      $opt="Moderating";
    } else {
      $fmovelinks="";
      $close = $stick = $trash = "";
      $retag = sizeof($tags) ? "<a href=javascript:showtbox()>Tag</a> | " : "";
      $edit = "<a href=javascript:showrbox()>Rename</a>";
      $opt = "Thread";
    }
    $taglinks="";
    for($i=0;$i<sizeof($tags);++$i) {
      $t=$tags[$i];
      if(!($thread[tags] & (1<<$t[bit]))) $taglinks.="<a href=javascript:submittag('$t[bit]')>$t[tag]</a> ";
    }
    $taglinks.="| Remove: ";
    for($i=0;$i<sizeof($tags);++$i) {
      $t=$tags[$i];
      if($thread[tags] & (1<<$t[bit])) $taglinks.="<a href=javascript:submittag('$t[bit]')>$t[tag]</a> ";
    }
    $taglinks=addslashes($taglinks);

    $modlinks=
          "$L[TBL2]>$L[TR2]>
".        "  <form action=thread.php method=post name=mod>
".        "  $L[TD3]>
".        "    <span id=moptions>
".        "    $opt options:
".        "    $stick
".        "    $close
".        "    $trash
".        "    $edit
".        "    </span>
".        "    <span id=mappend>
".        "    <input type=hidden name=tmp style='width:80%!important;border-width:0px!important;padding:0px!important' onkeypress=\"submit_on_return(event,'rename')\" value=\"".htmlentities($thread[title])."\">
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
".        "    <input type=hidden name=c value=".md5($loguser[pass].$pwdsalt).">
".        "  </td>
".        "  </form>
".        "$L[TBLend]
";
  }

  print   "$topbot
".        "$modlinks
".        "$pagelist
".        "$poll
";
  while($post=$sql->fetch($posts)){
    if (isset($post['fid'])) {
      if (!can_view_forum($post['fid'])) continue;
    }
    if($uid){
      $pthread[id]=$post[tid];
      $pthread[title]=$post[ttitle];
    }
    if($post[id]!=$_GET[pin]){
      $post[maxrevision]=$post[revision]; // not pinned, hence the max. revision equals the revision we selected
    } else {
      $post[maxrevision]=$sql->resultq("SELECT MAX(revision) FROM poststext WHERE id=$_GET[pin]");
    }
    if(can_edit_forum_posts($pthread[forum]) && $post[id]==$_GET[pin]) $post[deleted]=false;
    print "<br>
".         threadpost($post,0,$pthread);
  }
  print   "$pagelist$pagebr
".        "<br>
".        "$topbot";

  pagefooter();
?>
                                                                                                   