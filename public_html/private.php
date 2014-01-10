<?php
  require 'lib/common.php';


  needs_login(1);
  $page=$_GET['page'];
  if(!$page)
    $page=1;

  $fieldlist='';
  $ufields=array('id','name','displayname','sex','group_id');
  foreach($ufields as $field)
    $fieldlist.="u.$field u$field, ";

  if($_GET[view]=='sent'){
    $tablehead='To';
    $fieldn   ='to';
    $fieldn2  ='from';
    $sent     =true;
  }else{
    $tablehead='From';
    $fieldn   ='from';
    $fieldn2  ='to';
    $sent     =false;
  }

  if(has_perm('view-user-pms')) $id=$_GET[id];
  else $id	= 0;
  checknumeric($id);

  if(!has_perm('view-own-pms') && $id == 0) {
    pageheader("Access Denied");
    no_perm();
  }

  $showdel=$_GET[showdel];
  checknumeric($showdel);

  if($_GET[action]=="del") {
    $owner=$sql->resultq("SELECT user$fieldn2 FROM pmsgs WHERE id=$id");
    if(has_perm('delete-user-pms') || ($owner==$loguser[id] && has_perm('delete-own-pms')) ) {
      $sql->query($q="UPDATE pmsgs SET del_$fieldn2=".((int)!$showdel)." WHERE id=$id");
    } else {
      pageheader('Error');
      print "$L[TBL1]>
".          "  $L[TD1c]>
".          "    You are not allowed to (un)delete that message.<br>
".          "    <a href=login.php>Login</a>
".          "$L[TBLend]
";
      pagefooter();
      die();
    }
    $id=0;
  }

  $ptitle='Private messages'.($sent?' (sent)':'');
  if($id && has_perm('view-user-pms')){
    $user=$sql->fetchq("SELECT id,name,sex,group_id FROM users WHERE id=$id");
    pageheader("$user[name]'s ".strtolower($ptitle));
    $title=userlink($user)."'s ".strtolower($ptitle);
  }else{
    $id=$loguser[id];
    pageheader($ptitle);
    $title=$ptitle;
  }

  $pmsgc=$sql->resultq("SELECT COUNT(*) FROM pmsgs WHERE user$fieldn2=$id AND del_$fieldn2=$showdel");
  $pmsgs=$sql->query("SELECT $fieldlist p.* "
                    ."FROM pmsgs p "
                    ."LEFT JOIN users u ON u.id=p.user$fieldn "
                    ."WHERE p.user$fieldn2=$id "
		    ."AND del_$fieldn2=$showdel "
                    ."ORDER BY p.unread DESC, p.date DESC "
                    ."LIMIT ".(($page-1)*$loguser[tpp]).", ".$loguser[tpp]);

  if($sent)
    $link='?'.($id!=$loguser[id]?"?id=$id&":'')."showdel=$_GET[showdel]>View received";
  else
    $link='?'.($id!=$loguser[id]?"id=$id&":'')."showdel=$_GET[showdel]&view=sent>View sent";

  if($showdel)
    $link2='?'.($id!=$loguser[id]?"?id=$id&":'')."view=$_GET[view]>View normal";
  else
    $link2='?'.($id!=$loguser[id]?"id=$id&":'')."view=$_GET[view]&showdel=1>View deleted";

  $topbot=
        "$L[TBL] width=100%>
".      "  $L[TDn]><a href=./>Main</a> - $title</td>
".      "  $L[TDnr]><a href=private.php$link2</a> | <a href=private.php$link</a> | <a href=sendprivate.php>Send new</a></td>
".      "$L[TBLend]
";

  if($pmsgc<=$loguser[tpp])
    $fpagelist='<br>';
  else{
	if ($sent) $txt = "&view=sent";
    $fpagelist='<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">Pages:';
    for($p=1;$p<=1+floor(($pmsgc-1)/$loguser[tpp]);$p++)
      if($p==$page)
        $fpagelist.=" $p";
      elseif($id!=$loguser[id])
        $fpagelist.=" <a href=private.php?id=$id&page=$p$txt>$p</a>";
      else
        $fpagelist.=" <a href=private.php?page=$p$txt>$p</a>";
    $fpagelist.='</div>';
  }

  print "$topbot
".      "<br>
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=17>&nbsp;</td>
".      "    $L[TDh] width=17>&nbsp;</td>
".      "    $L[TDh]>Title</td>
".      "    $L[TDh] width=130>$tablehead</td>
".      "    $L[TDh] width=130>Sent on</td>
";

  for($i=1;$pmsg=$sql->fetch($pmsgs);$i++){
    $status='&nbsp;';
    if($pmsg[unread])
      $status='<img src=img/status/new.png>';
    if(!$pmsg[title])
      $pmsg[title]='(untitled)';

    $tr=($i%2?'TR2':'TR3').'c';

    print "  $L[$tr]>
".        "    $L[TD2]><a href=private.php?action=del&id=$pmsg[id]&showdel=$showdel&view=$_GET[view]><img src=img/delete.png></a></td>
".        "    $L[TD1]>$status</td>
".        "    $L[TDl]><a href=showprivate.php?id=$pmsg[id]>".forcewrap(htmlval($pmsg[title]))."</a></td>
".        "    $L[TD]>".userlink($pmsg,'u')."</td>
".        "    $L[TD]><nobr>".cdate($dateformat,$pmsg['date'])."</nobr></td>
";
  }
  print "$L[TBLend]
".      "$fpagelist
".      "$topbot
";
  pagefooter();
?>
