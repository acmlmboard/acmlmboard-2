<?php
  require '../lib/function.php';

  $u=$_GET[u];
  if(is_numeric($u))
    $user=$sql->fetchq("SELECT * FROM users WHERE id=$u");
  else
    $user=$sql->fetchq("SELECT * FROM users WHERE name='$u'");

  if(!$user)
    die(k('03').'User not found');

  $time=ctime();
  $rank=$sql->resultq("SELECT count(id) FROM users WHERE posts>$user[posts]")+1;
  $days=($time-$user[regdate])/86400;
  $exp=calcexp($user[posts],$days);
  $level=calclvl($exp);
  $reg=date('m-d-y',$user[regdate]);
  $expx=$exp;
  $pgain=calcexpgainpost($user[posts],$days);
  $tgain=calcexpgaintime($user[posts],$days);
  if($user[posts]<0) $expx=-1;
  if($user[sex]==0) $sex='Male';
  if($user[sex]==1) $sex='Female';
  if($user[sex]==2) $sex='N/A';
  if($user[power]==-1) $power='Banned';
  if($user[power]==0) $power='Regular';
  if($user[power]==1) $power='Local Mod';
  if($user[power]==2) $power='Full Mod';
  if($user[power]==3) $power='Admin';
  $erank=$sql->resultq("SELECT count(id) FROM users WHERE posts>=0 AND floor(".sqlexpval().")>$expx")+1;
  $c[1]=k('05');
  $c[2]=k('14');
  $c[3]=k('03');
  $c[4]=k('09');
  $c[5]=k('07');
  print
	$c[3].$user[id].
	$c[2].": ".
	$c[5].$user[name].
	$c[1]."  [".
	$c[5].$user[posts].
	$c[2]." post".(abs($user[posts])>1?"s":"").", #".
	$c[4].$rank.
	$c[1]."]  [".
	$c[2]."Lv ".
	$c[5].$level.
	$c[2].", ".
	$c[5].$exp.
	$c[2]." EXP, #".
	$c[4].$erank.
	$c[1]."]  [".
	$c[2]."Gain: ".
	$c[5].$pgain.
	$c[2]."/p, ".
	$c[5].$tgain.
	$c[2]."s".
	$c[1]."]  [".
	$c[2]."Since ".
	$c[3].$reg.
	$c[1]."]  [".
	$c[3].$sex.
	$c[2].", ".
	$c[3].$power.
	$c[1]."]"
  ;
  function k($n){ return "\x03".$n; }
?>