<?php
  require 'lib/common.php';

  function sslicon($a,$uid=0) {
    if(has_perm('view-post-ips') && $a) {
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

  print "<table cellspacing=\"0\" width=100%>
".      "  <td class=\"nb\">Online users during the last ".timeunits2($time).":</table>
".      '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">
'.       timelink(60).'|'.timelink(300).'|'.timelink(900).'|'.timelink(3600).'|'.timelink(86400)."</div>
".      "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\" width=30>#</td>
".      "    <td class=\"b h\">Name</td>
".      "    <td class=\"b h\" width=90>Last view</td>
".      "    <td class=\"b h\" width=140>Last post</td>
".(has_perm('view-user-urls')?
        "    <td class=\"b h\">URL</td>":'')."
".(has_perm('view-post-ips')?
        "    <td class=\"b h\" width=120>IP</td>":'')."
".      "    <td class=\"b h\" width=50>Posts</td>
";
  for($i=1;$user=$sql->fetch($users);$i++){
    if($user[url][0]=='!') {
      $user[url]=substr($user[url],1);
      $user[ssl]=1;
    }
    $tr = ($i % 2 ? 'n2' :'n3');
    print "<tr class=\$tr\" align=\"center\">
".        "    <td class=\"b n1\">$i.</td>
".        "    <td class=\"b\" align=\"left\">". ($user[hidden] ? '('.userlink($user).')' : userlink($user))."</td>
".        "    <td class=\"b\">".cdate($loguser[timeformat],$user[lastview])."</td>
".        "    <td class=\"b\">".($user[lastpost]?cdate($dateformat,$user[lastpost]):'-')."</td>
".(has_perm('view-user-urls')?        
          "    <td class=\"b\" align=\"left\"><span style='float:right'>".sslicon($user[ssl],$user[id])."</span".($user[url]?"><a href=$user[url]>".str_replace(array("%20", "_")," ",$user[url])."</a>":'>-').($user['ipbanned'] ? " (IP banned)":"")."</td>":'')."
".(has_perm("view-post-ips")?
          "    <td class=\"b\">".flagip($user[ip])."</td>":'')."
".        "    <td class=\"b\">$user[posts]</td>
";
  }
  print "</table>
".      "<br>
".      "<table cellspacing=\"0\" width=100%>
".      "  <td class=\"nb\">Guests:</table>
".      "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\" width=30>#</td>
".      "    <td class=\"b h\" width=70 style=\"min-width: 150px;\">User agent (Browser)</td>
".      "    <td class=\"b h\" width=70>Last view</td>
".      "    <td class=\"b h\">URL</td>
".(has_perm("view-post-ips")?
        "    <td class=\"b h\" width=120>IP</td>":'')."
";
  for($i=1;$guest=$sql->fetch($guests);$i++){
    if($guest[url][0]=='!') {
      $guest[url]=substr($guest[url],1);
      $guest[ssl]=1;
    }
    $tr = ($i % 2 ? 'n2' :'n3');
    print "<tr class=\$tr\" align=\"center\">
".        "    <td class=\"b n1\">$i.</td>
".        "    <td class=\"b\" align=\"left\"><span title=\"". htmlspecialchars($guest['useragent']) ."\" style=white-space:nowrap>". htmlspecialchars(substr($guest['useragent'], 0, 65)) ."</span></td>
".        "    <td class=\"b\">".cdate($loguser[timeformat],$guest[date])."</td>
".        "    <td class=\"b\" align=\"left\"><span style='float:right'>".sslicon($guest[ssl])."</span><a href=$guest[url]>". str_replace(array("%20", "_"), " ",  $guest[url]) ."</a>". ($guest['ipbanned'] ? " (IP banned)" : "") ."</td>
".(has_perm("view-post-ips")?
          "    <td class=\"b\">".flagip($guest[ip])."</td>":'')."
";
  }
  print "</table>
";

  
  print "</table>
".      "<br>
".      "<table cellspacing=\"0\" width=100%>
".      "  <td class=\"nb\">Bots:</table>
".      "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\" width=30>#</td>
".      "    <td class=\"b h\" width=70>Bot</td>
".      "    <td class=\"b h\" width=70>Last view</td>
".      "    <td class=\"b h\">URL</td>
".(has_perm("view-post-ips")?
        "    <td class=\"b h\" width=120>IP</td>":'')."
";
  for($i=1;$guest=$sql->fetch($bots);$i++){
    if($guest[url][0]=='!') {
      $guest[url]=substr($guest[url],1);
      $guest[ssl]=1;
    }
    $tr = ($i % 2 ? 'n2' :'n3');
    print "<tr class=\"$tr\" align=\"center\">
".        "    <td class=\"b n1\">$i.</td>
".        "    <td class=\"b\" align=\"left\"><span title=\"". htmlspecialchars($guest['useragent']) ."\" style=white-space:nowrap>". htmlspecialchars(substr($guest['useragent'], 0, 50)) ."</span></td>
".        "    <td class=\"b\">".cdate($loguser['timeformat'],$guest['date'])."</td>
".        "    <td class=\"b\" align=\"left\"><span style='float:right'>".sslicon($guest[ssl])."</span><a href=$guest[url]>$guest[url]</a>". ($guest['ipbanned'] ? " (IP banned)" : "") ."</td>
".(has_perm("view-post-ips")?
          "    <td class=\"b\">".flagip($guest['ip'])."</td>":'')."
";
  }
  print "</table>
";

  
  pagefooter();

  function timelink($timex){
    global $time;
    return ($time==$timex ? " ".timeunits2($timex)." " : " <a href=online.php?time=$timex>".timeunits2($timex).'</a> ');
  }
?>
