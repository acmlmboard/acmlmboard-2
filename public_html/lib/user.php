<?php

	$rankcache = array();

	$userbirthdays = array();
function dobirthdays(){ //Function for calling after we get the timezone for the user set [Gywall]
    global $sql, $userbirthdays;
	// [Mega-Mario] Check for birthdays globally.
	// Makes stuff like checking for rainbow usernames a lot easier.
	$rbirthdays = $sql->query("SELECT `id` FROM `users` WHERE `birth` LIKE '".date('m-d')."%'");
	while ($bd = $sql->fetch($rbirthdays))
		$userbirthdays[$bd['id']] = true;
	return;
}

  function checkuser($name,$pass){
    global $sql,$pwdsalt,$pwdsalt2,$config;
    $islegacy=0;
    $hash=$sql->resultq("SELECT pass FROM users WHERE (name='$name' OR displayname='$name')");
    $result=password_verify($pass,$hash);
    if($config['legacylogin']==true && $hash==md5($pwdsalt2.$pass.$pwdsalt)){ $result=true; $islegacy=1; }
    if($result==false) return array(0,0,0); //Password verification has failed at this point.
    $id=$sql->resultq("SELECT id FROM users WHERE (name='$name' OR displayname='$name') AND pass='$hash'");
    if(!$id) return array(0,0,0);
    return array($id,$hash,$islegacy);
  }

  function checkuid($userid,$pass){
    global $sql;
    checknumeric($userid);
    $user=$sql->fetchq("SELECT * FROM users WHERE id=$userid AND pass='".addslashes($pass)."'");
    return $user;
  }

  function checkctitle($uid){
    global $sql,$loguser;
    
    $defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default`=1");
    
    if(!$loguser['id']) return false;
    if (has_perm_revoked('edit-title')) return false;
    if ($uid == $loguser['id'] && has_perm('edit-title')) { 
       if ($loguser['group_id']!=$defaultgroup) return true;
       if ($loguser[posts]>=100) return true;
       if ($loguser[posts]>50 && $loguser[regdate]<(time()-3600*24*60)) return true;
       return false;
    }

    if (has_perm('edit-titles')) return true;
    if (has_perm_with_bindvalue('edit-user-title',$uid)) return true;
    
    return false;
  }

  function checkcusercolor($uid){
    global $loguser,$config;
    
    if (!$config["perusercolor"]) return false;

    if(!$loguser['id']) return false;
    if (has_perm_revoked('has-customusercolor')) return false;
    if ($uid == $loguser['id'] && has_perm('has-customusercolor')) return true;
    
    /* Allow a custom user color after a specific postcount/time. *DISABLED*
    if($loguser[posts]>=4000) return true;
    if($loguser[posts]>3500 && $loguser[regdate]<(time()-3600*24*183)) return true;
    */
    
    if (has_perm('edit-customusercolors')) return true;
    if (has_perm_with_bindvalue('edit-user-customnickcolor',$uid)) return true;

    return false;
  }

  function checkcdisplayname($uid){
    global $sql,$loguser,$config;
    
    $defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default`=1");
    
    if (!$config["displayname"]) return false;

    if(!$loguser['id']) return false;
    if (has_perm_revoked('has-displayname')) return false;
    if ($uid == $loguser['id'] && has_perm('has-displayname')) { 
       if ($loguser['group_id']!=$defaultgroup) return true;
      //Allow a custom displayname after a specific postcount/time.
       //if ($loguser[posts]>=100) return true;
       //if ($loguser[posts]>50 && $loguser[regdate]<(time()-3600*24*60)) return true;
       return false;
    }
  
    if (has_perm('edit-displaynames')) return true;
    if (has_perm_with_bindvalue('edit-user-displayname',$uid)) return true;
    
    return false;
  }
  
  function checkcextendedprofile($uid){
    global $loguser,$config;
    
    if (!$config["extendedprofile"]) return false;

    if(!$loguser[id]) return false;
    if (has_perm_revoked('update-own-extended-profile')) return false;
    if ($uid == $loguser['id'] && has_perm('update-own-extended-profile')) return true;
    
    if (has_perm('update-extended-profiles')) return true;
    if (has_perm_with_bindvalue('update-user-extended-profile',$uid)) return true;

    return false;
  } 
