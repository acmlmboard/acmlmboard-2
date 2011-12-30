<?php
  require 'function.php';
  $act=$_GET[a];

  if($act=='list'){
    switch($_GET[show]){
      case 0: $where='active>-1'; break;
      case 1: $where="(lvmin=0 OR lvmin<=$logst[lvl]) "
                ."AND (lvmax=0 OR lvmax>=$logst[lvl]) "
                ."AND users<usermax "
                ."AND active=0"; break;
      case 2: $where="active=1"; break;
    }

    $rooms=$sql->query("SELECT * FROM rpgrooms WHERE $where ORDER BY id");
    while($room=$sql->fetch($rooms)){
      $room[title]=str_replace(' ',' ',$room[title]);
      print "$room[id] $room[lvmin] $room[lvmax] $room[users] $room[usermax] $room[active] $room[title] ";
    }
    print '0';
  }

  if($act=='create'){
    $title=$_GET[title];
    $lvmin=$_GET[lvmin];
    $lvmax=$_GET[lvmax];
    if(!is_numeric($lvmin)) $lvmin=0;
    if(!is_numeric($lvmax)) $lvmax=0;

    if($lvmin && $lvmin>$logst[lvl]) $lvmin=$logst[lvl];
    if($lvmax && $lvmax<$logst[lvl]) $lvmax=$logst[lvl];

    $rooms=$sql->query("SELECT id FROM rpgrooms ORDER BY id");
    $n=1;
    while($room=$sql->fetch($rooms)){
      if($room[id]>$n)
        break;
      $n++;
    }

    $sql->query("INSERT INTO rpgrooms (id, title, users, usermax, lvmin, lvmax) "
               ."VALUES ($n, \"$title\", 1, 4, $lvmin, $lvmax)");
    $sql->query("UPDATE usersrpg SET room=$n WHERE id=$uid");
    print "ok $n";
  }

  if($act=='join'){
    $id=$_GET[id];
    if(!is_numeric($id))
      die();

    $rooms=$sql->query("SELECT * FROM rpgrooms WHERE id=$id");
    $room=$sql->fetch($rooms);

    if($room && $room[active]==0 && $room[users]<$room[usermax] && (!$room[lvmin] || $room[lvmin]<=$logst[lvl]) && (!$room[lvmax] || $room[lvmax]>=$logst[lvl])){
      $numA=$sql->resultq("SELECT count(*) FROM usersrpg WHERE room=$id AND side=0");
      $numB=$sql->resultq("SELECT count(*) FROM usersrpg WHERE room=$id AND side=1");
      $side=($numA<=$numB?0:1);

      $sql->query("UPDATE rpgrooms SET users=users+1 WHERE id=$id");
      $sql->query("UPDATE usersrpg SET room=$id, side=$side, ready=0 WHERE id=$uid");
      print 'ok';
    }
  }

  if($act=='exit'){
    $id=$sql->resultq("SELECT room FROM usersrpg WHERE id=$uid");
    $sql->query("UPDATE usersrpg SET room=0 WHERE id=$uid");
    $sql->query("UPDATE rpgrooms SET users=users-1 WHERE id=$id");

    $num=$sql->resultq("SELECT users FROM rpgrooms WHERE id=$id");
    if(!$num){
      $sql->query("DELETE FROM rpgrooms WHERE id=$id");
      $sql->query("DELETE FROM rpgchat WHERE chan=$id");
    }
  }

  if($act=='online'){
    $room=$sql->fetchq("SELECT * FROM rpgrooms WHERE id=$loguser[room]");
    if(!$room[active]){
      $users=$sql->query('SELECT *,'.sqlexp().' '
                        .'FROM users u '
                        .'LEFT JOIN usersrpg r ON u.id=r.id '
                        ."WHERE room=$loguser[room] "
                        .'ORDER BY side, exp DESC');

      while($user=$sql->fetch($users)){
        $user[name]=str_replace(' ',' ',$user[name]);
        $st=getstats($user,$items);
        print "$user[id] $user[side] $user[ready] $st[lvl] $user[name] ";
      }
      print '0';
    }else
      print '-1';
  }

  if($act=='switch'){
    $sql->query("UPDATE usersrpg SET side=(side+1)%2 WHERE id=$uid AND ready=0");
  }

  if($act=='ready'){
    $x=$_GET[x];
    if(!is_numeric($x))
      die();
    $sql->query("UPDATE usersrpg SET ready=$x WHERE id=$uid");

    if($x){
      $a=$sql->resultq("SELECT count(*) FROM usersrpg WHERE room=$loguser[room] AND ready=0");
      $s=$sql->resultq("SELECT count(DISTINCT side) FROM usersrpg WHERE room=$loguser[room]");

      if(!$a && $s==2){
        $turn=0;
        $maxspd=0;
        $users=$sql->query("SELECT * FROM users u LEFT JOIN usersrpg r ON u.id=r.id WHERE room=$loguser[room]");

        while($u=$sql->fetch($users)){
          $st=getstats($u,$items);
          $sql->query("UPDATE usersrpg SET hp=$st[HP], mp=$st[MP] WHERE id=$u[id]");

          if($st[Spd]>$maxspd){
            $maxspd=$st[Spd];
            $turn=$u[id];
          }
        }

        $sql->query("UPDATE rpgrooms SET active=1, turn=$turn WHERE id=$loguser[room]");
      }
    }
  }
?>