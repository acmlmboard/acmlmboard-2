<?php


  function userlink_by_name($name) {
    global $sql;
    $u = $sql->fetchp("SELECT id,name,displayname,power,minipic FROM users WHERE UPPER(name)=UPPER(?) OR UPPER(displayname)=UPPER(?)",array($name, $name));     
    if ($u) return userlink($u);
    else return 0;
  }



  function get_userlink($matches) {
    return userlink_by_id($matches[1]);
  }
  function get_username_link($matches) {
    $x = str_replace('"','',$matches[1]);
    $nl = userlink_by_name($x);
    if ($nl) return $nl;
    else return $matches[0];
  }
  function get_forumlink($matches) {
    $fl = forumlink_by_id($matches[1]);
    if ($fl) return $fl;
    else return $matches[0];
  }
  function get_threadlink($matches) {
    $tl = threadlink_by_id($matches[1]);
    if ($tl) return $tl;
    else return $matches[0];
  }

 function postfilter($msg, $nosmilies=0){
    global $smilies, $L, $config, $sql;

    //[blackhole89] - [code] tag
    $list = array("<","\\\"" ,"\\\\" ,"\\'","\r\n","[",":",")","_","@","-");
    $list2 = array("&lt;","\"","\\","\'","<br>","&#91;","&#58;","&#41;","&#95;","&#64;","&#45;");
    $msg=preg_replace("'\[code\](.*?)\[/code\]'sie",
       '\''."$L[TBL] width=90% style=\"min-width: 90%;\">$L[TR]>$L[TD3]><code style=font-size:9pt;>".'\''
      .'.str_replace($list,$list2,\'\\1\').\'</code></table>\'',$msg);

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
        $msg=str_replace('«'.$smilies[$i][text].'»','<img src='.$smilies[$i][url].' align=absmiddle border=0 alt="'.$smilies[$i][text].'" title="'.$smilies[$i][text].'">',$msg);
    }

    $tags=array('script','iframe','textarea','noscript','meta','xmp','plaintext','base');
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
    $msg=str_replace('[spoiler]','<span class="spoiler1"><span class="spoiler2">',$msg);
    $msg=str_replace('[/spoiler]','</span></span>',$msg);
    $msg=preg_replace("'\[url\](.*?)\[/url\]'si",'<a href=\\1>\\1</a>',$msg);
    $msg=preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si",'<a href=\\1>\\2</a>',$msg);    
    $msg=preg_replace("'\[img\](.*?)\[/img\]'si",'<img src=\\1>',$msg);
    $msg=str_replace('[quote]','<blockquote><hr>',$msg);
    $msg=str_replace('[/quote]','<hr></blockquote>',$msg);

    $msg=preg_replace_callback('\'@(("([^"]+)")|([A-Za-z0-9_\-%]+))\'si',"get_username_link",$msg);
//    $msg=preg_replace_callback('\'@(("([^"]+)"))\'si',"get_username_link",$msg);

    $msg=preg_replace_callback("'\[user=([0-9]+)\]'si","get_userlink",$msg);
    $msg=preg_replace_callback("'\[forum=([0-9]+)\]'si","get_forumlink",$msg);
    $msg=preg_replace_callback("'\[thread=([0-9]+)\]'si","get_threadlink",$msg);

    $msg=preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si",'<a href=\\1>\\2</a>',$msg);

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

	//[KAWA] Youtube tag.
	$msg = preg_replace("'\[youtube\]([\-0-9_a-zA-Z]*?)\[/youtube\]'si","<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1&amp;hl=en&amp;fs=1\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"never\"></param><embed src=\"http://www.youtube.com/v/\\1&amp;hl=en&amp;fs=1\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"never\" allowfullscreen=\"false\" width=\"425\" height=\"344\"></embed></object>", $msg);

  //[KAWA] TODO: replace with token effect
  /*
    if ($x_hacks['goggles']) {
      $msg=str_replace('<!--','<font color="#66ff66">&lt;!--',$msg);
      $msg=str_replace('-->','--></font>',$msg);
    }
    */
    
    if (has_badge_perm("show-html-comments")) {
      $msg=str_replace('<!--','<font color="#66ff66">&lt;!--',$msg);
      $msg=str_replace('-->','--></font>',$msg);
    }

    return $msg;
  }

  function amptags($post,$s){
    $exp=calcexp($post[uposts],(ctime()-$post[uregdate])/86400);
    $s=str_replace("&postnum&",$post[num],$s);
    $s=str_replace("&numdays&",floor((time()-$post[uregdate])/86400),$s);
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
    $s=str_replace("&postrank&",mysql_result(mysql_query("SELECT count(*) FROM users WHERE posts>$post[uposts]"),0,0),$s); //Added by request of Acmlm
    //This one's from ABXD
    $s= preg_replace('@&(\d+)&@sie','max($1 - '.$post[num].', 0)', $s);
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
        return -1;  //disrupture
      if($istag && !$params && !$iscomment && $t_depth==0 && !strcasecmp(substr($str,$i,2),'td'))
        return -1;  //td on top level
      if($istag && !$params && !$iscomment && $t_depth==0 && !strcasecmp(substr($str,$i,2),'tr'))
        return -1;  //tr on top level
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

  function posttoolbutton($e,$name,$leadin,$leadout,$names=""){
    global $L;
    if($names=="") $names=$name;
    return "$L[TD3] id='tbk$names' style='width:16px;text-align:center'><a href=\"javascript:buttonProc('$e','tbk$names','$leadin','$leadout')\">$name</a></td>";
  }

?>