<?php
  require 'function.php';
  $act=$_GET[a];

  if($act=='attack'){
    $room=$sql->fetchq("SELECT * FROM rpgrooms WHERE id=$loguser[room]");
    if($room[turn]!=$uid)
      die();

    $users=$sql->query("SELECT * FROM usersrpg "
                      ."WHERE room=$loguser[room] AND side!=$loguser[side] AND hp>0");
    $r=rand(0,mysql_num_rows($users)-1);
    while($u=$sql->fetch($users)){
      $r--;
      if($r<0){
        $t=$u[id];
        break;
      }
    }
    $st=getstats($u,$items);

    $at=$logst[Atk];
    $df=$st[Def];
    $dmg=max(0,$at-$df)*0.5+($at?pow(0.3,pow($df/$at,0.9)):0)*$at*0.5;
    $dmg=max(1,rand($dmg*0.6,$dmg));

    $sql->query("UPDATE usersrpg SET hp=hp-$dmg WHERE id=$t");
    $sql->query("UPDATE usersrpg SET hp=0 WHERE id=$t AND hp<0");

    print "1 ";
    print "0 $t $dmg ";

    $h=$sql->resultq("SELECT sum(hp) FROM usersrpg WHERE room=$loguser[room] AND side!=$loguser[side]");
    if($h>0){
      $turn1=0;
      $turn2=0;
      $maxspd1=0;
      $maxspd2=0;
      $users=$sql->query("SELECT * FROM users u LEFT JOIN usersrpg r ON u.id=r.id WHERE room=$loguser[room] AND hp>0 AND u.id!=$uid");

      while($u=$sql->fetch($users)){
        $st=getstats($u,$items);
        if($st[Spd]>$maxspd1 || ($st[Spd]==$maxspd1 && $u[id]<$turn1)){
          $maxspd1=$st[Spd];
          $turn1=$u[id];
        }
        if(($st[Spd]>$maxspd2 || ($st[Spd]==$maxspd2 && $u[id]<$turn2))
        && ($st[Spd]<$logst[Spd] || ($st[Spd]==$logst[Spd] && $u[id]<$uid))){
          $maxspd2=$st[Spd];
          $turn2=$u[id];
        }
      }
      if($turn2)
        $turn=$turn2;
      else
        $turn=$turn1;

      $sql->query("UPDATE rpgrooms SET turn=$turn WHERE id=$loguser[room]");

    }else{
      $sql->query("UPDATE usersrpg SET gcoins=gcoins+100 WHERE room=$loguser[room] AND side=$loguser[side]");
      $sql->query("UPDATE usersrpg SET hp=-1, mp=-1, ready=0 WHERE room=$loguser[room]");
      $sql->query("UPDATE rpgrooms SET active=0, turn=0 WHERE id=$loguser[room]");
    }


    $act='update';
  }

  if($act=='update'){
    $room=$sql->fetchq("SELECT * FROM rpgrooms WHERE id=$loguser[room]");

    $users=$sql->query('SELECT *,'.sqlexp().' '
                      .'FROM users u '
                      .'LEFT JOIN usersrpg r ON u.id=r.id '
                      ."WHERE room=$loguser[room] "
                      .'ORDER BY side, exp DESC');

    print "$room[turn] ";

    while($user=$sql->fetch($users)){
      $user[name]=str_replace(' ',' ',$user[name]);
      $st=getstats($user,$items);

      if($user[hp]<0) $user[hp]=$st[HP];
      if($user[mp]<0) $user[mp]=$st[MP];

      $hp=ceil(1000*$user[hp]/$st[HP]);
      $mp=ceil(1000*$user[mp]/$st[MP]);
      print "$user[id] $user[side] $st[lvl] $hp $mp $user[name] ";
    }
    print '0';
  }
?>
