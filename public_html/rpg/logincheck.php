<?php
  require 'function.php';

  $u=$sql->query_fetch("SELECT * FROM usersrpg WHERE id=$uid");

  print "ok $uid $u[room]";
?>