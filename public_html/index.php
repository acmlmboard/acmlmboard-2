<?php
  /* index.php ****************************************
    Changelog
    Xkeeper     fixed what blackhole89 broke, which was mostly nothing
    blackhole89 moved mark forum /all forums read here
    blackhole89 added consideration of minpower for forum/category display
    Xkeeper     added support for category ordering
  ****************************************************/


  if($p=$_GET[p]) return header("Location:thread.php?pid=$p#$p");
  if($t=$_GET[t]) return header("Location:thread.php?id=$t");
  if($u=$_GET[u]) return header("Location:profile.php?id=$u");
  if(isset($_GET['a'])) {
    $a=$_GET['a'];
    return header("Location:thread.php?announce=$a");
  }
  $showonusers=1;
  require 'lib/common.php';

  //mark forum read
  if($log && $_GET[action]=='markread'){
    if($fid!='all'){
      checknumeric($fid);
      //delete obsolete threadsread entries
      $sql->query("DELETE r "
                 ."FROM threadsread r "
                 ."LEFT JOIN threads t ON t.id=r.tid "
                 ."WHERE t.forum=$fid "
                 ."AND r.uid=$loguser[id]");
      //add new forumsread entry
      $sql->query("REPLACE INTO forumsread VALUES ($loguser[id],$fid,".ctime().')');
    } else {
      //mark all read
      $sql->query("DELETE FROM threadsread WHERE uid=$loguser[id]");
      $sql->query("REPLACE INTO forumsread (uid,fid,time) SELECT $loguser[id],f.id,".ctime()." FROM forums f");
    }

    // remove nasty GET strings so that refreshers like me don't mark things read over and over and burp
    header('Location: index.php');
  }
       
  // Moved pageheader here so that we can do header()s without fucking everything up again
  pageheader();






  $categs=$sql->query("SELECT * "
                     ."FROM categories "
//                     ."WHERE minpower <= ". ($loguser['power'] < 0 ? 0 : $loguser['power']) ." "
                     ."ORDER BY ord");
  while($c=$sql->fetch($categs)) {
    if (can_view_cat($c['id'])) $categ[$c[id]]=$c;
  }

	//[KAWA] ABXD does ignores with a very nice SQL trick that I think Mega-Mario came up with one day.
	//Unfortunately, this place is too hairy to add the trick to so I'll have to use a third query to collect the ignores. The first is categories. The second is the forum list itself.
	$ignores = array();
	$ignoreQ = $sql->query("SELECT * FROM ignoredforums WHERE uid = ".$loguser['id']);
	while($i = $sql->fetch($ignoreQ))
		$ignores[$i['fid']] = true;

  $forums=$sql->query("SELECT f.*".($log?", r.time rtime":'').", u.id uid, u.name uname, u.displayname udisplayname, u.sex usex, u.power upower "
                     ."FROM forums f "
                     ."LEFT JOIN users u ON u.id=f.lastuser "
                     ."LEFT JOIN categories c ON c.id=f.cat "
               .($log?"LEFT JOIN forumsread r ON r.fid=f.id AND r.uid=$loguser[id] ":'')
//                     ."WHERE f.minpower<=". ($loguser['power'] < 0 ? 0 : $loguser['power']) ." "
//                     .  "AND c.minpower<=". ($loguser['power'] < 0 ? 0 : $loguser['power']) ." "
                     ." WHERE announce=0 "
                     ."ORDER BY c.ord,ord");
  $cat=-1;
print "
".      "$L[TBL1]>";

echo announcement_row(0,2,3);

echo
      "  $L[TRh]>
".      "    $L[TDh] width=17>&nbsp;</td>
".      "    $L[TDh]>Forum</td>
".      "    $L[TDh] width=50>Threads</td>
".      "    $L[TDh] width=50>Posts</td>
".      "    $L[TDh] width=150>Last post</td>
";

  while($forum=$sql->fetch($forums)){
    if (!can_view_forum($forum['id'])) continue;

    if($forum[cat]!=$cat){
      $cat=$forum[cat];
        print "  $L[TRg]>
".            "    $L[TD] colspan=5>".($categ[$cat]['private']?('('.($categ[$cat][title]).')'):($categ[$cat][title]))."</td>
";
    }

    if($forum[posts]>0 && $forum[lastdate]>0)
      $lastpost='<nobr>'.cdate($dateformat,$forum[lastdate]).'</nobr><br><font class=sfont>by&nbsp;'.userlink($forum,'u')."&nbsp;<a href='thread.php?pid=$forum[lastid]#$forum[lastid]'>&raquo;</a></font>";
    else
      $lastpost='None';

    if($forum[lastdate]>($log?$forum[rtime]:ctime()-3600)){
      $thucount = $sql->resultq("SELECT count(*) FROM threadsread r LEFT JOIN threads t ON r.tid=t.id WHERE t.forum=$forum[id] AND r.time > $forum[rtime] ");
      $status="<img src=gfx/new.php?type=n&num=$thucount>";
    }
    else
      $status='&nbsp;';

	if($ignores[$forum['id']])
	{
		$status = "&nbsp;";
		$ignoreFX = "style=\"opacity: 0.5;\"";
	}
	else
		$ignoreFX = "";

    $modstring="";
    $a=$sql->query("SELECT u.name,u.displayname,u.id,u.sex,u.power FROM forummods f, users u WHERE f.fid=$forum[id] AND u.id=f.uid");
    while($mod=$sql->fetch($a)) $modstring.=userlink($mod).", ";
    if($modstring) $modstring="<br>(moderated by: ".substr($modstring,0,-2).")";
//    else $modstring="<p>&nbsp;</p>";
    print
        "  $L[TRc]>
".      "    $L[TD1]>$status</td>
".      "    $L[TD2l]>
".      "      ".($forum['private']?'(':'')."<a href=forum.php?id=$forum[id] $ignoreFX>$forum[title]</a>".($forum['private']?')':'')."<br>
".      "      <font class=sfont $ignoreFX>". str_replace("%%%SPATULANDOM%%%", $spatulas[$spaturand], $forum[descr]) ."$modstring</font>
".      "    </td>
".      "    $L[TD1]>$forum[threads]</td>
".      "    $L[TD1]>$forum[posts]</td>
".      "    $L[TD2]>$lastpost</td>
";
  }
  print "$L[TBLend]
";
  pagefooter();
?>
