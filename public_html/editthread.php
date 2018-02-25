<?php
  require 'lib/common.php';
  require 'lib/threadpost.php';
  //loadsmilies();

  if(isset($_POST['action'])){ $tid=$_POST['id']; $act=1; } else { $tid=$_GET['id']; $act=0; }
  checknumeric($tid);
  needs_login(1);

    $thread=$sql->fetchq("SELECT t.*, NOT ISNULL(p.id) ispoll, p.question, p.multivote, p.changeable, f.title ftitle, t.forum fid".($log?', r.time frtime':'').' '
                        ."FROM threads t LEFT JOIN forums f ON f.id=t.forum "
                  .($log?"LEFT JOIN forumsread r ON (r.fid=f.id AND r.uid=$loguser[id]) ":'')
		  	."LEFT JOIN polls p ON p.id=t.id "
                        ."WHERE t.id=$tid AND t.forum IN ".forums_with_view_perm());

    if(!isset($thread['id']))
    {
      error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
    }
if (!can_edit_forum_threads($thread[forum])){
  $err="    You have no permissions to modify threads in this forum!";
pageheader("Edit Thread");
    print "$top - Error";
    noticemsg("Error", $err);
    pagefooter();
    die();
  }

  $ispoll=$thread['ispoll'];
if ($ispoll==1) $cntopts=@$sql->result($sql->query("SELECT count(*) FROM polloptions WHERE poll=$tid"));

  if($act==1){
  //Submission Code
  $backlink="<a href=\"thread.php?id=$tid\">Return to thread</a>";
  //Error Detections
    if(strlen(trim(str_replace(" ","",$_POST['title'])))<4)
      $err="    You need to enter a longer $type title.<br>
".         "    $backlink";
    if($ispoll && (!isset($_POST['opt']) || count($_POST['opt']) < 2))
      $err="    You must add atleast two choices to your poll.<br>
".         "    $backlink";
    else if(isset($ispoll)) {
      foreach ($_POST['opt'] as $id => $text)
        if(trim($text) == '' || $_POST['col'][$id] == '')
          $err="You must fill in all poll choices' fields.<br>
".             "$backlink";
    }
if(isset($err)){
pageheader("Edit Thread");
    print "$top - Error";
    noticemsg("Error", $err);
} else {
  //No Errors detected
  if(!($iconurl=$_POST[iconurl]))
    $iconurl=$sql->resultq("SELECT url FROM posticons WHERE id=".(int)$_POST[iconid]);
  $title = $sql->escape($_POST['title']);
  $iconurl=addslashes($iconurl);
  $sql->query("UPDATE threads SET `title`='{$title}',`icon`='$iconurl' WHERE `id`=$tid");
  $n=1;
  if(isset($ispoll)){
      $sql->query("UPDATE polls SET `id`=$tid,`question`='{$_POST['question']}',`multivote`='{$_POST['multivote']}',`changeable`='{$_POST['changeable']}' WHERE `id`=$tid");  
      foreach ($_POST['opt'] as $id => $_text)
	  {
	    $color = stripslashes($_POST['col'][$id]);
		list($r,$g,$b) = sscanf(strtolower($color), '%02x%02x%02x');
		$text = $sql->escape($_text);
//        $sql->query("UPDATE polloptions SET `option`='{$text}',r=".(int)$r.",g=".(int)$g.",b=".(int)$b." WHERE id=$id AND `poll`=$tid");
if($n>$cntopts){ $insid="null"; } else { $insid=$id; }
        $sql->query("REPLACE INTO polloptions (`id`,`poll`,`option`,r,g,b) VALUES ($insid,$tid,'{$text}',".(int)$r.",".(int)$g.",".(int)$b.")");
        $n++;
	  }
    }
}
  redirect("thread.php?id=$tid",-1);
  } else {

  //No submitted data, fetch from the thread/poll data
pageheader("Edit Thread");
//Thread icon code.
$i=1;
  $icons=$sql->query('SELECT * FROM posticons ORDER BY id');
  while($icon=$sql->fetch($icons)){
    if($thread['icon']==$icon['url']){ $ext="checked"; $match=$icon['url']; } else { $ext=""; }
    $iconlist.=
          "      $L[INPr]=iconid value=$i $ext> <img src=$icon[url]>&nbsp; &nbsp;".(!($i++%10)?'<br>':'');
  }
  if(!isset($match)){
  $iconlist.=
          "      $L[INPr]=iconid value=0 checked> None&nbsp; &nbsp;
".        "      Custom: $L[INPt]=iconurl value=\"".$thread['icon']."\" size=40 maxlength=100>
";
} else { $ext="value=\"$match\""; }
  $iconlist.=
          "      $L[INPr]=iconid value=0> None&nbsp; &nbsp;
".        "      Custom: $L[INPt]=iconurl size=40 maxlength=100>
";


  echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>
".        " <form action=editthread.php method=post>
".        " $L[INPh]=id value=$tid>
".        " $L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Editing Thread - ".$thread['title']."</td>
";
    print "  $L[TR]>
".        "    $L[TD1c]>$typecap title:</td>
".        "    $L[TD2]>$L[INPt]=title value=\"".$thread['title']."\" size=100 maxlength=100></td>
".        "  $L[TR]>
".        "    $L[TD1c]>$typecap icon:</td>
".        "    $L[TD2]>
".        "$iconlist
";

//Do we have a poll?
	if ($ispoll)
	{
		echo '<script type="text/javascript" src="jscolor/jscolor.js"></script>';
		echo '<script type="text/javascript" src="polleditor.js"></script>';
		$optfield1 = '<div><input type="text" name="opt[';
		$optfield2 = ']" size=40 maxlength=40 value="%s"> - Color: <input class="color" name="col[';
		$optfield3 = ']" value="%02X%02X%02X"> - <button class="submit" onclick="removeOption(this.parentNode);return false;">Remove</button></div>';
	}
    if($ispoll){
      echo 
          "$L[TR]>
".        "  $L[TD1c]>Poll question:</td>
".        "  $L[TD2]>$L[INPt]=question size=100 maxlength=100 value=\"".htmlval($thread['question'])."\"></td>
".        "$L[TR]>
".        "  $L[TD1c]>Poll choices:</td>
".        "  $L[TD2]><div id=\"polloptions\">";
$n=0;
while($n<$cntopts){
  $poll=$sql->fetchq("SELECT * FROM `polloptions` WHERE `poll`=$tid ORDER BY `id` LIMIT $n,1");
  $pid=$poll['id'];
  $str="$optfield1".$pid."$optfield2".$pid."$optfield3";
  echo sprintf($str, $poll['option'], $poll['r'], $poll['g'], $poll['b']);
  $n++;
}
echo "  </div>
".        "  $L[BTTn]=addopt onclick=\"addOption();return false;\">Add choice</button></td>
".        "$L[TR]>
".             "  $L[TD1c]>Options:</td>
".             "  $L[TD2]>$L[INPc]=multivote value=1 id=mv><label for=mv>Allow multiple voting</label> | $L[INPc]=changeable checked value=1 id=ch><label for=ch>Allow changing one's vote</label>
";
    }
echo "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPs]=action value=Submit>$L[TBLend]";
}
  pagefooter();
?>
