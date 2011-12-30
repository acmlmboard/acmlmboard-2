<?php
  require 'lib/common.php';

  if($id=$_GET[id])
    checknumeric($id);
  else $id=0;

  if($page=$_GET[page])
    checknumeric($page);
  else $page=1;
  
  if($page<1) $page=1;

  if($id) {
    $user=$sql->fetchq("SELECT id,name FROM users WHERE id=$id");
    if($user[id]) {
      
      $print="<a href=./>Main</a> - Posts by user $user[name]<br><br>
".           "$L[TBL1]>
".           "  $L[TRh]>
".           "    $L[TDh]>ID
".           "    $L[TDh]>Num.
".           "    $L[TDh]>Posted on
".           "    $L[TDh]>Thread title
".           "  </tr>";

      $numposts=$sql->fetchq("SELECT COUNT(*) c FROM posts WHERE user=$id");
      $numposts=$numposts[c];

      $p=$sql->query("SELECT p.id,p.num,p.date,t.title,f.minpower FROM (posts p LEFT JOIN threads t ON t.id=p.thread) "
                    ."LEFT JOIN forums f ON f.id=t.forum WHERE p.user=$id "
                    ."ORDER BY p.num DESC LIMIT ".(($page-1)*$loguser[tpp]).",".$loguser[tpp]);

      $i=0;
      while($post=$sql->fetch($p)) {
        if($post[minpower]>$loguser[power]) $tlink="<i>(Restricted forum)</i>";
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

  pageheader("Posts by user $user[name]");
  echo $print.$fpagelist."<br>";
  pagefooter();
?>
