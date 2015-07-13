<?php
  require 'lib/common.php';

  if($id=$_GET[id])
    checknumeric($id);
  else $id=0;

  if($page=$_GET[page])
    checknumeric($page);
  else $page=1;
  
  if($page<1) $page=1;

  else if($id) {
    $user=$sql->fetchq("SELECT ".userfields()." FROM users WHERE id=$id");
    if($user[id]) {
      
      $print="<a href=./>Main</a> - Posts by user ".($user[displayname]?$user[displayname]:$user[name])."<br><br>
".           "<table cellspacing=\"0\" class=\"c1\">
".           "  <tr class=\"h\">
".           "    <td class=\"b h\">ID
".           "    <td class=\"b h\">Num.
".           "    <td class=\"b h\">Posted on
".           "    <td class=\"b h\">Thread title
".           "  </tr>";

      $numposts=$sql->fetchq("SELECT COUNT(*) c FROM posts WHERE user=$id");
      $numposts=$numposts[c];

      $p=$sql->query("SELECT p.id pid,p.num,p.date,t.title,t.forum,t.announce,f.id,f.private FROM (posts p LEFT JOIN threads t ON t.id=p.thread) "
                    ."LEFT JOIN forums f ON f.id=t.forum WHERE p.user=$id "
                    ."ORDER BY p.num DESC LIMIT ".(($page-1)*$loguser[tpp]).",".$loguser[tpp]);

      $i=0;
      while($post=$sql->fetch($p)) {
        if(!(can_view_forum($post))) $tlink="<i>(Restricted forum)</i>";
        else $tlink="<a href=thread.php?pid=$post[pid]#$post[pid]>$post[title]</a>";
        $print.="<tr class=\"".(($i=!$i)?"n3":"n2").">
".              "  <td class=\"b\" align=\"center\">$post[pid]
".              "  <td class=\"b\" align=\"center\">#$post[num]
".              "  <td class=\"b\" align=\"center\">".cdate($dateformat,$post[date])."
".              "  <td class=\"b\">$tlink
".              "</tr>";
      }
      $print.="</table>";

      if($numposts<=$loguser[tpp])
        $fpagelist='<br>';
      else{
        $fpagelist='Pages:';
        for($p=1;$p<=1+floor(($numposts-1)/$loguser[tpp]);$p++)
          if($p==$page)
            $fpagelist.=" $p";
          else
            $fpagelist.=" <a href=postsbyuser.php?id=$id&page=$p>$p</a>";
      }

    } else {
      $print="<table cellspacing=\"0\" class=\"c1\">
".           "  <tr class=\"n2\">
".           "    <td class=\"b n1\" align=\"center\">
".           "      This user does not exist.
".           "</table>";
    }
  } else {
    $print="<table cellspacing=\"0\" class=\"c1\">
".         "  <tr class=\"n2\">
".         "    <td class=\"b n1\" align=\"center\">
".         "      You must specify a user ID.
".         "</table>";
  }

  //This is heavily based off of AB1's code so posts by thread and posts by forum need to be cleaned at some point.
  if(isset($_GET[postsbythread])) {
  $time=$_GET[time];
if(!$time) $time=86400;
  $posters=$sql->query("SELECT t.id,t.replies,t.title,t.forum,f.id,f.private,COUNT(p.id) cnt FROM threads t,posts p,forums f WHERE p.user=$id AND p.thread=t.id AND p.date>".(ctime()-$time).' AND t.forum=f.id GROUP BY t.id ORDER BY cnt DESC');
  $u=$sql->fetchq("SELECT ".userfields()." FROM users WHERE id=$id");
  $username=($u[displayname]?$u[displayname]:$u[name]);
  if($time<999999999) $during=' during the last '.timeunits2($time);
  $print= "Posts by $username in threads$during:
".      "<br>
".       timelink1(3600).'|'.timelink1(86400).'|'.timelink1(604800).'|'.timelink1(2592000)."
".           "<table cellspacing=\"0\" class=\"c1\">
".           "  <tr class=\"h\">
".	"<td class=\"b h\">#
".	"<td class=\"b h\">Thread
".	"<td class=\"b h\">Posts
".	"<td class=\"b h\">Thread total
".      "  </tr>
  ";
  for($i=1;$t=$sql->fetch($posters);$i++){
    $print.= "
	<tr>
".	"<td class=\"b\" align=\"center\">$i</td>
".	"<td class=\"b\" align=left>
    ";
    if(!(can_view_forum($t)))
	$print.= "<i>(Restricted forum)</i>";
    else $print.= "<a href=thread.php?id=$t[id]>$t[title]</a>";
    $print.= "
	</td>
".	"<td class=\"b\" align=\"center\">$t[cnt]</td>
".     "<td class=\"b\" align=\"center\">".($t[replies]+1)."</td>
".     "  </tr>
    ";
  }
      $print.="</table>";
            $fpagelist="";
  }
  
  if(isset($_GET[postsbyforum])) {
  $time=$_GET[time];
  if(!$time) $time=86400;
  if($id){
    $useridquery="posts.user=$id AND";
    $by='by ';
    $u=$sql->fetchq("SELECT ".userfields()." FROM users WHERE id=$id");
    $username=($u[displayname]?$u[displayname]:$u[name]);
  }
  $posters=$sql->query("SELECT forums.*,COUNT(posts.id) AS cnt FROM forums,threads,posts WHERE $useridquery posts.thread=threads.id AND threads.forum=forums.id AND posts.date>".(ctime()-$time).' AND threads.announce=0 GROUP BY forums.id ORDER BY cnt DESC');
  $userposts=$sql->query("SELECT id FROM posts WHERE $useridquery date>".(ctime()-$time).'');
  if($time<999999999) $during=' during the last '.timeunits2($time);
  $print= "Posts $by$username in forums$during:
".      "<br>
".       timelink2(3600).'|'.timelink2(86400).'|'.timelink2(604800).'|'.timelink2(2592000)."
".           "<table cellspacing=\"0\" class=\"c1\">
".           "  <tr class=\"h\">
".	"<td class=\"b h\">#
".	"<td class=\"b h\">Forum
".	"<td class=\"b h\">Posts
".	"<td class=\"b h\">Forum total
".      "  </tr>
  ";
  for($i=1;$f=$sql->fetch($posters);$i++){
      if($i>1) $print.= '<tr>';
	if(!(can_view_forum($f))) $link="<i>(Restricted forum)</i>";
	else $link="<a href=forum.php?id=$f[id]>$f[title]</a>";
      $print.= "
".	"<td class=\"b\" align=\"center\">$i</td>
".	"<td class=\"b\">$link</td>
".	"<td class=\"b\" align=\"center\">$f[cnt]</td>
".	"<td class=\"b\" align=\"center\">$f[posts]</td>
".      "  </tr>
      ";
  }
      $print.="</table>";
            $fpagelist="";
  }
  
  if(isset($_GET[postsbytime])) {
  $posttime=$_GET[time];
  if(!$posttime) $posttime=86400;
  $time=ctime()-$posttime;
  if($id){
    $user=$sql->fetchq("SELECT ".userfields()." FROM users WHERE id=$id");
    $from=" from ".($user[displayname]?$user[displayname]:$user[name]);
  }else $from=' on the board';
  $posts=$sql->query("SELECT count(*) AS cnt, FROM_UNIXTIME(date,'%k') AS hour FROM posts WHERE ".($id?"user=$id AND ":'')."date>$time GROUP BY hour");
  if($posttime<999999999) $during=' during the last '.timeunits2($posttime);
  $print= "Posts$from by time of day$during:
".      "<br>
".       timelink3(3600).'|'.timelink3(86400).'|'.timelink3(604800).'|'.timelink3(2592000)."
".           "<table cellspacing=\"0\" class=\"c1\">
".           "  <tr class=\"h\">
".	"<td class=\"b h\" width=40>Hour
".	"<td class=\"b h\" width=50>Posts
".	"<td class=\"b h\">&nbsp<tr>";
  for($i=0;$i<24;$i++) $postshour[$i]=0;
  while($h=$sql->fetch($posts)) $postshour[$h[hour]]=$h[cnt];
  for($i=0;$i<24;$i++) if($postshour[$i]>$max) $max=$postshour[$i];
  for($i=0;$i<24;$i++){
    if($i) $print.= '<tr>';
    $bar="<img src=gfx/rpg/bar-on.png width=".(@floor($postshour[$i]/$max*10000)/100).'% height=8>';
    $print.= "
".	"<td class=\"b n2\">$i</td>
".	"<td class=\"b n2\">$postshour[$i]</td>
".	"<td class=\"b n2\" width=100%>$bar</td>
    ";
  }
      $print.="</table>";
            $fpagelist="";
    }

  if(isset($_GET[postsbyforum])) {
  if($id) pageheader("Posts in forums by user $user[name]");
  else pageheader("Posts in forums on the board");
  }
  else if(isset($_GET[postsbythread])) {
  pageheader("Posts in threads by user $user[name]");
  }
  else if(isset($_GET[postsbytime])) {
  if($id) pageheader("Posts by time of day from user $user[name]");
  else pageheader("Posts by time of day on the board");
  }
  else {
  pageheader("Posts by user $user[name]");
  }
  echo $print.$fpagelist."<br>";
  pagefooter();
  
 function timelink1($timex){
    global $time,$id;
    return ($time==$timex ? " ".timeunits2($timex)." " : " <a href=postsbyuser.php?postsbythread&id=$id&time=$timex>".timeunits2($timex).'</a> ');
  }
  function timelink2($timex){
    global $time,$id;
    return ($time==$timex ? " ".timeunits2($timex)." " : " <a href=postsbyuser.php?postsbyforum&id=$id&time=$timex>".timeunits2($timex).'</a> ');
  }
  function timelink3($timex){
    global $posttime,$id;
    return ($posttime==$timex ? " ".timeunits2($timex)." " : " <a href=postsbyuser.php?postsbytime&id=$id&time=$timex>".timeunits2($timex).'</a> ');
  }
?>