<?php
  function checknumeric(&$var){
    if(!is_numeric($var)) {
      $var=0;
      return false;
    }
    return true;
  }

  function getforumbythread($tid){
    global $sql;
    static $cache;
    return isset($cache[$tid])?$cache[$tid]:$cache[$tid]=$sql->resultq("SELECT forum FROM threads WHERE id='$tid'");
  }

  function getcategorybyforum($fid){
    global $sql;
    static $cache;
    return isset($cache[$fid])?$cache[$fid]:$cache[$fid]=$sql->resultq("SELECT cat FROM forums WHERE id='$fid'");
  }

  function getcategorybythread($tid){
    return getcategorybyforum(getforumbythread($tid));
  }

  // Dynamic meta helpers
  function threadformeta($tid){
    global $sql;
    $fpost=$sql->fetchq("SELECT pt.text "
                      ."FROM posts p "
                      ."LEFT JOIN threads t ON t.id=p.thread "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
                      ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) " //SQL barrel roll
                      ."LEFT JOIN users u ON p.user=u.id "
                      ."WHERE p.thread=$tid AND ISNULL(pt2.id) "
		      ."GROUP BY p.id "
                      ."ORDER BY p.id "
                      ."LIMIT 1");
    $fpst=strip_tags($fpost['text']);
    if(strlen($fpst)>=152){
      $fpost=str_split($fpst,149);
      $fpst=$fpost[0]."...";
    }
    return $fpst;
  }
  function postformeta($pid){
    global $sql;
    $fpost=$sql->fetchq("SELECT pt.text "
                      ."FROM posts p "
                      ."LEFT JOIN threads t ON t.id=p.thread "
                      ."LEFT JOIN poststext pt ON p.id=pt.id "
                      ."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) " //SQL barrel roll
                      ."LEFT JOIN users u ON p.user=u.id "
                      ."WHERE p.id=$pid AND ISNULL(pt2.id) "
		      ."GROUP BY p.id "
                      ."ORDER BY p.id "
                      ."LIMIT 1");
    $fpst=strip_tags($fpost['text']);
    if(strlen($fpst)>=152){
      $fpost=str_split($fpst,149);
      $fpst=$fpost[0]."...";
    }
    return $fpst;
  }
?>