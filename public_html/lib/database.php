<?php
  $sql=new mysql;
  $sql->connect($sqlhost,$sqluser,$sqlpass) or die("Couldn't connect to MySQL server<br>".$sql->error());
  $sql->selectdb($sqldb) or die("Couldn't find MySQL database");

  /* Phase 2 */
  //require 'lib/Database/Perm.php';
  //require 'lib/Database/User.php';
  //DBRecord::setDB($sql);

?>