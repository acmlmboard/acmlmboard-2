<?php
  require 'lib/common.php';
  require 'lib/threadpost.php';
  loadsmilies();

    $announce=$_REQUEST[announce];
    checknumeric($announce);

  if($act=$_POST[action])
  {
    $fid=$_POST[fid];
    if ($log)
	{
		$userid = $loguser['id'];
		$user = $loguser;
		if ($_POST['passenc'] !== md5($pwdsalt2.$loguser['pass'].$pwdsalt))
			$err = 'Invalid token.';
			
		$pass = $_POST['passenc'];
	}
	else
	{
      if($_POST['passenc'])
         $pass=$_POST['passenc'];
      else
         $pass=md5($pwdsalt2.$_POST['pass'].$pwdsalt);

    $userid=checkuser($_POST['name'],$pass);
    if($userid) {
      $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");
      $loguser=$user;
       load_user_permset();
    }
    else
      $err="    Invalid username or password!<br>
".         "    <a href=forum.php?id=$fid>Back to forum</a> or <a href=newthread.php?id=$fid>try again</a>";

	}
  }
  else
  {
    $user=$loguser;
    $fid=$_GET[id];
  }
  checknumeric($fid);

  if ($announce) {
    $type = "announcement";
    $typecap = "Announcement";
  }
  elseif ($_GET[ispoll]) {
    $type = "poll";
    $typecap = "Poll";
	$ispoll = 1;
  }
  else {
    $type = "thread";
    $typecap = "Thread";
	$ispoll = 0;
  }


  if ($announce && $fid==0)
	$forum = array('id' => 0, 'readonly' => 1);
  else
    $forum=$sql->fetchq("SELECT * FROM forums WHERE id=$fid AND id IN ".forums_with_view_perm());
	
if($act!="Submit"){
  echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
  $toolbar= posttoolbar();
	 
	if ($ispoll)
	{
		echo '<script type="text/javascript" src="jscolor/jscolor.js"></script>';
		echo '<script type="text/javascript" src="polleditor.js"></script>';
		$optfield = '<div><input type="text" name="opt[]" size=40 maxlength=40 value="%s"> - Color: <input class="color" name="col[]" value="%02X%02X%02X"> - <button class="submit" onclick="removeOption(this.parentNode);return false;">Remove</button></div>';
	}
}
  $tagsin="";
  $t=$sql->query("SELECT * FROM tags WHERE fid=$fid");
  while($tt=$sql->fetch($t)) {
    if($tagsin=="") $tagsin=
          "<tr>
".        "  <td class=\"b n1\" align=\"center\">$typecap tags:</td>
".        "  <td class=\"b n2\">
";
    $tagsin.="<input type='checkbox' name='tag$tt[bit]' id='tag$tt[bit]' value='1' ".($_POST["tag$tt[bit]"]?"checked":"")."><label for='tag$tt[bit]'>$tt[name]</label> ";
  }
  if($tagsin!="") $tags.="</tr>";

  $forumlink="<a href=forum.php?id=$fid>Back to forum</a>";

  if(!$forum) {
	error("Error", "Forum does not exist. <br> <a href=./>Back to main</a>");
  }

    else if ($announce && !can_create_forum_announcements($fid))
    $err = "    You have no permissions to create announcements in this forum!<br>$forumLink";

     else if (!can_create_forum_thread($forum)){

  $err="    You have no permissions to create threads in this forum!<br>$forumlink";
  }

  else if($user[lastpost]>ctime()-30 && $act=='Submit' && !has_perm('ignore-thread-time-limit'))
      $err="    Don't post threads so fast, wait a little longer.<br>
".         "    $forumlink";

  else if($user[lastpost]>ctime()-$config[secafterpost] && $act=='Submit' && has_perm('ignore-thread-time-limit'))
      $err="    You must wait $config[secafterpost] seconds before posting a thread.<br>
