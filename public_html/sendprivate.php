<?php
  require 'lib/common.php';
  require 'lib/threadpost.php';
  loadsmilies();

  needs_login(1);
  $act=checkvar('act');

  if($act!="Submit"){
    $js="<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
  }

  $top='<a href=./>Main</a> '
    .'- <a href=private.php>Private messages</a> '
    .'- Send';

  $toolbar= posttoolbar($loguser['posttoolbar']);

  if (!has_perm('create-pms')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

  if(isset($err)){
    print "$top - Error
".        "<br><br>
".        "$L[TBL1]>
".        "  $L[TD1c]>
".        "$err
".        "$L[TBLend]
";
  }elseif(!$act=checkvar('_POST','action')){
    if($pid=checkvar('_GET','pid')){
      checknumeric($pid);
      $post=$sql->fetchq("SELECT IF(u.displayname='',u.name,u.displayname) name, p.title, pt.text "
                        ."FROM pmsgs p "
                        ."LEFT JOIN pmsgstext pt ON p.id=pt.id "
                        ."LEFT JOIN users u ON p.userfrom=u.id "
                        ."WHERE p.id=$pid".(!has_perm('view-user-pms')?" AND (p.userfrom=".$loguser['id']." OR p.userto=".$loguser['id'].")":''));
      if($post){
        $quotetext="[reply=\"".$post['name']."\" id=\"$pid\"]".$post['text']."[/quote]\n";
        $title="Re: ".$post['title'];
        $userto=$post['name'];
      }
    }

    if($uid=checkvar('_GET','uid')){
      checknumeric($uid);
      $userto=$sql->resultq("SELECT IF(displayname='',name,displayname) name FROM users WHERE id=$uid");
    }elseif(!isset($userto))
      $userto=checkvar('_POST','userto');

  pageheader('Send private message');
    print "$js $top
".        "<br><br>
".        "$L[TBL1]>
".        " <form action=sendprivate.php method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Send message</td>
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Send to:</td>
".        "    $L[TD2]>$L[INPt]=userto size=25 maxlength=25 value=\"".htmlval($userto)."\"></td>
".        "  $L[TR]>
".        "    $L[TD1c]>Title:</td>
".        "    $L[TD2]>$L[INPt]=title size=80 maxlength=255 value=\"".htmlval(checkvar('title'))."\"></td>
";
     if($loguser['posttoolbar']!=1)  
print     "  $L[TR]>
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
";
print     "  $L[TR]>
".        "    $L[TD1c]>Message:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=20 cols=80>".htmlval(checkvar('quotetext'))."</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        "      $L[INPl]=mid>".moodlist()."
".        "      $L[INPc]=nolayout id=nolayout value=1 ".(checkvar('_POST','nolayout')?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Preview'){
    $_POST['title']=stripslashes($_POST['title']);
    $_POST['message']=stripslashes($_POST['message']);

    $post['date']=ctime();
    $post['ip']=$userip;
    $post['num'] = $post['id'] = $post['deleted'] = $post['thread'] = $post['revision'] = $post['maxrevision'] = $post['user'] = 0;
    $post['text']=$_POST['message'];
    $post['mood'] = (isset($_POST['mid']) ? (int)$_POST['mid'] : -1);
    $post['nolayout']=checkvar('_POST','nolayout');
    foreach($loguser as $field=>$val)
      $post['u'.$field]=$val;
    $post['ulastpost']=ctime();

  pageheader('Send private message');
    print "$js $top - Preview
".        "<br>
".        "$L[TBL1]>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Message preview
".        "$L[TBLend]
".         threadpost($post,0)."
".        "<br>
".        "$L[TBL1]>
".        " <form action=sendprivate.php method=post>
".        "  $L[TRh]>
".        "    $L[TDh] colspan=2>Send message</td>
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Send to:</td>
".        "    $L[TD2]>$L[INPt]=userto size=25 maxlength=25 value=\"".htmlval($_POST['userto'])."\"></td>
".        "  $L[TR]>
".        "    $L[TD1c] width=120>Title:</td>
".        "    $L[TD2]>$L[INPt]=title size=80 maxlength=255 value=\"".htmlval($_POST['title'])."\"></td>
";
     if($loguser['posttoolbar']!=1)  
print     "  $L[TR]>
".        "    $L[TD1c] width=120>Format:</td>
".        "    $L[TD2]>$L[TBL]>$L[TR]>$toolbar$L[TBLend]
";
print     "  $L[TR]>
".        "    $L[TD1c] width=120>Message:</td>
".        "    $L[TD2]>$L[TXTa]=message id='message' rows=10 cols=80>".htmlval($_POST['message'])."</textarea></td>
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>
".        "      $L[INPs]=action value=Submit>
".        "      $L[INPs]=action value=Preview>
".        "      $L[INPl]=mid>".moodlist($post['mood'])." 
".        "      $L[INPc]=nolayout id=nolayout value=1 ".($post['nolayout']?"checked":"")."><label for=nolayout>Disable post layout</label>
".        "    </td>
".        " </form>
".        "$L[TBLend]
";
  }elseif($act=='Submit'){
    $userto=$sql->resultq("SELECT id FROM users WHERE name LIKE '".addslashes($_POST['userto'])."' OR displayname LIKE '".addslashes($_POST['userto'])."'");

    if($userto && isset($_POST['message'])){
    if(strlen($_POST['message'])>60000){  // Protection against huge posts getting cut off
      $msg="    This post is too long. Maximum length: 60000 characters. <br>
".         "    Go back or <a href='sendprivate.php'>try again</a>";
} else {
      //[blackhole89] 2007-07-26
      $recentpms=$sql->query("SELECT date FROM pmsgs WHERE date>=(UNIX_TIMESTAMP()-30) AND userfrom='$loguser[id]'");
      $secafterpm=$sql->query("SELECT date FROM pmsgs WHERE date>=(UNIX_TIMESTAMP()-$config[secafterpost]) AND userfrom='$loguser[id]'");
    if(($sql->numrows($recentpms)>0)&&(!has_perm('consecutive-posts'))) 
    {
        $msg="You can't send more than one PM within 30 seconds!<br>
".           "Go back or <a href=sendprivate.php>try again</a>";
      } else if(($sql->numrows($secafterpm)>0)&&(has_perm('consecutive-posts'))) {
        $msg="You can't send more than one PM within $config[secafterpost] seconds!<br>
".           "Go back or <a href=sendprivate.php>try again</a>";
      } else {
          checknumeric($_POST['nolayout']);
          checknumeric($_POST['mid']);   
        $sql->query("INSERT INTO pmsgs (date,ip,userto,userfrom,unread,title,mood,nolayout) "
                   ."VALUES ('".ctime()."','$userip',$userto,$loguser[id],1,'".addslashes($_POST['title'])."',".$_POST['mid'].",".$_POST['nolayout'].")");
        $pid=$sql->insertid();
        $sql->query("INSERT INTO pmsgstext (id,text) VALUES ($pid,'".addslashes($_POST['message'])."')");
        redirect("private.php", "The private message has been sent successfully.", "Sent!", "the private message box");
      }
}
    }elseif(!$userto){
      $msg="    That user doesn't exist!<br>
".         "    Go back or <a href=sendprivate.php>try again</a>
";
    }elseif(!$_POST[message]){
      $msg="    You can't send a blank message!<br>
".         "    Go back or <a href=sendprivate.php>try again</a>
";
    }else{
      $msg="    Someone set up us the unexpected error!!<br>
".         "    Go back or <a href='sendprivate.php'>try again</a>
";
  }

  pageheader('Send private message');
    print "$top - Error";
    noticemsg("Error", $msg);

  }

  pagefooter();
?>
