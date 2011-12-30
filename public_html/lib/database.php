<?php
  $sql=new mysql;
  $sql->connect($sqlhost,$sqluser,$sqlpass) or die("Couldn't connect to MySQL server");
  $sql->selectdb($sqldb) or die("Couldn't find MySQL database");
?>