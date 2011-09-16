<?php

/* Populate default Access Control List. */
$default_acl = array();
$q=$sql->query("SELECT r FROM tokenrights WHERE t=0");
while($d=$sql->fetchq($q)) {
  $default_acl[$d[r]]=1;
}

/* Load Access Control List for user $uid. 0 is passed for the default list. */
function load_acl($uid) {
  global $sql,$default_acl;
  
  $acl=$default_acl;
  $q=$sql->query("SELECT tr.r FROM usertokens ut JOIN tokenrights tr ON tr.t=ut.t WHERE ut.u='$uid'");
  while($d=$sql->fetchq($q)) {
    $acl[$d[r]]=1;
  }

  return $acl;
}

?>
