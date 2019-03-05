<?php


  function userlink_by_name($name) {
    global $sql, $config;
    $u = $sql->fetchp("SELECT ".userfields().",minipic FROM users WHERE UPPER(name)=UPPER(?) OR UPPER(displayname)=UPPER(?)",array($name, $name));     
    if ($u) return userlink($u,null,$config['userlinkminipic']);
    else return 0;
  }



  function get_userlink($matches) {
    global $config;
    return userlink_by_id($matches[1],$config['userlinkminipic']);
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
  
// https://stackoverflow.com/questions/1336776/xss-filtering-function-in-php
function securityfilter($data) {
	// Fix &entity\n;
	$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
	// Remove any attribute starting with "on" or xmlns
	#$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
	do {
		$old_data   = $data;
		$data       = preg_replace('#(<[A-Za-z][^>]*?[\x00-\x20\x2F"\'])(on|xmlns)[A-Za-z]*=([^>]*+)>#iu', '$1DISABLED_$2$3>', $data);
	} while ($old_data !== $data);
	// Remove javascript: and vbscript: protocols
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
	// Remove namespaced elements (we do not need them)
	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
	do {
	    // Remove really unwanted tags
	    $old_data = $data;
	    $data = preg_replace('#<(/*(?:applet|b(?:ase|gsound)|embed|frame(?:set)?|i(?:frame|layer)|layer|meta|noscript|object|plaintext|script|title|textarea|xml|xmp)[^>]*+)>#i', '&lt;$1&gt;', $data);
	} while ($old_data !== $data);
	
	// This is for CSS filters
	$data=str_ireplace("expression", "ex<z>pression",$data);
	$data=preg_replace("'filter\s:'si", 'filter&#58;>',$data);
	$data=preg_replace("'transform\s:'si", 'transform&#58;>',$data);
//Block attempts to bypass the filter.
    $data=str_replace('enc64href','href',$data);
    $data=str_replace('enc64src','src',$data);
	
	return $data;
}

  function nofilterchar($match)
  {
	$code = htmlspecialchars($match);
	$list = array("\r\n","[",":",")","_","@","-");
	$list2 = array("<br>","&#91;","&#58;","&#41;","&#95;","&#64;","&#45;"); //Added zero-width space behind bbcode & smilies firing inside code/irc blocks
	return str_replace($list,$list2,$code);
  
  }
  function filterurl($match)
  {
	$src=str_replace(array($match[1],'=','\'','"'),'',$match[0]);
	return "enc64$src=".base64_encode($match[1]);
  }
  function unfilterurl($match)
  {
	$src=str_replace(array($match[1],'=','\'','"','enc64'),'',$match[0]);
	return "$src=\"".base64_decode($match[1])."\"";
  }
  function makecode64($match)
  {
	global $L;
	return "[code]".base64_encode($match[1])."[/code]";
  }
  function makeirc64($match)
  {
	global $L;
	return "[irc]".base64_encode($match[1])."[/irc]";
  }
  function makecode($match)
  {
	global $L;
	return "$L[TBL] style=\"width: 90%; min-width: 90%;\">$L[TR]><td class=\"b cd\" style=\"background: #000;\"><code class=\"prettyprint\" style=\"font-size:9pt;\">".nofilterchar(base64_decode($match[1]))."</code></table>";
  }
 
  function makeirc($match)
  {
    global $L;
    return "$L[TBL] style=\"width: 90%; min-width: 90%;\">$L[TR]>$L[TD3]><code style=\"font-size:9pt;\">".nofilterchar(base64_decode($match[1]))."</code></table>";
  } 
  function makesvg($match)
  {
	$svgin="<?xml version=\"1.0\" standalone=\"no\"?".">"
          ."<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\"\n "
    ."\"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">"
    ."<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"{$match[1]}\" height=\"{$match[2]}\" viewBox=\"0 0 {$match[1]} {$match[2]}\" version=\"1.1\">";
    $svgout="</svg>";
	
	$svgcode = $match[3];
	
	if(strpos($_SERVER['HTTP_USER_AGENT'],"Chrome")!==false)
		return "<img src=\"data:image/svg+xml;base64,".htmlspecialchars(base64_encode($svgin.$svgcode.$svgout))."\" width=\"{$match[1]}\" height=\"{$match[2]}\">";
    else
		return "<object data=\"data:image/svg+xml;base64,".htmlspecialchars(base64_encode($svgin.$svgcode.$svgout))."\" type=\"image/svg+xml\" width=\"{$match[1]}\" height=\"{$match[2]}\"></object>";
  }
/*  Function is no longer used.
  function makeswf($match)
  {
	global $L;
	static $swfid = 0;
	
	// in case you wonder why there is double html escaping going on:
	// we first escape the URL attribute, with ENT_QUOTES, to make sure that it doesn't leak out from anything
	// and then we escape the whole link's onclick attribute so that it doesn't leak out either (this guarantees valid HTML, too)
	return "$L[TBL]>$L[TR]>$L[TD3] width=\"{$match[1]}\" height=\"".($match[2]+4)."\" style=\"text-align:center\">
".		"	<div style=\"padding:0px\" id=\"swf".(++$swfid)."\"></div>
".		"	<div style=\"font-size:50px\" id=\"swf{$swfid}play\">
".		"		<a href=\"#\" onclick=\"".htmlspecialchars("document.getElementById('swf{$swfid}').innerHTML='<embed src=\"".htmlspecialchars($match[3],ENT_QUOTES)."\" width=\"{$match[1]}\" height=\"{$match[2]}\"></embed>';document.getElementById('swf{$swfid}stop').style.display='block';document.getElementById('swf{$swfid}play').style.display='none';return false;")."\">&#x25BA;</a>
".		"	</div>
".		"</td>
".		"<td style=\"vertical-align:bottom\">
".		"	<div style=\"display:none\" id=\"swf{$swfid}stop\">
".		"		<a href=\"#\" onclick=\"document.getElementById('swf{$swfid}').innerHTML='';document.getElementById('swf{$swfid}stop').style.display='none';document.getElementById('swf{$swfid}play').style.display='block';return false;\">&#x25A0;</a>
".		"	</div>
".		"</td></tr></table>";
  }*/
    function nobreaks($match)
  {
	return str_replace("\n", '', $match[1]);
  }
 
  function filterstyle($match)
  {
	$style = $match[2];
	// remove newlines.
	// this will prevent them being replaced with <br> tags and breaking the CSS
	$style = str_replace("\n", '', $style);

//Prevent animations, as too many can slow up less powerful systems.
	$style=preg_replace("'@keyframes'si",'noanimation4u',$style);
	$style=preg_replace("'@-webkit-keyframe'si",'noanimation4u',$style);
//Blacklisting body tag [Epele]
	$style=preg_replace("'[.](.*?)body'si",'.\\1XBDY',$style);
	$style=preg_replace("'body'si",'fish',$style);
	$style=preg_replace("'[.](.*?)XBDY'si",'.\\1body',$style);
//Blacklisting html tag
	$style=preg_replace("'[.](.*?)html'si",'.\\1XBDY',$style);
	$style=preg_replace("'html'si",'fish',$style);
	$style=preg_replace("'[.](.*?)XBDY'si",'.\\1html',$style);
//Selectors that shouldn't be used by a post/layout
	$style=str_replace(':root','',$style);
	$style=str_replace(':not','',$style);

	return $match[1].$style.$match[3];
  }

function ytube($match){
  $msg=$match[1];
  $msg=str_replace(array("http://","https://","m.youtube.com/","youtube.com/","watch?","youtu.be/","embed/"),"",$msg);
  $msg=str_replace("?","&",$msg);
  $vars=explode("&",$msg);
  $tid='';
  foreach($vars AS $vfind){
    $fid=explode("=",$vfind);
    if(!isset($vid)&&!isset($fid[1])) $vid=$fid[0]; //Orphaned variable is likely to be our Video ID.
    switch($fid[0]){
    //Video ID
    case 'v': $vid=$fid[1]; break;
    //Start time has two possible results
    case 't': 
    case 'start': $tid.="&start=".$fid[1]; break; 
    //End time for specific sections
    case 'end': $tid.="&end=".$fid[1]; break; 
    //Show related Links at end
    case 'rel': $tid.="&rel=".$fid[1]; break;
    // Leaving these defined here for if they are ever requested to be enabled.
    // Controls hides the controls, ShowInfo hides the title of the video.
    //case 'controls': $tid.="&controls=".$fid[1]; break;
    //case 'showinfo': $tid.="&showinfo=".$fid[1]; break;
    }
  }
  return '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$vid.'?'.$tid.'" frameborder="0" allowfullscreen></iframe>';
}

 function postfilter($msg, $nosmilies=0){
    global $smilies, $L, $config, $sql, $swfid;

    //[code] tag, [irc] tag encoding
    $msg=preg_replace_callback("'\[code\](.*?)\[/code\]'si",'makecode64',$msg);
    $msg=preg_replace_callback("'\[irc\](.*?)\[/irc\]'si",'makeirc64',$msg);

    //Moved [url] and [img] tags here to filter
    $msg=preg_replace("'\[url\](.*?)\[/url\]'si",'<a href=\\1>\\1</a>',$msg);
    $msg=preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si",'<a href=\\1>\\2</a>',$msg);    
    $msg=preg_replace("'\[img\](.*?)\[/img\]'si",'<img src=\\1 style="max-width: 100%">',$msg);
    $msg=preg_replace("'\[imgs\](.*?)\[/imgs\]'si",'<a href="\1" title="Click to enlarge"><img src="\1" style="max-width: 300px; max-height: 300px;"></a>',$msg);

	
    $msg = preg_replace_callback("@(<style.*?>)(.*?)(</style.*?>)@si", 'filterstyle', $msg);

    //Moving Line Break parsing to before the security filter, as this can be used to bypass it.
    $msg=preg_replace_callback("'\[nobr\](.*?)\[/nobr\]'si",'nobreaks',$msg); //No Line Breaks tag block
    $msg=str_replace("\n",'<br>',$msg);
    // security filtering needs to be done before [svg] is parsed because [svg]
    // uses tags that are otherwise blacklisted
    $msg = securityfilter($msg);
    //[code] tag, [irc] tag decoding
    $msg=preg_replace_callback("'\[code\](.*?)\[/code\]'si",'makecode',$msg);
    $msg=preg_replace_callback("'\[irc\](.*?)\[/irc\]'si",'makeirc',$msg);
    //Url filtering on href= or src=
    $msg=preg_replace_callback('/href=["\']?([^"\s\'>]+)["\']?/','filterurl',$msg);
    $msg=preg_replace_callback ('/src=["\']?([^"\s\'>]+)["\']?/','filterurl',$msg);

    //[blackhole89] - [svg] tag
    $msg=preg_replace_callback("'\[svg ([0-9]+) ([0-9]+)\](.*?)\[/svg\]'si",'makesvg',$msg);


    
    if (!$nosmilies) {
      for($i=0;$i<$smilies['num'];$i++)
        $msg=str_replace($smilies[$i]['text'],'<img src='.$smilies[$i]['url'].' align=absmiddle border=0 alt="'.$smilies[$i]['text'].'" title="'.$smilies[$i]['text'].'">',$msg);
    }

    //Unfilter URLs now we've passed the smilies.
    $msg=preg_replace_callback('/enc64href=["\']?([^"\s\'>]+)["\']?/','unfilterurl',$msg);
    $msg=preg_replace_callback ('/enc64src=["\']?([^"\s\'>]+)["\']?/','unfilterurl',$msg);

    //Relocated here due to conflicts with specific smilies.
    $msg = preg_replace("@(</?(?:table|caption|col|colgroup|thead|tbody|tfoot|tr|th|td|ul|ol|li|div|p|style|link).*?>)\r?\n@si", '$1', $msg);
	
    $msg=preg_replace("'\[(b|i|u|s)\]'si",'<\\1>',$msg);
    $msg=preg_replace("'\[/(b|i|u|s)\]'si",'</\\1>',$msg);
    $msg=str_replace('[spoiler]','<span class="spoiler1" onclick=""><span class="spoiler2">',$msg);
    $msg=str_replace('[/spoiler]','</span></span>',$msg);
    $msg=str_replace('[quote]','<blockquote><hr>',$msg);
    $msg=str_replace('[/quote]','<hr></blockquote>',$msg);
    //Color Codes.
    $msg=str_replace('[red]','<span style="color: #FFC0C0">',$msg);
    $msg=str_replace('[green]','<span style="color: #C0FFC0">',$msg);
    $msg=str_replace('[blue]','<span style="color: #C0C0FF">',$msg);
    $msg=str_replace('[orange]','<span style="color: #FFC080">',$msg);
    $msg=str_replace('[yellow]','<span style="color: #FFEE20">',$msg);
    $msg=str_replace('[pink]','<span style="color: #FFC0FF">',$msg);
    $msg=str_replace('[white]','<span style="color: #FFFFFF">',$msg);
    $msg=str_replace('[black]','<span style="color: #000000">',$msg);
    $msg=str_replace(array('[/red]','[/green]','[/blue]','[/orange]','[/yellow]','[/pink]','[/white]','[/black]'),'</span>',$msg);
    $msg=preg_replace("'\[color=([a-f0-9]{6})\](.*?)\[/color\]'si",'<span style="color: #\\1">\\2</span>',$msg);

    $msg=preg_replace_callback('\'@\"((([^"]+))|([A-Za-z0-9_\-%]+))\"\'si',"get_username_link",$msg);

    $msg=preg_replace_callback("'\[user=([0-9]+)\]'si","get_userlink",$msg);
    $msg=preg_replace_callback("'\[forum=([0-9]+)\]'si","get_forumlink",$msg);
    $msg=preg_replace_callback("'\[thread=([0-9]+)\]'si","get_threadlink",$msg);
    $msg=preg_replace_callback("'\[username=([[A-Za-z0-9 _\-%]+)\]'si","get_username_link",$msg);


    $msg=preg_replace("'\[reply=\"(.*?)\" id=\"(.*?)\"\]'si",'<blockquote><span class="quotedby"><small><i><a href=showprivate.php?id=\\2>Sent by \\1</a></i></small></span><hr>',$msg);
    $msg=preg_replace("'\[quote=\"(.*?)\" id=\"(.*?)\"\]'si",'<blockquote><span class="quotedby"><small><i><a href=thread.php?pid=\\2#\\2>Posted by \\1</a></i></small></span><hr>',$msg);
    $msg=preg_replace("'\[quote=(.*?)\]'si",'<blockquote><span class="quotedby"><i>Posted by \\1</i></span><hr>',$msg);
    $msg=preg_replace("'>>([0-9]+)'si",'>><a href=thread.php?pid=\\1#\\1>\\1</a>',$msg);
    //dynamically convert SSL and non-SSL links
    if(isssl()) $msg=str_replace($config['base'],$config['sslbase'],$msg);
    else $msg=str_replace($config['sslbase'],$config['base'],$msg);


    //Youtube tag. Updated from Kawa's initial version to now dissect urls. Adds support for timed starts.
    $msg = preg_replace_callback("'\[youtube\](.*?)\[/youtube\]'si",'ytube', $msg);
    
    if ($htmlcomcolor = has_badge_perm("show-html-comments")) {
      if ($htmlcomcolor == "1") $htmlcomcolor = "#66ff66";
      $msg=str_replace('<!--','<span style="color:'.$htmlcomcolor.';">&lt;!--',$msg);
      $msg=str_replace('-->','--></span>',$msg);
    }

    return $msg;
  }

  function amptags($post,$s){
	global $sql;
    if(!$post['num']) $post['num']=$post['uposts'];
    $exp=calcexp($post['uposts'],(ctime()-$post['uregdate'])/86400);
    $s=str_replace("&postnum&",$post['num'],$s);
    $s=str_replace("&numdays&",floor((time()-$post['uregdate'])/86400),$s);
    $s=str_replace("&postcount&",$post['uposts'],$s);
    $s=str_replace("&level&",$lvl=calclvl($exp),$s);
    $s=str_replace("&exp&",$exp,$s);
    $s=str_replace("&expdone&",$edone=($exp-calclvlexp($lvl)),$s);
    $s=str_replace("&expnext&",$eleft=calcexpleft($exp),$s);
    $s=str_replace("&lvlexp&",calclvlexp($lvl+1),$s);
    $s=str_replace("&lvllen&",lvlexp($lvl),$s);
    $s=str_replace("&expgain&",calcexpgainpost($post['uposts'],(ctime()-$post['uregdate'])/86400),$s);
    $s=str_replace("&expgaintime&",calcexpgaintime($post['uposts'],(ctime()-$post['uregdate'])/86400),$s);
    $s=str_replace("&exppct&",sprintf("%d",$edone*100/lvlexp($lvl)),$s);
    $s=str_replace("&exppct2&",sprintf("%d",$eleft*100/lvlexp($lvl)),$s);
    $s=str_replace("&rank&",$post['ranktext'],$s);
    $s=str_replace("&rankname&",preg_replace("'<(.*?)>'si","",$post['ranktext']),$s);
    $s=str_replace("&lvlbar&",drawrpglevelbar($exp, '166'),$s);
    $s=str_replace("&mood&",(isset($post['mood'])?$post['mood']:"0"),$s);
    $s=str_replace("&postrank&",$sql->result($sql->query("SELECT count(*) FROM users WHERE posts>".$post['uposts']),0,0),$s); //Added by request of Acmlm
    //This one's from ABXD
    $s = preg_replace_callback('@&(\d+)&@si', function ($match) use ($post) { return max($match[1] - $post['num'], 0); }, $s);
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

  function htmlval($text){
    $text=str_replace('&','&amp;',$text);
    $text=str_replace('<','&lt;',$text);
    $text=str_replace('"','&quot;',$text);
    $text=str_replace('>','&gt;',$text);
    return $text;
  }

  function posttoolbutton($e,$upref,$name,$title,$leadin,$leadout,$names=""){
    global $L;
    if($names=="") $names=$name;
    return "$L[TD3] id='tbk$names' style='width:16px;text-align:center'><a href=\"javascript:bbCode('$e','$leadin','$leadout',$upref)\"><font size='0.1'><input type=\"button\"  title='$title' class='Submit $name' tabindex=\"-1\" style=\"width: 24px; height: 24px;\"></font></a></td>";
  }
  
  function posttoolbar($upref)
  { 
	global $smilies,$L;
	//print_r($smilies);
        $smiletxt="$L[TBL] style='display: none' id='smilebar'>$L[TR] class='toolbar'>$L[TD3]>";
        for($i=0;$i<$smilies['num'];$i++){
          $smiletxt.="<div style=\"float:left; margin-right: 2px;\"><a href=\"javascript:bbCode('message','smilies','".addslashes($smilies[$i]['text'])."','')\"><font size='0.1'><button type=\"button\" class=\"button\" style=\"background: #000000; padding: 0px; width: 24px; height: 24px;\" title=\"".$smilies[$i]['text']."\"><img src=".$smilies[$i]['url']." style=\"max-width: 18px; max-height: 18px;\"></button></font></a></div>";
          //if($i%16==15 && $i!=$smilies[num]) $smiletxt.="</tr>$L[TR] class='toolbar'>";
        }
	return posttoolbutton("message",$upref,"ToolBarB","Bold","b","")
           .posttoolbutton("message",$upref,"ToolBarI","Italic","i","")
           .posttoolbutton("message",$upref,"ToolBarU","Underline","u","")
           .posttoolbutton("message",$upref,"ToolBarS","Strikethrough","s","")
           ."$L[TD2]>&nbsp;</td>"
           .posttoolbutton("message",$upref,"ToolBarUrl","URL","url","")
           .posttoolbutton("message",$upref,"ToolBarSpoil","Spoiler","spoiler","","sp")
           .posttoolbutton("message",$upref,"ToolBarIrc","IRC","irc","")
           .posttoolbutton("message",$upref,"ToolBarQuote","Quote","quote","","qt")
           .posttoolbutton("message",$upref,"ToolBarCode","Code","code","","cd")
           ."$L[TD2]>&nbsp;</td>"
           .posttoolbutton("message",$upref,"ToolBarImg","IMG","img","")
           .posttoolbutton("message",$upref,"ToolBarSvg","SVG","svg <WIDTH> <HEIGHT>","svg","sv")
	   .posttoolbutton("message",$upref,"ToolBarYt","YouTube","youtube","","yt")
           ."$L[TD3] id='tbk' style='width:16px;text-align:center'><a href=\"javascript:togglesmiles()\"><font size='0.1'><input type=\"button\" title='Smilies' class='Submit ToolBarSmile' tabindex=\"-1\" style=\"width: 24px; height: 24px;\"></font></a></td>"
           ."$smiletxt";
  }
  
  function moodlist($mid = -1, $userid='') { // 2009-07 Sukasa: It occurred to me that this would be better off in function.php, but last I checked
                        // it was owned by root.
						// 2013-06 Mega-Mario: wish granted :)
    global $sql, $loguser;
    //Attempting to fix what is displayed when editing someone else's post
    is_numeric($userid);
    if($userid > 0) $moodset = $userid;
    else $moodset = $loguser['id'];

    //$mid = (isset($_POST[mid]) ? $_POST[mid] : -1);
    $moods = $sql->query("select '-Normal Avatar-' label, -1 id union select label, id from mood where user=$moodset");
    $moodst="";
    while ($mood=$sql->fetch($moods))
      $moodst.= "<option value=\"$mood[id]\"".($mood['id']==$mid?"selected=\"selected\"":"").">".stripslashes($mood['label'])."</option>";
    $moodst.= "</select>";
    return $moodst;
  }

?>
