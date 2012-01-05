<?php
  require 'lib/common.php';

  function sslicon($a,$uid=0) {
    if(acl_for_user($uid,"show-ips") && $a) {
      return "<img src='img/ssloff.gif'>";
    }
    return "";
  }

  pageheader('Online users');

  $time=$_GET[time];
  checknumeric($time);

  if(!$time)
    $time=300;


  $hiddencheck  = "AND hidden=0 ";
  if (has_perm('view-hidden-users')) {
    $hiddencheck = "";
  }

  $users=$sql->query("SELECT * FROM users "
                    ."WHERE lastview>".(ctime()-$time)." $hiddencheck"
                    ."ORDER BY lastview DESC");
  $guests=$sql->query("SELECT g.* FROM guests g "
                    ."WHERE g.date>".(ctime()-$time)." "
                    ."AND g.bot=0 "
                    ."ORDER BY g.date DESC");
  $bots=$sql->query("SELECT * FROM guests "
                    ."WHERE date>".(ctime()-$time)." "
                    ."AND bot=1 "
                    ."ORDER BY date DESC");

  print "$L[TBL] width=100%>
".      "  $L[TDn]>Online users during the last ".timeunits2($time).":$L[TBLend]
".      '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">
'.       timelink(60).'|'.timelink(300).'|'.timelink(900).'|'.timelink(3600).'|'.timelink(86400)."</div>
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=30>#</td>
".      "    $L[TDh]>Name</td>
".      "    $L[TDh] width=90>Last view</td>
".      "    $L[TDh] width=140>Last post</td>
".(has_perm('view-user-urls')?
        "    $L[TDh]>URL</td>":'')."
".(has_perm('view-post-ips')?
        "    $L[TDh] width=120>IP</td>":'')."
".      "    $L[TDh] width=50>Posts</td>
";
  for($i=1;$user=$sql->fetch($users);$i++){
    if($user[url][0]=='!') {
      $user[url]=substr($user[url],1);
      $user[ssl]=1;
    }
    $tr=($i%2?'TR2':'TR3').'c';
    print "  $L[$tr]>
".        "    $L[TD1]>$i.</td>
".        "    $L[TDl]>". ($user[hidden] ? '('.userlink($user).')' : userlink($user))."</td>
".        "    $L[TD]>".cdate($loguser[timeformat],$user[lastview])."</td>
".        "    $L[TD]>".($user[lastpost]?cdate($dateformat,$user[lastpost]):'-')."</td>
".(has_perm('view-user-urls')?        
          "    $L[TDl]><span style='float:right'>".sslicon($user[ssl],$user[id])."</span".($user[url]?"><a href=$user[url]>".str_replace(array("%20", "_")," ",$user[url])."</a>":'>-').($user['ipbanned'] ? " (IP banned)":"")."</td>":'')."
".(has_perm("view-post-ips")?
          "    $L[TD]>".flagip($user[ip])."</td>":'')."
".        "    $L[TD]>$user[posts]</td>
";
  }
  print "$L[TBLend]
".      "<br>
".      "$L[TBL] width=100%>
".      "  $L[TDn]>Guests:$L[TBLend]
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=30>#</td>
".      "    $L[TDh] width=70>User agent (Browser)</td>
".      "    $L[TDh] width=70>Last view</td>
".      "    $L[TDh]>URL</td>
".      "    $L[TDh] width=120>IP</td>
";
  for($i=1;$guest=$sql->fetch($guests);$i++){
    if($guest[url][0]=='!') {
      $guest[url]=substr($guest[url],1);
      $guest[ssl]=1;
    }
    $tr=($i%2?'TR2':'TR3').'c';
    print "  $L[$tr]>
".        "    $L[TD1]>$i.</td>
".        "    $L[TDl]><span title=\"". htmlspecialchars($guest['useragent']) ."\" style=white-space:nowrap>". htmlspecialchars(substr($guest['useragent'], 0, 65)) ."</span></td>
".        "    $L[TD]>".cdate($loguser[timeformat],$guest[date])."</td>
".        "    $L[TDl]><span style='float:right'>".sslicon($guest[ssl])."</span><a href=$guest[url]>". str_replace(array("%20", "_"), " ",  $guest[url]) ."</a>". ($guest['ipbanned'] ? " (IP banned)" : "") ."</td>
".        "    $L[TD]>".flagip($guest[ip])."</td>
";
  }
  print "$L[TBLend]
";

  
  print "$L[TBLend]
".      "<br>
".      "$L[TBL] width=100%>
".      "  $L[TDn]>Bots:$L[TBLend]
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=30>#</td>
".      "    $L[TDh] width=70>Bot</td>
".      "    $L[TDh] width=70>Last view</td>
".      "    $L[TDh]>URL</td>
".      "    $L[TDh] width=120>IP</td>
";
  for($i=1;$guest=$sql->fetch($bots);$i++){
    if($guest[url][0]=='!') {
      $guest[url]=substr($guest[url],1);
      $guest[ssl]=1;
    }
    $tr=($i%2?'TR2':'TR3').'c';
    print "  $L[$tr]>
".        "    $L[TD1]>$i.</td>
".        "    $L[TDl]><span title=\"". htmlspecialchars($guest['useragent']) ."\" style=white-space:nowrap>". htmlspecialchars(substr($guest['useragent'], 0, 50)) ."</span></td>
".        "    $L[TD]>".cdate($loguser[timeformat],$guest[date])."</td>
".        "    $L[TDl]><span style='float:right'>".sslicon($guest[ssl])."</span><a href=$guest[url]>$guest[url]</a>". ($guest['ipbanned'] ? " (IP banned)" : "") ."</td>
".        "    $L[TD]>$guest[ip]</td>
";
  }
  print "$L[TBLend]
";

  
  pagefooter();

  function timelink($time){
    return " <a href=online.php?time=$time>".timeunits2($time).'</a> ';
  }
?>
