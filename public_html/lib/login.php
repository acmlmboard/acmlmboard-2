<?php
  $log=false;
      $logpermset = array();
  if($_COOKIE[user]>0){
    if($id=checkuid($_COOKIE[user],unpacklcookie($_COOKIE[pass]))){
      $log=true;
      $loguser=$sql->fetchq("SELECT * FROM users WHERE id=$id");
      load_user_permset();
    }else{
      setcookie('user',0);
      setcookie('pass','');
            load_guest_permset();
    }
  }
  else {
          load_guest_permset();

  }
?>