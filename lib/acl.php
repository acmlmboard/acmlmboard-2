<?php

/* Populate default Access Control List. */
$default_acl = array();
$q=$sql->query("SELECT r FROM tokenrights WHERE t=0");
while($d=$sql->fetchq($q)) {
  $default_acl[$d[r]]=1;
}

/* Populate token info. */
$tokens = array();
$q=$sql->query("SELECT * FROM tokens");
while($d=$sql->fetchq($q)) {
  $tokens[$d[id]]=$d;
}

/* Load Access Control List for user $uid. 0 yields the default list. */
function load_acl($uid) {
  global $sql,$default_acl;
  
  $acl=$default_acl;
  $q=$sql->query("SELECT tr.r FROM usertokens ut JOIN tokenrights tr ON tr.t=ut.t WHERE ut.u='$uid'");
  while($d=$sql->fetchq($q)) {
    $acl[$d[r]]=1;
  }

  return $acl;
}

/* Render a presentation of a token, if available. */
function present_token($tid) {
  if($tokens[$tid][img]=="") return "";
  
  return "<img src='".$tokens[$tid][img]."' title='".$tokens[$tid][name]."'>";
}

/* Return a presentation of all tokens for a user. */
function gettokenstring($uid) {
  static $cache;
  if(isset($cache[$uid])) return $cache[$uid];
  else {
    $q=$sql->query("SELECT t FROM usertokens WHERE u='$uid'");
    $str="";
    while($d=$sql->fetchq($q)) $str.=present_token($d[t]);
    if($str!="") $str.="<br>";
    return ($cache[$uid]=$str);
  }
}

?>
