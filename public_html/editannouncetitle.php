<?php
//Uses editpost.php as template

  require 'lib/common.php';

  if($act=$_POST[action])
  {
    $pid=$_POST[pid];  

  }
  else
  {
    $pid=$_GET[pid];
  }
  
  checknumeric($pid);

  needs_login(1);

  $thread=$sql->fetchq('SELECT p.user puser, t.*, f.title ftitle, f.private fprivate, f.readonly freadonly '
                      .'FROM posts p '
                      .'LEFT JOIN threads t ON t.id=p.thread '
                      .'LEFT JOIN forums f ON f.id=t.forum '
                      ."WHERE p.id=$pid AND t.announce=1 AND (t.forum IN ".forums_with_view_perm()." OR (t.forum IN (0, NULL) AND t.announce>=1))");


  if (!$thread) $pid = 0;
if($act!="Submit"){
  echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
}
else if (!can_edit_post(array('user'=>$thread['puser'], 'tforum' => $thread['forum']))) {
      error("Error", "<a href=http://board.kafuka.org/profile.php?id=156><span class=nc12 style=color:#FF3399;>SquidEmpress</span></a> decided to put this message here because she felt like it.<br> <a href=./>Back to main</a>");
  }
  elseif($pid==-1){
      error("Error", "<a href=http://board.kafuka.org/profile.php?id=156><span class=nc12 style=color:#FF3399;>SquidEmpress</span></a> decided to put this message here because she felt like it.<br> <a href=./>Back to main</a>");
  }

  $top='<a href=./>Main</a> '
    .($thread[forum]==0 ? "- <a href=thread.php?announce=0>Announcements</a> " : "- <a href=forum.php?id=$thread[forum]>$thread[ftitle]</a> ")
    .'- Edit announcement title';

  $res=$sql->query  ("SELECT u.id, p.user, p.mood, p.nolayout, pt.text "
                    ."FROM posts p "
                    ."LEFT JOIN poststext pt ON p.id=pt.id "
                    ."JOIN ("
                      ."SELECT id,MAX(revision) toprev FROM poststext GROUP BY id"
                    .") as pt2 ON pt2.id=pt.id AND pt2.toprev=pt.revision "
                    ."LEFT JOIN users u ON p.user=u.id "
                    ."WHERE p.id=$pid");

  if(@$sql->numrows($res)<1){
    error("Error", "<a href=http://board.kafuka.org/profile.php?id=156><span class=nc12 style=color:#FF3399;>SquidEmpress</span></a> decided to put this message here because she felt like it.<br> <a href=./>Back to main</a>");
    }


  $post=$sql->fetch($res);
if(!$act){
  pageheader('Edit announcement title',$thread[forum]);
    print "$top
".        "<br><br>
".        "<table cellspacing=\"0\" class=\"c1\">
".        " <form action=editannouncetitle.php method=post>
".        "  <tr class=\"h\">
".        "    <td class=\"b h\" colspan=2>Edit Announcement Title</td>
".        "  <tr>
".        "    <td class=\"b n1\" align=\"center\">Title:</td>
".        "    <td class=\"b n2\"><input type=\"text\" name=title size=100 maxlength=100 value='".$thread[title]."' class='right'></td>
".        "  <tr class=\"n1\">
".        "    <td class=\"b\">&nbsp;</td>
".        "    <td class=\"b\">
".        "      <input type=\"hidden\" name=pid value=$pid>
".        "      <input type=\"submit\" class=\"submit\" name=action value=Submit>
".        "    </td>
".        " </form>
".        "</table>
";
  }elseif($act=='Submit'){
    $sql->query("UPDATE threads SET title='$_POST[title]' WHERE id='$thread[id]'");

/*if($loguser[redirtype]==0){ //Classical Redirect
  $loguser['blocksprites']=1;
  pageheader('Edit announcement title',$thread[forum]);
    print "$top - Submit
".        "<br><br>
".        "<table cellspacing=\"0\" class=\"c1\">
".        "  <td class=\"b n1\" align=\"center\">
".        "    Announcement title edited!<br>
".        "    ".redirect("thread.php?pid=$pid#$pid",htmlval($thread[title]))."
".        "</table>
";
} else { //Modern redirect*/
  redirect("thread.php?pid=$pid#edit","-1");
//}
  }

  pagefooter();
?>