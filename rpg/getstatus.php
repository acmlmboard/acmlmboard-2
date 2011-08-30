<?php
  require 'function.php';

  if(!$id){
    $user=$loguser;
    $st=$logst;
  }else{
    $user=getuser($id);
    $st=getstats($user,$items);
  }

  if($user[hp]<0) $user[hp]=$st[HP];
  if($user[mp]<0) $user[mp]=$st[MP];

  print "$st[lvl] ";
  for($i=0;$i<9;$i++)
    print $st[$stat[$i]].' ';
  print "$st[GP] $user[gcoins] $user[hp] $user[mp] $user[rank] $user[name]";
?>