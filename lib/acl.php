<?php

/* Populate default Access Control List. */
$default_acl = array();
$q=$sql->query("SELECT r FROM tokenrights WHERE t=0");
while($d=$sql->fetch($q)) {
  $default_acl[$d[r]]=1;
}

/* Populate token info. */
$tokens = array();
$q=$sql->query("SELECT * FROM tokens");
while($d=$sql->fetch($q)) {
  $tokens[$d[id]]=$d;
}

/* Load Access Control List for user $uid. 0 yields the default list. */
function load_acl($uid) {
  global $sql,$default_acl;
  
  $acl=$default_acl;
  $q=$sql->query("SELECT tr.r FROM usertokens ut JOIN tokenrights tr ON tr.t=ut.t WHERE ut.u='$uid'");
  while($d=$sql->fetch($q)) {
    $acl[$d[r]]=1;
  }

  return $acl;
}

/* Render a presentation of a token, if available. */
function present_token($tid) {
  global $tokens;

  if($tokens[$tid][img]=="") return "";
  
  return "<img src='".$tokens[$tid][img]."' title='".htmlentities($tokens[$tid][name])."' style='vertical-align:text-bottom;padding-right:2px'>";
}

/* Return a presentation of all tokens for a user. */
function gettokenstring($uid) {
  global $sql;
  static $cache;
  
  if(isset($cache[$uid])) return $cache[$uid];
  else {
    $q=$sql->query("SELECT t FROM usertokens WHERE u='$uid'");
    $str="";
    while($d=$sql->fetch($q)) $str.=present_token($d[t]);
    if($str!="") $str.="<br>";
    return ($cache[$uid]=$str);
  }
}

/* specific ACL resolution functions */
function acl_for_thread($tid,$key) {
  global $loguser;
  if($loguser[acl][$key]) return 1;
  else if($loguser[acl]["$key t$tid"]) return 1;
  else if($loguser[acl]["$key f".getforumbythread($tid)]) return 1;
  return 0;
}

function acl_for_user($uid,$key) {
  global $loguser;
  if($loguser[acl][$key]) return 1;
  else if($loguser[acl]["$key u$uid"]) return 1;
  return 0;
}

function acl($key) {
  global $loguser;
  if($loguser[acl][$key]) return 1;
  return 0;
}

function acl_or_die($key) {
  global $L;
  global $sql;
  if (acl($key)) {
	return true;
 }
 else {
     $r = $sql->fetchp('SELECT title FROM rights WHERE r=?',array($key));
     $name = $r['title'];
     print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    You do not have the permissions to $name.<br>
".        "    <a href=./>Back to main</a>
".        "$L[TBLend]
";
     die();
}
}

/* Legacy */

  function isadmin(){
    global $loguser;
    return $loguser[power]>=3;
  }

  function ismod($fid=0){
    global $loguser;
    if($loguser[power]==1) return isset($loguser[modforums][$fid]);
    return $loguser[power]>=2;
  }

  function isbanned(){
    global $loguser;
    return $loguser[power]<0;
  }

/* End Legacy */

?>
