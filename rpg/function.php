<?php
  require '../lib/function.php';
  print '°';

  if($n)
    $uid=checkuser($n,md5($p));
  elseif(is_numeric($u))
    $uid=checkuid($u,md5($p));

  if(!$uid)
    die();

  $sql->query("UPDATE usersrpg "
             ."SET lastact=".ctime()." "
             ."WHERE id=$uid");

  $items  =getitems();
  $loguser=getuser($uid);
  $logst  =getstats($loguser,$items);

  function getitems(){
    global $sql;
    $it=$sql->query('SELECT * FROM items');
    while($item=$sql->fetch($it))
      $items[$item[id]]=$item;
    return $items;
  }

  function getuser($id){
    global $sql;
    $user=$sql->fetchq('SELECT *, '.sqlexp().' '
                      .'FROM users u '
                      .'LEFT JOIN usersrpg r ON u.id=r.id '
                      ."WHERE u.id=$id");
    $r=$sql->query('SELECT '.sqlexp().' '
                  .'FROM users '
                  ."HAVING floor(exp)>$user[exp]");
    $user[rank]=@mysql_num_rows($r)+1;
    
    $user[name]=str_replace(' ',' ',$user[name]);
    return $user;
  }
?>
