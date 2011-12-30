<?php


  function checkuser($name,$pass){
    global $sql;
    $id=$sql->resultq("SELECT id FROM users WHERE name='$name' AND pass='$pass'");
    if(!$id) $id=0;
    return $id;
  }

  function forumbanned($uid,$fid) { //2009/07 Sukasa: Forum Bans
    global $sql;
    checknumeric($uid);
    checknumeric($fid);
    return $sql->resultq("select count(`uid`) > 0 from `forumbans` where `forum`=$fid and `uid`=$uid");
  }

  function checkuid($userid,$pass){
    global $sql;
    checknumeric($userid);
    $id=$sql->resultq("SELECT id FROM users WHERE id=$userid AND pass='".addslashes($pass)."'");
    if(!$id) $id=0;
    return $id;
  }

  function checkctitle(){
    global $loguser;
    if(!$loguser[id]) return 0;
    if($loguser[posts]>1200) return 1;
    if($loguser[posts]>800 && $loguser[regdate]<(time()-3600*24*200)) return 1;
    if($loguser[power]>0) return 1;
    return 0;
  }

  function getrank($set,$posts){
    global $ranks,$sql;

    //[KAWA] Climbing the Ranks Again
    if($posts > 5100)
    {
      $posts %= 5000;
      if($posts < 10)
        $posts = 10;
    }

    if($set) {
      $d=$sql->fetchq("SELECT str FROM ranks WHERE rs=$set AND p<=$posts ORDER BY p DESC LIMIT 1");
      return $d[0];
    }
    return "";
  }


 function userlink($user,$u=''){
    global $loguser;

    if(!$user[$u.name])
      $user[$u.name]='&nbsp;';

    return '<a href=profile.php?id='.$user[$u.id].'>'
          .userdisp($user,$u)
          .'</a>';
  }

  function userdisp($user,$u=''){
    global $sql;
    if($user[$u.power]<0)
      $user[$u.power]='x';


/* OLD HACKISH CODE FOR APRIL 5
    $stime=gettimeofday();
    $h=(($stime[usec]/5)%600);
    if($h<100){
  $r=255;
  $g=155+$h;
  $b=155;
    }elseif($h<200){
  $r=255-$h+100;
  $g=255;
  $b=155;
    }elseif($h<300){
  $r=155;
  $g=255;
  $b=155+$h-200;
    }elseif($h<400){
  $r=155;
  $g=255-$h+300;
  $b=255;
    }elseif($h<500){
  $r=155+$h-400;
  $g=155;
  $b=255;
    }else{
  $r=255;
  $g=155;
  $b=255-$h+500;
    }
    $rndcolor=substr(dechex($r*65536+$g*256+$b),-6);
    $namecolor="color=$rndcolor";    
  
*/  // hack

  //global $loguser;
 // if ($loguser['id'] != 640 && $user[$u.name] == "smwedit") $user[$u.name] = "smwdork"; 
/* Broken as of 2011-09-18 -Emuz */
  static $nccache;
  if(isset($nccache[$user[$u.id]])) $nc=$nccache[$user[$u.id]];
  else $nc=$nccache[$user[$u.id]]=$sql->resultq("SELECT t.nc".$user[$u.sex]." FROM usertokens ut, tokens t WHERE ut.u='".$user[$u.id]."' AND ut.t=t.id ORDER BY t.nc_prio DESC LIMIT 1");
 
   if($user[$u.minipic] && $user[showminipic]) $minipic="<img style='vertical-align:text-bottom' src='".$user[$u.minipic]."' border=0> ";
   else $minipic="";
  return "$minipic<font color='#$nc'>" //class=nc".$user[$u.sex].$user[$u.power].'>'
  //return "$minipic<font class=nc".$user[$u.sex].$user[$u.power].'>'
         .str_replace(" ","&nbsp;",htmlval($user[$u.name]))
         .'</font>';
 /* return '<font color=#'. $c .'>'
          .htmlval($user[$u.name])
          .'</font>';

    return '<font '. $namecolor .'>'
          .htmlval($user[$u.name])
          .'</font>';
*/
}


?>