".         "    $forumlink";

  //2007-02-19 //blackhole89 - table breach protection
  if($act=='Submit'){
    $title = $_POST['title'];
    $message =  $_POST['message'];
    if(($tdepth=tvalidate($message))!=0)
      $err="    This post would disrupt the board's table layout! The calculated table depth is $tdepth.<br>
".         "    $forumlink";
    if(strlen(trim(str_replace(" ","",$title)))<4)
      $err="    You need to enter a longer $type title.<br>
".         "    $forumlink";
    if($ispoll && (!isset($_POST['opt']) || count($_POST['opt']) < 2))
      $err="    You must add atleast two choices to your poll.<br>
".         "    $forumlink";
    else if($ispoll) {
      foreach ($_POST['opt'] as $id => $text)
        if(trim($text) == '' || $_POST['col'][$id] == '')
          $err="You must fill in all poll choices' fields.<br>
".             "$forumlink";
    }
  }

  $top="<a href=./>Main</a> - <a href=forum.php?id=$fid>$forum[title]</a> - New $type";

  $i=1;
  $icons=$sql->query('SELECT * FROM posticons ORDER BY id');
  while($icon=$sql->fetch($icons))
    $iconlist.=
          "      <input type=\"radio\" class=\"radio\" name=iconid value=$i> <img src=$icon[url]>&nbsp; &nbsp;".(!($i++%10)?'<br>':'')."
";
  $iconlist.=
          "      <input type=\"radio\" class=\"radio\" name=iconid value=0 checked> None&nbsp; &nbsp;
