<?php

  function editthread($id,$title='',$forum=0,$icon='',$closed=-1,$sticky=-1,$delete=-1){
    global $sql;

    if($delete<1){
      $set='';
      if($title!='') $set.=",title=\"$title\"";
      if($icon!='')  $set.=",icon=$icon";
      if($closed>=0) $set.=",closed=$closed";
      if($sticky>=0) $set.=",sticky=$sticky";
      $set[0]=' ';
      if(strlen(trim($set))>0&&!is_array($set)) $sql->query("UPDATE threads SET $set WHERE id=$id");

      if($forum)
        movethread($id,$forum);
    }else{
    }
  }

  function movethread($id,$forum){
    global $sql;

    if(!$sql->resultq("SELECT COUNT(*) FROM forums WHERE id=$forum")) return;

    $thread=$sql->fetchq("SELECT forum,replies FROM threads WHERE id=$id");
    $sql->query("UPDATE threads SET forum=$forum WHERE id=$id");

    $last1=$sql->fetchq("SELECT lastdate,lastuser,lastid "
                       ."FROM threads "
                       ."WHERE forum=$thread[forum] "
                       ."ORDER BY lastdate DESC LIMIT 1");
    $last2=$sql->fetchq("SELECT lastdate,lastuser,lastid "
                       ."FROM threads "
                       ."WHERE forum=$forum "
                       ."ORDER BY lastdate DESC LIMIT 1");
    if($last1)
      $sql->query("UPDATE forums "
                ."SET posts=posts-($thread[replies]+1), "
                .    "threads=threads-1, "
                .    "lastdate=$last1[lastdate], "
                .    "lastuser=$last1[lastuser], "
				.    "lastid=$last1[lastid] "
                ."WHERE id=$thread[forum]");
    if($last2)
      $sql->query("UPDATE forums "
                 ."SET posts=posts+($thread[replies]+1), "
                 .    "threads=threads+1, "
                 .    "lastdate=$last2[lastdate], "
                 .    "lastuser=$last2[lastuser], "
				 .    "lastid=$last2[lastid] "
                 ."WHERE id=$forum");
  }
?>