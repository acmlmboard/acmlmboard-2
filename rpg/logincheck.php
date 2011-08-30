<?php
  require 'function.php';

  $u=$sql->fetchq("SELECT * FROM usersrpg WHERE id=$uid");

  print "ok $uid $u[room]";
?>