".        "      Custom: <input type=\"text\" name=iconurl size=40 maxlength=100>
";

  if($err){
    pageheader("New $type",$forum[id]);
    print "$top - Error";
    noticemsg("Error", $err);
  }elseif(!$act){
    if($ispoll){
      $pollin=
          "<tr>
".        "  <td class=\"b n1\" align=\"center\">Poll question:</td>
".        "  <td class=\"b n2\"><input type=\"text\" name=question size=100 maxlength=100 value=\"".htmlval($_POST[question])."\"></td>
".        "<tr>
".        "  <td class=\"b n1\" align=\"center\">Poll choices:</td>
".        "  <td class=\"b n2\"><div id=\"polloptions\">
".        "    ".sprintf($optfield, '', rand(0,255), rand(0,255), rand(0,255))."
".        "    ".sprintf($optfield, '', rand(0,255), rand(0,255), rand(0,255))."
".        "  </div>
".        "  <button type=\"button\" class=\"submit\" id=addopt onclick=\"addOption();return false;\">Add choice</button></td>
".        "<tr>
".             "  <td class=\"b n1\" align=\"center\">Options:</td>
".             "  <td class=\"b n2\"><input type=\"checkbox\" name=multivote value=1 id=mv><label for=mv>Allow multiple voting</label> | <input type=\"checkbox\" name=changeable checked value=1 id=ch><label for=ch>Allow changing one's vote</label>
";
    }
 pageheader("New $type",$forum[id]);
    print "$top
".        "<br><br>
".        "<form action=newthread.php?ispoll=$ispoll method=post>
".        " <table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"h\">
".        "    <td class=\"b h\" colspan=2>$typecap</td>
";
    if(!$log)
    print "  <tr>
".        "    <td class=\"b n1\" align=\"center\">Username:</td>
".        "    <td class=\"b n2\"><input type=\"text\" name=name size=25 maxlength=25></td>
".        "  <tr>
".        "    <td class=\"b n1\" align=\"center\">Password:</td>
".        "    <td class=\"b n2\"><input type=\"password\" name=pass size=13 maxlength=32></td>
";
    else
    print "  <input type=\"hidden\" name=name value=\"".htmlval($loguser[name])."\">
".        "  <input type=\"hidden\" name=passenc value=\"".md5($pwdsalt2.$loguser[pass].$pwdsalt)."\">
";
    print "  <tr>
".        "    <td class=\"b n1\" align=\"center\">$typecap title:</td>
".        "    <td class=\"b n2\"><input type=\"text\" name=title size=100 maxlength=100></td>
".        "  <tr>
".        "    <td class=\"b n1\" align=\"center\">$typecap icon:</td>
".        "    <td class=\"b n2\">
".        "$iconlist
".        "    </td>
".$tagsin."
".$pollin."
";
     if($loguser[posttoolbar]!=1)
print     "  <tr>
".        "    <td class=\"b n1\" align=\"center\" width=120>Format:</td>
".        "    <td class=\"b n2\"><table cellspacing=\"0\"><tr>$toolbar</table>
";
print     "  <tr>
".        "    <td class=\"b n1\" align=\"center\" width=120>Post:</td>
".        "    <td class=\"b n2\"><textarea wrap=\"virtual\" name=message id='message' rows=20 cols=80></textarea></td>
".        "  <tr class=\"n1\">
".        "    <td class=\"b\">&nbsp;</td>
".        "    <td class=\"b\">
".        "      <input type=\"hidden\" name=fid value=$fid>
".        "      <input type=\"hidden\" name=announce value=$announce>
".        "      <input type=\"submit\" class=\"submit\" name=action value=Submit>
".        "      <input type=\"submit\" class=\"submit\" name=action value=Preview>
";
     if($log)
print   // 2009-07 Sukasa: Newthread mood selector, just in the place I put it in mine
          "      <select name=mid>".moodlist()."
";
print   "      <input type=\"checkbox\" name=nolayout id=nolayout value=1 ".($_POST[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
".      "      <input type=\"checkbox\" name=nosmilies id=nosmilies value=1 ".($_POST[nosmilies]?"checked":"")."><label for=nosmilies>Disable smilies</label>
";
    if(can_edit_forum_threads($fid) && !$announce)
    print "     <input type=\"checkbox\" name=close id=close value=1 ".($_POST[close]?"checked":"")."><label for=close>Close thread</label>
".        "      <input type=\"checkbox\" name=stick id=stick value=1 ".($_POST[stick]?"checked":"")."><label for=stick>Stick thread</label>
";
    print "    </td>
".        " </table>
".        "</form>
";
  }elseif($act=='Preview'){
    $_POST[title]  =stripslashes($_POST[title]);
    $_POST[message]=stripslashes($_POST[message]);

    $post[date]=ctime();
    $post[ip]=$userip;
    $post[num]=++$user[posts];
    $post[text]=$_POST[message];
    $post[mood] = (isset($_POST[mid]) ? (int)$_POST[mid] : -1); // 2009-07 Sukasa: Newthread preview
    $post[nolayout]=$_POST[nolayout];
    $post[nosmilies]=$_POST[nosmilies];
    $post[close]=$_POST[close];
    $post[stick]=$_POST[stick];
    foreach($user as $field => $val)
      $post[u.$field]=$val;
    $post[ulastpost]=ctime();

    if($ispoll){
      $_POST[question]=stripslashes($_POST[question]);
      $numopts=$_POST[numopts];
      checknumeric($numopts);
      $pollprev="<br><table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"n1\">
".        "    <td class=\"b n1\" colspan=2>".htmlval($_POST[question])."
";
      $pollin=
          "<tr>
".        "  <td class=\"b n1\" align=\"center\">Poll question:</td>
".        "  <td class=\"b n2\"><input type=\"text\" name=question size=100 maxlength=100 value=\"".htmlval($_POST[question])."\"></td>
".        "<tr>
".        "  <td class=\"b n1\" align=\"center\">Poll choices:</td>
".        "  <td class=\"b n2\"><div id=\"polloptions\">
";

      if (isset($_POST['opt']))
	  {
		  foreach ($_POST['opt'] as $id => $text)
		  {
			$text = htmlval(stripslashes($text));
			
			$color = stripslashes($_POST['col'][$id]);
			list($r,$g,$b) = sscanf(strtolower($color), '%02x%02x%02x');
			
			$pollin .= "    ".sprintf($optfield, $text, $r, $g, $b)."\n";
			$pollprev .= "<tr class=\"n2\"><td class=\"b n2\">{$text} $h<td class=\"b n3\"><img src=\"gfx/bargraph.php?z=1&n=1&r={$r}&g={$g}&b={$b}\">";

		  }
	  }
	  
      $pollin.="  </div>
".             "  <button type=\"button\" class=\"submit\" id=addopt onclick=\"addOption();return false;\">Add choice</button></td>
".             "<tr>
".             "  <td class=\"b n1\" align=\"center\">Options:</td>
".             "  <td class=\"b n2\"><input type=\"checkbox\" name=multivote ".($_POST[multivote]?"checked":"")." value=1 id=mv><label for=mv>Allow multiple voting</label> | <input type=\"checkbox\" name=changeable ".($_POST[changeable]?"checked":"")." value=1 id=ch><label for=ch>Allow changing one's vote</label>
";
$pollprev.="</table>";
    }

 pageheader("New $type",$forum[id]);
    print "$top - Preview
".        "$pollprev<br>
".        "<table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"h\">
".        "    <td class=\"b h\" colspan=2>Post preview
".        "</table>
".         threadpost($post,0)."
".        "<br>
".        "<form action=newthread.php?ispoll=$ispoll method=post>
".        " <table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"h\">
".        "    <td class=\"b h\" colspan=2>$typecap</td>
".        "      <input type=\"hidden\" name=name value=\"".htmlval(stripslashes($_POST[name]))."\">
".        "      <input type=\"hidden\" name=passenc value=\"$pass\">
".        "  <tr>
".        "    <td class=\"b n1\" align=\"center\">$typecap title:</td>
".        "    <td class=\"b n2\"><input type=\"text\" name=title size=100 maxlength=100 value=\"".htmlval($_POST[title])."\"></td>
".$tagsin."
".$pollin."
";
     if($loguser[posttoolbar]!=1)
print     "  <tr>
".        "    <td class=\"b n1\" align=\"center\" width=120>Format:</td>
".        "    <td class=\"b n2\"><table cellspacing=\"0\"><tr>$toolbar</table>
";
print     "  <tr>
".        "    <td class=\"b n1\" align=\"center\" width=120>Post:</td>
".        "    <td class=\"b n2\"><textarea wrap=\"virtual\" name=message id='message' rows=10 cols=80>".htmlval($_POST[message])."</textarea></td>
".        "  <tr class=\"n1\">
".        "    <td class=\"b\">&nbsp;</td>
".        "    <td class=\"b\">
".        "      <input type=\"hidden\" name=fid value=$fid>
".        "      <input type=\"hidden\" name=iconid value=$_POST[iconid]>
".        "      <input type=\"hidden\" name=iconurl value=$_POST[iconurl]>
".        "      <input type=\"hidden\" name=announce value=$announce>
".        "      <input type=\"submit\" class=\"submit\" name=action value=Submit>
".        "      <input type=\"submit\" class=\"submit\" name=action value=Preview>
".        // 2009-07 Sukasa: Newthread mood selector, just in the place I put it in mine
          "      <select name=mid>".moodlist($_POST[mid], $userid)."
".        "      <input type=\"checkbox\" name=nolayout id=nolayout value=1 ".($post[nolayout]?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "      <input type=\"checkbox\" name=nosmilies id=nosmilies value=1 ".($post[nosmilies]?"checked":"")."><label for=nosmilies>Disable smilies</label>
";
    if(can_edit_forum_threads($fid) && !$announce)
    print "     <input type=\"checkbox\" name=close id=close value=1 ".($post[close]?"checked":"")."><label for=close>Close thread</label>
".        "      <input type=\"checkbox\" name=stick id=stick value=1 ".($post[stick]?"checked":"")."><label for=stick>Stick thread</label>
";
    print "    </td>
".        " </table>
".        "</form>
";
  }elseif($act=='Submit'){
    if(!($iconurl=$_POST[iconurl]))
      $iconurl=$sql->resultq("SELECT url FROM posticons WHERE id=".(int)$_POST[iconid]);

    checknumeric($_POST[nolayout]);
    checknumeric($_POST[nosmilies]);
    if(can_edit_forum_threads($fid)){
        checknumeric($_POST['close']);
        checknumeric($_POST['stick']);
   	if($_POST['close']) $modclose="1";
   	if($_POST['stick']) $modstick="1";
    }

    if(!$_POST['close']) $modclose="0";
    if(!$_POST['stick']) $modstick="0";

    $iconurl=addslashes($iconurl);

    $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid");
    $user[posts]++;

    $tagsum=0;
    for($i=0;$i<32;++$i) if($_POST["tag$i"]) $tagsum|=(1<<$i);
    
    $mid=(isset($_POST[mid]) ? (int)$_POST[mid] : -1);
    if($announce) {
    $modclose=$announce;
    }

    $sql->query("UPDATE users SET posts=posts+1,threads=threads+1,lastpost=".ctime()." "
               ."WHERE id=$userid");
    $sql->query("INSERT INTO threads (title,forum,user,lastdate,lastuser,icon,tags,announce,closed,sticky) "
               ."VALUES ('$_POST[title]',$fid,$userid,".ctime().",$userid,'$iconurl',$tagsum,$announce,$modclose,$modstick)");
    $tid=$sql->insertid();
    $sql->query("INSERT INTO posts (user,thread,date,ip,num,mood,nolayout,nosmilies,announce) "
               ."VALUES ($userid,$tid,".ctime().",'$userip',$user[posts],$mid,'$_POST[nolayout]','$_POST[nosmilies]',$announce)");
    $pid=$sql->insertid();
    $sql->query("INSERT INTO poststext (id,text) VALUES ($pid,'$message')");
if (!$announce)   {
   $sql->query("UPDATE forums SET threads=threads+1,posts=posts+1,lastdate=".ctime().",lastuser=$userid,lastid=$pid "
               ."WHERE id=$fid");
}
    $sql->query("UPDATE threads SET lastid=$pid WHERE id=$tid");

    if($ispoll)
    {
      $sql->query("INSERT INTO polls (id,question,multivote,changeable) VALUES ($tid,'{$_POST['question']}','{$_POST['multivote']}','{$_POST['changeable']}')");
	  
      foreach ($_POST['opt'] as $id => $_text)
	  {
	    $color = stripslashes($_POST['col'][$id]);
		list($r,$g,$b) = sscanf(strtolower($color), '%02x%02x%02x');
		$text = $sql->escape($_text);
		
        $sql->query("INSERT INTO polloptions (`poll`,`option`,r,g,b) VALUES ($tid,'{$text}',".(int)$r.",".(int)$g.",".(int)$b.")");
	  }
    }

    // bonus shit
    $c = rand(250,750);
    if (!$announce) $sql->query("UPDATE `usersrpg` SET `spent` = `spent` - '$c' WHERE `id` = '$userid'");

    $chan = $sql->resultp("SELECT chan FROM announcechans WHERE id=?",array($forum['announcechan_id']));

if ($announce) {
  $viewlink = "thread.php?announce=".$forum['id'];
  $shortlink = "a=".$forum['id'];
  $bonus = "";
}
else {
  $viewlink = "thread.php?id=$tid";
  $shortlink = "t=$tid";
  $bonus = "    Posted! (Gained $c bonus coins)<br>";
}

if ($announce && $forum['id']==0) {
     sendirc("{irccolor-base}New $type by {irccolor-name}".get_irc_displayname()."{irccolor-url}: {irccolor-name}".stripslashes($_POST[title])."{irccolor-base} - {irccolor-url}{boardurl}?$shortlink{irccolor-base}",$chan);
}
else if ($announce) {
     sendirc("{irccolor-base}New forum $type by {irccolor-name}".get_irc_displayname()."{irccolor-base} in {irccolor-title}$forum[title]{irccolor-url}: {irccolor-name}".stripslashes($_POST[title])."{irccolor-base} - {irccolor-url}{boardurl}?$shortlink{irccolor-base}",$chan);  
}
else {
     
     sendirc("{irccolor-base}New $type by {irccolor-name}".get_irc_displayname()."{irccolor-base} in {irccolor-title}".$forum[title]."{irccolor-url}: {irccolor-name}".stripslashes($_POST[title])."{irccolor-base} - {irccolor-url}"."{boardurl}?$shortlink{irccolor-base}",$chan);

}


/*if($loguser[redirtype]==0){ //Classic
    $loguser['blocksprites']=1;
    pageheader("New $type",$forum[id]);
    print "$top - Submit
".        "<br><br>
".        "<table cellspacing=\"0\" class=\"c1\">
".        "  <td class=\"b n1\" align=\"center\">
".        "  $bonus
".        "    ".redirect($viewlink,"the $type")."
".        "</table>
";
} else { //Modern*/
  redirect($viewlink,$c);
//}
  }

  pagefooter();
?>