<?php
  require 'function.php';

  $users=$sql->query('SELECT *,'.sqlexp().' '
                    .'FROM users u '
                    .'LEFT JOIN usersrpg r ON u.id=r.id '
                    .'WHERE lastact>'.(ctime()-15).' AND room=0 '
                    .'ORDER BY exp DESC');

  while($user=$sql->fetch($users)){
    $user[name]=str_replace(' ',' ',$user[name]);
    $st=getstats($user,$items);
    print "$user[id] $st[lvl] $user[name] ";
  }
  print '0';
?>