//This block was borrowed from Blargboard. It is a proxy and stop forum spam detection routine and it's required defined function for url pulling.
function queryURL($url)
{
  if (function_exists('curl_init'))
  {
    $page = curl_init($url);
    if ($page === FALSE)
    return FALSE;

    curl_setopt($page, CURLOPT_TIMEOUT, 10);
    curl_setopt($page, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($page, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($page, CURLOPT_USERAGENT, 'Acmlmboard/');

   $result = curl_exec($page);
    curl_close($page);
    return $result;
  }

  else if (ini_get('allow_url_fopen'))
  {
  return file_get_contents($url);
  }

    else
      return FALSE;
}

function isProxy()
{
  if (checkvar('_SERVER','HTTP_X_FORWARDED_FOR') && checkvar('_SERVER','HTTP_X_FORWARDED_FOR') != $_SERVER['REMOTE_ADDR'])
    return true;

  $result = queryURL('http://www.stopforumspam.com/api?ip='.urlencode($_SERVER['REMOTE_ADDR']));
  
  if (!$result)
    return false;
  if (stripos($result, '<appears>yes</appears>') !== FALSE)
    return true;

  return false;
}
//BLARG!

function renderdotrank($posts=0){
      //This function takes the number of posts a user has ($posts), and returns the html to be printed out. 
     
      $postcount = $posts; // Save the actual post count before we get the mod value

      // Protection again overflow of dots. This normally won't be triggered in a real-world situation.
      if($posts > 10000)
      {
        $posts %= 100010;
        if($posts < 10)
          $posts = 10;
      }

      $ranknum=floor($posts / 10) * 10;
      //Based off of AB 1.x code. Well.. more or is the same code...
        if ($postcount > 0)
        {
          $pr[5] = 5000;
          $pr[4] = 1000;
          $pr[3] =  250;
          $pr[2] =   50;
          $pr[1] =   10;

          $postsx = $posts;
          $dotnum[5] = floor($postsx / $pr[5]);
          $postsx = $postsx - $dotnum[5] * $pr[5];
          $dotnum[4] = floor($postsx / $pr[4]);
          $postsx = $postsx - $dotnum[4] * $pr[4];
          $dotnum[3] = floor($postsx / $pr[3]);
          $postsx = $postsx - $dotnum[3] * $pr[3];
          $dotnum[2] = floor($postsx / $pr[2]);
          $postsx = $postsx - $dotnum[2] * $pr[2];
          $dotnum[1] = floor($postsx / $pr[1]);

          $rank = "<span title=\"$ranknum\"> ";

          foreach($dotnum as $dot => $num) 
          {
            for ($x = 0; $x < $num; $x++) 
            {
              $rank .= "<img src=\"img/dots/dot". $dot .".gif\" align=\"absmiddle\">";
            }
          }

          if ($postcount < 10) return "Newbie";
          //else $rank .= "</span><br>$ranknum"; //This will show number the original way.
          else $rank .= "</span>";

        }
        else return "Non-Poster";

      return $rank;

    }

  function getrank($set,$posts){
    global $ranks,$sql,$rankcache;
	
	// [Mega-Mario] rank cache. In the lack of a better solution, avoids doing the same thing over and over again...
	if (isset($rankcache[$set][$posts]))
		return $rankcache[$set][$posts];
	
    if($set == "-1") 
	{
		$r = renderdotrank($posts);
		$rankcache[$set][$posts] = $r;
		return $r;
	}

    //[KAWA] Climbing the Ranks Again
    if($posts > 5100)
    {
      $posts %= 5000;
      if($posts < 10)
        $posts = 10;
    }

    if($set) {
      $d=$sql->fetchq("SELECT str FROM ranks WHERE rs=$set AND p<=$posts ORDER BY p DESC LIMIT 1");
	  $rankcache[$set][$posts] = $d['str'];
      return $d['str'];
    }
	$rankcache[$set][$posts] = '';
    return "";
  }

  function randnickcolor(){
  global $config;
  if(!is_numeric($config['rainbowoffset'])){
   $rainoff=5;
  } else {
    $rainoff=$config['rainbowoffset'];
    if($rainoff<1) $rainoff=5; //Value must be above zero.
 }
    /* OLD HACKISH CODE FOR APRIL 5 */
    $stime=gettimeofday();
    $h=(($stime[usec]/$rainoff)%600);
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
    return $rndcolor;   
  }
  
function userfields($tbl='', $pf='')
{
	$fields = array('id','name','displayname','sex','group_id','nick_color','enablecolor');
	
	$ret = '';
	foreach ($fields as $f)
	{
		if ($ret) $ret .= ',';
		if ($tbl) $ret .= '`'.$tbl.'`.';
		$ret .= '`'.$f.'`';
		if ($pf) $ret .= ' AS `'.$pf.$f.'`';
	}
	
	return $ret;
}

 function userlink_by_id($uid,$usemini='') {
    global $sql;
    $u = $sql->fetchp("SELECT ".userfields().",minipic FROM users WHERE id=?",array($uid));        
    $u['showminipic']=$usemini;   
    return userlink($u);
 }

 function userlink($user,$u='',$usemini=''){
    global $loguser;
    if(!$user[$u.'name'])
      $user[$u.'name']='&nbsp;';

    return '<a href="profile.php?id='.$user[$u.'id'].'">'
          .userdisp($user,$u,$usemini)
          .'</a>';
  }

  function userdisp($user,$u='',$usemini=''){
    global $sql, $config, $usergroups, $userbirthdays, $usercnc, $nccache;

   if($usemini) $user['showminipic'] = true;
//Enable per theme nick colors & light theme nick shadows
  $unclass ='';
  $unspanend ='';
  $nccss = '';
  if($config['useshadownccss'])
    {
      $unclass="<span class='needsshadow'>";
      $unspanend="</span>";
    }
//Enable Caching of Name Colours for name-intensive pages.
  if(isset($nccache[$user[$u.'id']])){
    $nc=$nccache[$user[$u.'id']];
  } else {
  if($config['nickcolorcss']) $nccss="class='nc".$user[$u.'sex'].$user[$u.'group_id']."'";
//Over-ride for custom colours [Gywall]
  if($user[$u.'nick_color'] && $user[$u.'enablecolor'] && $config['perusercolor'])
  { 
    $nc = $user[$u.'nick_color'];
    $nccss = "";
	}
  else
	{
		$group = $usergroups[$user[$u.'group_id']];
		$nc = $group['nc'.$user[$u.'sex']];
	}
  //Random Nick Color on Birthday
  if (isset($userbirthdays[$user[$u.'id']]) && !user_has_perm('banned',$user[$u.'id'],$user[$u.'group_id']))
    $nc = "RANDOM";

  $nccache[$user[$u.'id']]=$nc;
  }
  if($nc=="RANDOM") $nc = randnickcolor();
  $n = $user[$u.'name'];
	if($user[$u.'displayname'] && $config['displayname'])
		$n = $user[$u.'displayname'];
		
	if(isset($user[$u.'minipic']) && isset($user['showminipic'])) $minipic="<img style='vertical-align:text-bottom' src='".$user[$u.'minipic']."' border=0> ";
	else $minipic="";

  //Badge username manipulation
  if($config['badgesystem'] && $config['usernamebadgeeffects'])
      {

        if(has_badge_perm("rainbow_username", $user[$u.'id']))
         {
                $nc = randnickcolor();
         }

        $cssstyle = "color:#$nc;";

        $result = has_badge_perm("change_username_style", $user[$u.'id']);
        if($result)
         {
            $cssstyle .=has_badge_perm("change_username_style", $user[$u.'id']);
         }

        $userdisname = "$minipic$unclass<span $nccss style='$cssstyle'>"
        .str_replace(" ","&nbsp;",htmlval($n))
        .'</span>'.$unspanend;

      }

   else 
    {
      $userdisname = "$minipic$unclass<span $nccss style='color:#$nc;'>"
        .str_replace(" ","&nbsp;",htmlval($n))
        .'</span>'.$unspanend;
    }
	return $userdisname;
}


?>
