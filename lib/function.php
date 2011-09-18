<?php
  function usectime(){
    $t=gettimeofday();
    return $t[sec]+$t[usec]/1000000;
  }
  $start=usectime();

  if(!get_magic_quotes_gpc()){
    if(is_array($GLOBALS)) while(list($key,$val)=each($GLOBALS)) if(is_string($val)) $GLOBALS[$key]=addslashes($val);
    if(is_array($_POST  )) while(list($key,$val)=each($_POST  )) if(is_string($val)) $_POST[$key]  =addslashes($val);
  }

  require 'mysql.php';
  require 'layout.php';
  require 'config.php';
  $sql=new mysql;
  $sql->connect($sqlhost,$sqluser,$sqlpass) or die("Couldn't connect to MySQL server");
  $sql->selectdb($sqldb) or die("Couldn't find MySQL database");

  function checknumeric(&$var){
    if(!is_numeric($var))
      $var=0;
  }

  function ctime(){
    return time();
  }

  function isssl(){
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on');
  }

  function cdate($format,$date){
    global $loguser;
    return date($format,$date+$loguser[tzoff]);
  }

  function timeunits($sec){
    if($sec<    60) return "$sec sec.";
    if($sec<  3600) return floor($sec/60).' min.';
    if($sec< 86400) return floor($sec/3600).' hour'.($sec>=7200?'s':'');
    return floor($sec/86400).' day'.($sec>=172800?'s':'');
  }

  function timeunits2($sec){
    $d=floor($sec/86400);
    $h=floor($sec/3600)%24;
    $m=floor($sec/60)%60;
    $s=$sec%60;
    $ds=($d>1?'s':'');
    $hs=($h>1?'s':'');
    $str=($d?"$d day$ds ":'').($h?"$h hour$hs ":'').($m?"$m min. ":'').($s?"$s sec.":'');
    if(substr($str,-1)==' ') $str=substr_replace($str,'',-1);
    return $str;
  }

  function getforumbythread($tid){
    global $sql;
    static $cache;
    return isset($cache[$tid])?$cache[$tid]:$cache[$tid]=$sql->resultq("SELECT forum FROM threads WHERE id='$tid'");
  }

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

  function encryptpwd($pass){
    global $config;
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $config[ckey], $pass, MCRYPT_MODE_ECB, $iv)));
  }

  function decryptpwd($pass){
    global $config;
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $config[ckey], base64_decode($pass), MCRYPT_MODE_ECB, $iv));
  }

  function packlcookie($pass){
    $a=func_get_args();
    $exstr=implode(",",array_slice($a,1));
    if(strlen($exstr)) $exstr=",$exstr";
    return encryptpwd($_SERVER['REMOTE_ADDR'].",".$pass.$exstr);
  }

  function ipmatch($mask,$ip){
    $pos=strpos($mask,"*");
    if($pos===false) {
      $pos=strlen($mask);
      if(strlen($ip)>$pos) return false;
    }
    $mask=substr($mask,0,strpos($mask,"*"));
    if($mask==substr($ip,0,$pos)) return true;
    return false;
  }

  function unpacklcookie($pass){
    $p=decryptpwd($pass);
    $pa=explode(",",$p);
    $p1=explode(".",$pa[0]);
    $p2=explode(".",$_SERVER['REMOTE_ADDR']);
    if(!strlen($pa[2]) && (!($p1[0]==$p2[0] && $p1[1]==$p2[1]))) {
      // old-style lcookie, no /16 match
      return "";
    } else if(!strlen($pa[2])) {
      return $pa[1];
    }
    $i=2;
    while(strlen($pa[$i])){
      if(ipmatch($pa[$i],$_SERVER['REMOTE_ADDR']))
        return $pa[1];
      ++$i;
    }
    return "";
  }

  function packsafenumeric($i){
    global $loguser;
    return encryptpwd($i.",".$loguser[id]);
  }

  function unpacksafenumeric($s,$fallback=-1){
    global $loguser;
    $a=explode(",",decryptpwd($s));
    if($a[1]!=$loguser[id]) return $fallback;
    else return $a[0];
  }

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
    if($loguser[posts]>800 && $loguser[regdate]<(time()-3600*24*365)) return 1;
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

  function redirect($url,$msg,$delay=2){
    return "You will now be redirected to <a href=$url>$msg</a> ...<META HTTP-EQUIV=REFRESH CONTENT=$delay;URL=$url>";
  }

  function mkmath($in) {
    global $sql;
    if($d=$sql->fetchq("SELECT file FROM mcache WHERE hash='".md5($in)."'")) $pstr="0".$d[file];
    else {
      $in=addslashes($in);
      $pstr=`cd math/;./texvc ../mathres/ ../mathres/ "$in" utf-8;cd ..`;
      if(strlen($pstr)<32) $pstr="/invalid";
      $sql->query("INSERT INTO mcache VALUES('".md5($in)."','".substr($pstr,1,32)."')");
    }
    return "<img style=vertical-align:middle; src=mathres/".substr($pstr,1,32).".png>";
  }

  function loadsmilies(){
    global $sql,$smilies;
    $i=0;
    $s=$sql->query("SELECT * FROM smilies");
    while($smilies[$i++]=$sql->fetch($s));
    $smilies[num]=$i;
  }

  function postfilter($msg, $nosmilies=0){
    global $smilies, $L, $config;

    //[blackhole89] - [code] tag
    $list = array("<","\\\"" ,"\\\\" ,"\\'","\r\n","[",":",")","_");
    $list2 = array("&lt;","\"","\\","\'","<br>","&#91;","&#58;","&#41;","&#95;");
    $msg=preg_replace("'\[code\](.*?)\[/code\]'sie",
       '\''."$L[TBL] width=90%>$L[TR]>$L[TD3]><pre><font style=font-size:9pt;>".'\''
      .'.str_replace($list,$list2,\'\\1\').\'</table>\'',$msg);

    //[blackhole89] - [svg] tag
    $svgin="'<?xml version=\"1.0\" standalone=\"no\"?".">"
          ."<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\"\n "
	  ."\"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">"
	  ."<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"\\1\" height=\"\\2\" viewBox=\"0 0 \\1 \\2\" version=\"1.1\">'";
    $svgout="'</svg>'";
    $svglist1 = array("\\\"","\\\\","\\'");
    $svglist2 = array("\"","\\","\'");
    if(strpos($_SERVER['HTTP_USER_AGENT'],"Chrome")!==false)
      $msg=preg_replace("'\[svg ([0-9]+) ([0-9]+)\](.*?)\[/svg\]'sie",
       '\''."<img src=\"data:image/svg+xml;base64,"
      .'\''.".base64_encode($svgin.".'str_replace($svglist1,$svglist2,\'\\3\')'.".$svgout).".'"'
      ."\\\"  width=".'\'\\1\' height=\'\\2\''.">\"",$msg);
    else $msg=preg_replace("'\[svg ([0-9]+) ([0-9]+)\](.*?)\[/svg\]'sie",
       '\''."<object data=\"data:image/svg+xml;base64,"
      .'\''.".base64_encode($svgin.".'str_replace($svglist1,$svglist2,\'\\3\')'.".$svgout).".'"'
      ."\\\" type=\\\"image/svg+xml\\\" width=".'\'\\1\' height=\'\\2\''."></object>\"",$msg);


    $msg=preg_replace("'\[math\](.*?)\[/math\]'sie", "mkmath('\\1')",$msg);

    $msg=str_replace("\n",'<br>',$msg);
    
    if (!$nosmilies) {
      for($i=0;$i<$smilies[num];$i++)
        $msg=str_replace($smilies[$i][text],'«'.$smilies[$i][text].'»',$msg);
      for($i=0;$i<$smilies[num];$i++)
        $msg=str_replace('«'.$smilies[$i][text].'»','<img src='.$smilies[$i][url].' align=absmiddle border=0>',$msg);
    }

    $tags=array('script','iframe','textarea','noscript','meta','xmp','plaintext');
    foreach($tags as $tag){
      $msg=preg_replace("'<$tag(.*?)>'si" ,"&lt;$tag\\1>" ,$msg);
      $msg=preg_replace("'</$tag(.*?)>'si","&lt;/$tag>",$msg);
    }

//  $msg=preg_replace("'<table(.*?)>(.*?)</table>'si",'°table\\1°\\2°/table°',$msg);
//  $msg=preg_replace("'<table(.*?)>'si",'&lt;table\\1>',$msg);
//  $msg=preg_replace("'</table(.*?)>'si",'&lt;/table>',$msg);
//  $msg=preg_replace("'°table'si",'<table',$msg);
//  $msg=preg_replace("'°/table°'si",'</table>',$msg);

//  $msg=preg_replace("'jul.rusted'si",'jul&#46;rusted',$msg);
    $msg=preg_replace("'display:'si",'display&#58;',$msg);
//    $msg=preg_replace("'([\s]+)([o])([n])([a-z]*)'si",'\\1\\2<z>\\3\\4',$msg);
    $msg=preg_replace('/<([^>]+)\bon\w+\s*=\s*".+"/Uis', "<\\1", $msg);
    $msg=preg_replace("/<([^>]+)\bon\w+\s*=\s*'.+'/Uis", "<\\1", $msg);
    $msg=preg_replace('/<([^>]+)\bon\w+\s*=\s*\S+/i', "<\\1", $msg);

    $msg=preg_replace("'-moz-binding'si",' -mo<z>z-binding',$msg);
    $msg=str_ireplace("expression","ex<z>pression",$msg);
    $msg=preg_replace("'lemonparty'si",'ffff',$msg);
    $msg=preg_replace("'filter:'si",'filter&#58;>',$msg);
    $msg=preg_replace("'javascript:'si",'javascript&#58;>',$msg);
    $msg=preg_replace("'\[(b|i|u|s)\]'si",'<\\1>',$msg);
    $msg=preg_replace("'\[/(b|i|u|s)\]'si",'</\\1>',$msg);
    $msg=str_replace('[spoiler]','<div style=color:black;background:black class=fonts><font color=white><b>Spoiler:</b></font><br>',$msg);
    $msg=str_replace('[/spoiler]','</div>',$msg);
    $msg=preg_replace("'\[url\](.*?)\[/url\]'si",'<a href=\\1>\\1</a>',$msg);
    $msg=preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si",'<a href=\\1>\\2</a>',$msg);
    $msg=preg_replace("'\[img\](.*?)\[/img\]'si",'<img src=\\1>',$msg);
    $msg=str_replace('[quote]','<blockquote><hr>',$msg);
    $msg=str_replace('[/quote]','<hr></blockquote>',$msg);
    $msg=preg_replace("'\[reply=\"(.*?)\" id=\"(.*?)\"\]'si",'<blockquote><small><i><a href=showprivate.php?id=\\2>Sent by \\1</a></i></small><hr>',$msg);
    $msg=preg_replace("'\[quote=\"(.*?)\" id=\"(.*?)\"\]'si",'<blockquote><small><i><a href=thread.php?pid=\\2#\\2>Posted by \\1</a></i></small><hr>',$msg);
    $msg=preg_replace("'\[quote=(.*?)\]'si",'<blockquote><i>Posted by \\1</i><hr>',$msg);
    $msg=preg_replace("'>>([0-9]+)'si",'>><a href=thread.php?pid=\\1#\\1>\\1</a>',$msg);
    //dynamically convert SSL and non-SSL links
    if(isssl()) $msg=str_replace($config[base],$config[sslbase],$msg);
    else $msg=str_replace($config[sslbase],$config[base],$msg);

    $msg=preg_replace(":reggie:","<img src='img/reggie.jpg'>",$msg);

    //spam
//    $msg=str_ireplace("posting","posting<span style=position:relative;left:-55px;top:-28px;width:0px;height:0px;vertical-align:text-bottom;display:inline-block><img src=img/rsi.png></span>",$msg);

    static $swfid=0;
    $msg=preg_replace("'\[swf ([0-9]+) ([0-9]+)\](.*?)\[/swf\]'sie",
       '\''."$L[TBL]>$L[TR]>$L[TD3] width=\\1 height='.(\\2+4).' style=\"text-align:center\"><div style=\"padding:0px\" id=swf'.".'(++$swfid)'.".'></div><div style=\"font-size:50px\" id=swf'.".'($swfid)'.".'play><a href=\"#\" onclick=\"document.getElementById(\'swf'.".'$swfid'.".'\').innerHTML=\'<embed src=\\3 width=\\1 height=\\2></embed>\';document.getElementById(\'swf'.".'$swfid'.".'stop\').style.display=\'block\';document.getElementById(\'swf'.".'$swfid'.".'play\').style.display=\'none\';return false;\">&#x25BA;</a></div></td><td style=\"vertical-align:bottom\"><div style=\"display:none\" id=swf'.".'($swfid)'.".'stop><a href=\"#\" onclick=\"document.getElementById(\'swf'.".'$swfid'.".'\').innerHTML=\'\';document.getElementById(\'swf'.".'$swfid'.".'stop\').style.display=\'none\';document.getElementById(\'swf'.".'$swfid'.".'play\').style.display=\'block\';return false;\">&#x25A0;</a></div></td></tr></table>"
       .'\'',$msg);

	//[KAWA] TODO: replace with token effect
	/*
    if ($x_hacks['goggles']) {
      $msg=str_replace('<!--','<font color="#66ff66">&lt;!--',$msg);
      $msg=str_replace('-->','--></font>',$msg);
    }
    */

    return $msg;
  }

  function amptags($post,$s){
    $exp=calcexp($post[uposts],(ctime()-$post[uregdate])/86400);
    $s=str_replace("&postnum&",$post[num],$s);
    $s=str_replace("&postcount&",$post[uposts],$s);
    $s=str_replace("&level&",$lvl=calclvl($exp),$s);
    $s=str_replace("&exp&",$exp,$s);
    $s=str_replace("&expdone&",$edone=($exp-calclvlexp($lvl)),$s);
    $s=str_replace("&expnext&",$eleft=calcexpleft($exp),$s);
    $s=str_replace("&lvlexp&",calclvlexp($lvl+1),$s);
    $s=str_replace("&lvllen&",lvlexp($lvl),$s);
    $s=str_replace("&expgain&",calcexpgainpost($post[uposts],(ctime()-$post[uregdate])/86400),$s);
    $s=str_replace("&expgaintime&",calcexpgaintime($post[uposts],(ctime()-$post[uregdate])/86400),$s);
    $s=str_replace("&exppct&",sprintf("%d",$edone*100/lvlexp($lvl)),$s);
    $s=str_replace("&exppct2&",sprintf("%d",$eleft*100/lvlexp($lvl)),$s);
    $s=str_replace("&rank&",getrank($post[urankset],$post[uposts]),$s);
    $s=str_replace("&rankname&",preg_replace("'<(.*?)>'si","",getrank($post[urankset],$post[uposts])),$s);
    return $s;
  }

  //2007-02-19 //blackhole89 - table depth validation
  function tvalidate($str){
    $l=strlen($str);
    $isquot=0; $istag=0; $isneg=0; $iscomment=0; $params=0; $iscode=0;
    $t_depth=0;

    for($i=0;$i<$l;++$i){
      if($iscode) {
        if(!strcasecmp(substr($str,$i,7),'[/code]'))
	  $iscode=0;
	else continue;
      }
      if(!strcasecmp(substr($str,$i,6),'[code]'))
        $iscode=1;
      if(($str[$i]=='\"' || $str[$i]=='\'') && $str[$i-1]!='\\')
        $isquot=!$isquot;
      if($str[$i]=='<' && !$isquot){
        $istag=1; $isneg=0; $params=0;
      }elseif($str[$i]=='>' && !$isquot)
        $istag=0;
      if($str[$i]=='/' && !$isquot && $istag)
        $isneg=1;
      if(!strcmp(substr($str,$i,4),"<!--"))
        $iscomment=1;
      if(!strcmp(substr($str,$i,3),"-->"))
        $iscomment=0;
      if($istag && !$params && !$iscomment && !strcasecmp(substr($str,$i,5),'table'))
        $t_depth+=($isneg==1?-1:1);
      if($t_depth<0)
        return -1;	//disrupture
      if($istag && !$params && !$iscomment && $t_depth==0 && !strcasecmp(substr($str,$i,2),'td'))
        return -1;	//td on top level
      if($istag && !$params && !$iscomment && $t_depth==0 && !strcasecmp(substr($str,$i,2),'tr'))
        return -1;	//tr on top level
      if($istag && $str[$i]!=' ' && $str[$i]!='/' && $str[$i]!='<')
        $params=1;
    }
    return $t_depth;
  }

  function postfilter2($msg){
    $msg=postfilter($msg);
    $msg=preg_replace("'<embed(.*?)>'si" ,"&lt;embed\\1>" ,$msg);
    return $msg;
  }

  function htmlval($text){
    $text=str_replace('&','&amp;',$text);
    $text=str_replace('<','&lt;',$text);
    $text=str_replace('"','&quot;',$text);
    $text=str_replace('>','&gt;',$text);
    return $text;
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
  
*/	// hack

	//global $loguser;
//	if ($loguser['id'] != 640 && $user[$u.name] == "smwedit") $user[$u.name] = "smwdork"; 
  static $nccache;
  if(isset($nccache[$user[$u.id]])) $nc=$nccache[$user[$u.id]];
  else $nc=$nccache[$user[$u.id]]=$sql->resultq("SELECT t.nc".$user[$u.sex]." FROM usertokens ut, tokens t WHERE ut.u='".$user[$u.id]."' AND ut.t=t.id ORDER BY t.nc_prio DESC LIMIT 1");
  if($user[$u.minipic] && $user[showminipic]) $minipic="<img style='vertical-align:text-bottom' src='".$user[$u.minipic]."' border=0> ";
  else $minipic="";
  return "$minipic<font color='#$nc'>" //class=nc".$user[$u.sex].$user[$u.power].'>'
        .str_replace(" ","&nbsp;",htmlval($user[$u.name]))
        .'</font>';
/*	return '<font color=#'. $c .'>'
          .htmlval($user[$u.name])
          .'</font>';

  	return '<font '. $namecolor .'>'
          .htmlval($user[$u.name])
          .'</font>';
*/
}

  function forcewrap($text){
    $l=0;
    $text2='';
    for($i=0;$i<strlen($text);$i++){
      $text2.=$text[$i];
      if($text[$i]==' ')
        $l=0;
      else{
        $l++;
        if(!($l%30))
          $text2.=' ';
      }
    }
    return $text2;
  }

  function editthread($id,$title='',$forum=0,$icon='',$closed=-1,$sticky=-1,$delete=-1){
    global $sql;

    if($delete<1){
      $set='';
      if($title!='') $set.=",title=\"$title\"";
      if($icon!='')  $set.=",icon=$icon";
      if($closed>=0) $set.=",closed=$closed";
      if($sticky>=0) $set.=",sticky=$sticky";
      $set[0]=' ';
      if(strlen(trim($set))>0&&!is_array($set)) $sql->query("UPDATE threads SET $set WHERE id=$id");

      if($forum)
        movethread($id,$forum);
    }else{
    }
  }

  function movethread($id,$forum){
    global $sql;

    if(!$sql->resultq("SELECT COUNT(*) FROM forums WHERE id=$forum")) return;

    $thread=$sql->fetchq("SELECT forum,replies FROM threads WHERE id=$id");
    $sql->query("UPDATE threads SET forum=$forum WHERE id=$id");

    $last1=$sql->fetchq("SELECT lastdate,lastuser "
                       ."FROM threads "
                       ."WHERE forum=$thread[forum] "
                       ."ORDER BY lastdate DESC LIMIT 1");
    $last2=$sql->fetchq("SELECT lastdate,lastuser "
                       ."FROM threads "
                       ."WHERE forum=$forum "
                       ."ORDER BY lastdate DESC LIMIT 1");
    if($last1)
      $sql->query("UPDATE forums "
                ."SET posts=posts-($thread[replies]+1), "
                .    "threads=threads-1, "
                .    "lastdate=$last1[lastdate], "
                .    "lastuser=$last1[lastuser] "
                ."WHERE id=$thread[forum]");
    if($last2)
      $sql->query("UPDATE forums "
                 ."SET posts=posts+($thread[replies]+1), "
                 .    "threads=threads+1, "
                 .    "lastdate=$last2[lastdate], "
                 .    "lastuser=$last2[lastuser] "
                 ."WHERE id=$forum");
  }

  function syndrome($num){
    $a='>Affected by';
    if($num>= 75) $syn="83F3A3$a 'Reinfors Syndrome'";
    if($num>=100) $syn="FFE323$a 'Reinfors Syndrome' +";
    if($num>=150) $syn="FF5353$a 'Reinfors Syndrome' ++";
    if($num>=200) $syn="CE53CE$a 'Reinfors Syndrome' +++";
    if($num>=250) $syn="8E83EE$a 'Reinfors Syndrome' ++++";
    if($num>=300) $syn="BBAAFF$a 'Wooster Syndrome'!!";
    if($num>=350) $syn="FFB0FF$a 'Wooster Syndrome' +!!";
    if($num>=400) $syn="FFB070$a 'Wooster Syndrome' ++!!";
    if($num>=450) $syn="C8C0B8$a 'Wooster Syndrome' +++!!";
    if($num>=500) $syn="A0A0A0$a 'Wooster Syndrome' ++++!!";
    if($num>=500) $syn="A0A0A0$a 'Wooster Syndrome' ++++!!";
    if($num>=600) $syn="C762F2$a 'Anya Syndrome'!!!";
    if($num>=800) $syn="D06030$a 'Something higher than Anya Syndrome' +++++!!";
    if($syn) $syn="<br><i><font color=$syn</font></i>";
    return $syn;
  }

  function posttoolbutton($e,$name,$leadin,$leadout,$names=""){
    global $L;
    if($names=="") $names=$name;
    return "$L[TD3] id='tbk$names' style='width:16px;text-align:center'><a href=\"javascript:buttonProc('$e','tbk$names','$leadin','$leadout')\">$name</a></td>";
  }

  //2007-07-01 blackhole89
  //xkeeper: fadding width/height to make it load better, adding align to move it away from the IP somewhat
  function flagip($ip){
    global $sql; 
    $d=$sql->fetchq("SELECT cc2 FROM ip2c WHERE ip_from<=inet_aton('$ip') AND ip_to>=inet_aton('$ip')");
    return ($d[cc2]?" <img src=\"img/flags/".strtolower($d[cc2]).".png\" width=\"16\" height=\"11\" align=\"right\">":"").$ip;
  }

  function feedicon($icon,$para,$text="RSS feed"){
    return "<a href='$para'><img src='$icon' border='0' style='margin-right:5px' title='$text'></a>"
          ."<link rel='alternate' type='application/rss+xml' title='$text' href='$para'>";
  }

  require 'rpg.php';
?>
