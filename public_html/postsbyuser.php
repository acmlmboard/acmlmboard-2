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
    $user=$sql->fetchq("SELECT id,name,displayname FROM users WHERE id=$id");
    if($user[id]) {
      
      $print="<a href=./>Main</a> - Posts by user ".($user[displayname]?$user[displayname]:$user[name])."<br><br>
".           "$L[TBL1]>
".           "  $L[TRh]>
".           "    $L[TDh]>ID
".           "    $L[TDh]>Num.
".           "    $L[TDh]>Posted on
".           "    $L[TDh]>Thread title
".           "  </tr>";

      $numposts=$sql->fetchq("SELECT COUNT(*) c FROM posts WHERE user=$id");
      $numposts=$numposts[c];

      $p=$sql->query("SELECT p.id,p.num,p.date,t.title,f.private FROM (posts p LEFT JOIN threads t ON t.id=p.thread) "
                    ."LEFT JOIN forums f ON f.id=t.forum WHERE p.user=$id "
                    ."ORDER BY p.num DESC LIMIT ".(($page-1)*$loguser[tpp]).",".$loguser[tpp]);

      $i=0;
      while($post=$sql->fetch($p)) {
        if(!(can_view_forum($post))) $tlink="<i>(Restricted forum)</i>";
        else $tlink="<a href=thread.php?pid=$post[id]#$post[id]>$post[title]</a>";
        $print.=" ".(($i=!$i)?$L[TR3]:$L[TR2]).">
".              "  $L[TDc]>$post[id]
".              "  $L[TDc]>#$post[num]
".              "  $L[TDc]>".cdate($dateformat,$post[date])."
".              "  $L[TD]>$tlink
".              "</tr>";
      }
      $print.="$L[TBLend]";

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
      $print="$L[TBL1]>
".           "  $L[TR2]>
".           "    $L[TD1c]>
".           "      This user does not exist.
".           "$L[TBLend]";
    }
  } else {
    $print="$L[TBL1]>
".         "  $L[TR2]>
".         "    $L[TD1c]>
".         "      You must specify a user ID.
".         "$L[TBLend]";
  }

  //This is heavily based off of AB1's code so posts by thread and posts by forum need to be cleaned at some point.
  if(isset($_GET[postsbythread])) {
  $time=$_GET[time];
if(!$time) $time=86400;
  $posters=$sql->query("SELECT t.id,t.replies,t.title,t.forum,f.private,COUNT(p.id) cnt FROM threads t,posts p,forums f WHERE p.user=$id AND p.thread=t.id AND p.date>".(ctime()-$time).' AND t.forum=f.id GROUP BY t.id ORDER BY cnt DESC');
  $u=$sql->fetchq("SELECT name FROM users WHERE id=$id");
  $username=$u[name];
  $lnk="<a href=postsbyuser.php?postsbythread&id=$id&time";
  if($time<999999999) $during=' during the last '.timeunits2($time);
  $print= "$lnk=3600>1 hour</a> |
	$lnk=86400>1 day</a> |
	$lnk=604800>7 days</a> |
	$lnk=2592000>30 days</a> | 
	$lnk=999999999>Total</a><br>
	Posts by $username in threads$during:
".           "$L[TBL1]>
".           "  $L[TRh]>
".	"$L[TDh]>#
".	"$L[TDh]>Thread
".	"$L[TDh]>Posts
".	"$L[TDh]>Thread total
".      "  </tr>
  ";
  for($i=1;$t=$sql->fetch($posters);$i++){
    $print.= "
	<tr>
".	"$L[TDc]>$i</td>
".	"$L[TD] align=left>
    ";
    if(!(can_view_forum($t)))
	$print.= "<i>(Restricted forum)</i>";
    else $print.= "<a href=thread.php?id=$t[id]>$t[title]</a>";
    $print.= "
	</td>
".	"$L[TDc]>$t[cnt]</td>
".     "$L[TDc]>".($t[replies]+1)."</td>
".     "  </tr>
    ";
  }
      $print.="$L[TBLend]";
            $fpagelist="";
  }
  
  if(isset($_GET[postsbyforum])) {
  $time=$_GET[time];
  if(!$time) $time=86400;
  if($id){
    $useridquery="posts.user=$id AND";
    $by='by ';
    $u=$sql->fetchq("SELECT name FROM users WHERE id=$id");
    $username=$u[name];
  }
  $posters=$sql->query("SELECT forums.*,COUNT(posts.id) AS cnt FROM forums,threads,posts WHERE $useridquery posts.thread=threads.id AND threads.forum=forums.id AND posts.date>".(ctime()-$time).' AND threads.announce=0 GROUP BY forums.id ORDER BY cnt DESC');
  $userposts=$sql->query("SELECT id FROM posts WHERE $useridquery date>".(ctime()-$time).'');
  $lnk="<a href=postsbyuser.php?postsbyforum&id=$id&time";
  if($time<999999999) $during=' during the last '.timeunits2($time);
  $print= "$lnk=3600>1 hour</a> |
	$lnk=86400>1 day</a> |
	$lnk=604800>7 days</a> |
	$lnk=2592000>30 days</a> | 
	$lnk=999999999>Total</a><br>
	Posts $by$username in forums$during:
".           "$L[TBL1]>
".           "  $L[TRh]>
".	"$L[TDh]>#
".	"$L[TDh]>Forum
".	"$L[TDh]>Posts
".	"$L[TDh]>Forum total
".      "  </tr>
  ";
  for($i=1;$f=$sql->fetch($posters);$i++){
      if($i>1) $print.= '<tr>';
	if(!(can_view_forum($f))) $link="<i>(Restricted forum)</i>";
	else $link="<a href=forum.php?id=$f[id]>$f[title]</a>";
      $print.= "
".	"$L[TDc]>$i</td>
".	"$L[TD]>$link</td>
".	"$L[TDc]>$f[cnt]</td>
".	"$L[TDc]>$f[posts]</td>
".      "  </tr>
      ";
  }
      $print.="$L[TBLend]";
            $fpagelist="";
  }

  if(isset($_GET[postsbyforum])) {
  if($id) pageheader("Posts in forums by user $user[name]");
  else pageheader("Posts in forums on the board");
  }
  else if(isset($_GET[postsbythread])) {
  pageheader("Posts in threads by user $user[name]");
  }
  else {
  pageheader("Posts by user $user[name]");
  }
  echo $print.$fpagelist."<br>";
  pagefooter();
?>