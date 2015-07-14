<?php

function userlink_by_name($name) {
	global $sql, $config;
	$u = $sql->fetchp("SELECT " . userfields() . ",minipic FROM users WHERE UPPER(name)=UPPER(?) OR UPPER(displayname)=UPPER(?)", array($name, $name));
	if ($u)
		return userlink($u, null, $config['userlinkminipic']);
	else
		return 0;
}

function get_userlink($matches) {
	global $config;
	return userlink_by_id($matches[1], $config['userlinkminipic']);
}

function get_username_link($matches) {
	$x = str_replace('"', '', $matches[1]);
	$nl = userlink_by_name($x);
	if ($nl)
		return $nl;
	else
		return $matches[0];
}

function get_forumlink($matches) {
	$fl = forumlink_by_id($matches[1]);
	if ($fl)
		return $fl;
	else
		return $matches[0];
}

function get_threadlink($matches) {
	$tl = threadlink_by_id($matches[1]);
	if ($tl)
		return $tl;
	else
		return $matches[0];
}

function securityfilter($msg) {
	$tags = 'script|iframe|embed|object|textarea|noscript|meta|xmp|plaintext|base';
	$msg = preg_replace("'<(/?)({$tags})'si", "&lt;$1$2", $msg);

	$msg = preg_replace('@(on)(\w+\s*)=@si', '$1$2&#x3D;', $msg);

	$msg = preg_replace("'-moz-binding'si", ' -mo<z>z-binding', $msg);
	$msg = str_ireplace("expression", "ex<z>pression", $msg);
	$msg = preg_replace("'filter:'si", 'filter&#58;>', $msg);
	$msg = preg_replace("'javascript:'si", 'javascript&#58;>', $msg);
	$msg = preg_replace("'transform:'si", 'transform&#58;>', $msg);

	return $msg;
}

function makecode($match) {
	$code = htmlspecialchars($match[1]);
	$list = array("\r\n", "[", ":", ")", "_", "@", "-");
	$list2 = array("<br>", "&#91;", "&#58;", "&#41;", "&#95;", "&#64;", "&#45;");
	return "<table cellspacing=\"0\" style=\"width: 90%; min-width: 90%;\"><tr><td class=\"b n3\"><code class=\"prettyprint\" style=\"font-size:9pt;\">" . str_replace($list, $list2, $code) . "</code></table>";
}

function makeirc($match) {
	$code = htmlspecialchars($match[1]);
	$list = array("\r\n", "[", ":", ")", "_", "@", "-");
	$list2 = array("<br>", "&#91;", "&#58;", "&#41;", "&#95;", "&#64;", "&#45;");
	return "<table cellspacing=\"0\" style=\"width: 90%; min-width: 90%;\"><tr><td class=\"b n3\"><code style=\"font-size:9pt;\">" . str_replace($list, $list2, $code) . "</code></table>";
}

function makesvg($match) {
	$svgin = "<?xml version=\"1.0\" standalone=\"no\"?" . ">"
			. "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\"\n "
			. "\"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">"
			. "<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"{$match[1]}\" height=\"{$match[2]}\" viewBox=\"0 0 {$match[1]} {$match[2]}\" version=\"1.1\">";
	$svgout = "</svg>";

	$svgcode = $match[3];

	if (strpos($_SERVER['HTTP_USER_AGENT'], "Chrome") !== false)
		return "<img src=\"data:image/svg+xml;base64," . htmlspecialchars(base64_encode($svgin . $svgcode . $svgout)) . "\" width=\"{$match[1]}\" height=\"{$match[2]}\">";
	else
		return "<object data=\"data:image/svg+xml;base64," . htmlspecialchars(base64_encode($svgin . $svgcode . $svgout)) . "\" type=\"image/svg+xml\" width=\"{$match[1]}\" height=\"{$match[2]}\"></object>";
}

function makeswf($match) {
	static $swfid = 0;

	// in case you wonder why there is double html escaping going on:
	// we first escape the URL attribute, with ENT_QUOTES, to make sure that it doesn't leak out from anything
	// and then we escape the whole link's onclick attribute so that it doesn't leak out either (this guarantees valid HTML, too)
	return "<table cellspacing=\"0\"><tr><td class=\"b n3\" width=\"{$match[1]}\" height=\"" . ($match[2] + 4) . "\" style=\"text-align:center\">
" . "	<div style=\"padding:0px\" id=\"swf" . ( ++$swfid) . "\"></div>
" . "	<div style=\"font-size:50px\" id=\"swf{$swfid}play\">
" . "		<a href=\"#\" onclick=\"" . htmlspecialchars("document.getElementById('swf{$swfid}').innerHTML='<embed src=\"" . htmlspecialchars($match[3], ENT_QUOTES) . "\" width=\"{$match[1]}\" height=\"{$match[2]}\"></embed>';document.getElementById('swf{$swfid}stop').style.display='block';document.getElementById('swf{$swfid}play').style.display='none';return false;") . "\">&#x25BA;</a>
" . "	</div>
" . "</td>
" . "<td style=\"vertical-align:bottom\">
" . "	<div style=\"display:none\" id=\"swf{$swfid}stop\">
" . "		<a href=\"#\" onclick=\"document.getElementById('swf{$swfid}').innerHTML='';document.getElementById('swf{$swfid}stop').style.display='none';document.getElementById('swf{$swfid}play').style.display='block';return false;\">&#x25A0;</a>
" . "	</div>
" . "</td></tr></table>";
}

function filterstyle($match) {
	$style = $match[2];

	// remove newlines.
	// this will prevent them being replaced with <br> tags and breaking the CSS
	$style = str_replace("\n", '', $style);

	return $match[1] . $style . $match[3];
}

function postfilter($msg, $nosmilies = 0) {
	global $smilies, $config, $sql, $swfid;

	//[blackhole89] - [code] tag
	$msg = preg_replace_callback("'\[code\](.*?)\[/code\]'si", 'makecode', $msg);

	//[irc] variant of [code]
	$msg = preg_replace_callback("'\[irc\](.*?)\[/irc\]'si", 'makeirc', $msg);

	$msg = preg_replace_callback("@(<style.*?>)(.*?)(</style.*?>)@si", 'filterstyle', $msg);

	// security filtering needs to be done before [svg] is parsed because [svg]
	// uses tags that are otherwise blacklisted
	$msg = securityfilter($msg);

	//[blackhole89] - [svg] tag
	$msg = preg_replace_callback("'\[svg ([0-9]+) ([0-9]+)\](.*?)\[/svg\]'si", 'makesvg', $msg);

	$msg = str_replace("\n", '<br>', $msg);

	if (!$nosmilies) {
		for ($i = 0; $i < $smilies['num']; $i++)
			$msg = str_replace($smilies[$i]['text'], '�' . $smilies[$i]['text'] . '�', $msg);
		for ($i = 0; $i < $smilies['num']; $i++)
			$msg = str_replace('�' . $smilies[$i]['text'] . '�', '<img src=' . $smilies[$i]['url'] . ' align=absmiddle border=0 alt="' . $smilies[$i]['text'] . '" title="' . $smilies[$i]['text'] . '">', $msg);
	}

	//Relocated here due to conflicts with specific smilies.
	$msg = preg_replace("@(</?(?:table|caption|col|colgroup|thead|tbody|tfoot|tr|th|td|ul|ol|li|div|p|style|link).*?>)\r?\n@si", '$1', $msg);

	//$msg=preg_replace("'lemonparty'si",'ffff',$msg); Lemonparty Filter
	$msg = preg_replace("'\[(b|i|u|s)\]'si", '<\\1>', $msg);
	$msg = preg_replace("'\[/(b|i|u|s)\]'si", '</\\1>', $msg);
	$msg = str_replace('[spoiler]', '<span class="spoiler1"><span class="spoiler2">', $msg);
	$msg = str_replace('[/spoiler]', '</span></span>', $msg);
	$msg = preg_replace("'\[url\](.*?)\[/url\]'si", '<a href=\\1>\\1</a>', $msg);
	$msg = preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si", '<a href=\\1>\\2</a>', $msg);
	$msg = preg_replace("'\[img\](.*?)\[/img\]'si", '<img src=\\1>', $msg);
	$msg = str_replace('[quote]', '<blockquote><hr>', $msg);
	$msg = str_replace('[/quote]', '<hr></blockquote>', $msg);
	//Color Codes. Possibly could be simplified...
	$msg = str_replace('[red]', '<span style="color: #FFC0C0">', $msg);
	$msg = str_replace('[/red]', '</span>', $msg);
	$msg = str_replace('[green]', '<span style="color: #C0FFC0">', $msg);
	$msg = str_replace('[/green]', '</span>', $msg);
	$msg = str_replace('[blue]', '<span style="color: #C0C0FF">', $msg);
	$msg = str_replace('[/blue]', '</span>', $msg);
	$msg = str_replace('[orange]', '<span style="color: #FFC080">', $msg);
	$msg = str_replace('[/orange]', '</span>', $msg);
	$msg = str_replace('[yellow]', '<span style="color: #FFEE20">', $msg);
	$msg = str_replace('[/yellow]', '</span>', $msg);
	$msg = str_replace('[pink]', '<span style="color: #FFC0FF">', $msg);
	$msg = str_replace('[/pink]', '</span>', $msg);
	$msg = str_replace('[white]', '<span style="color: #FFFFFF">', $msg);
	$msg = str_replace('[/white]', '</span>', $msg);
	$msg = str_replace('[black]', '<span style="color: #000000">', $msg);
	$msg = str_replace('[/black]', '</span>', $msg);
	$msg = preg_replace("'\[color=([a-f0-9]{6})\](.*?)\[/color\]'si", '<span style="color: #\\1">\\2</span>', $msg);

	$msg = preg_replace_callback('\'@\"((([^"]+))|([A-Za-z0-9_\-%]+))\"\'si', "get_username_link", $msg);
//    $msg=preg_replace_callback('\'@(("([^"]+)"))\'si',"get_username_link",$msg);
	//$msg=preg_replace_callback('\'@(("([^"]+)")|([A-Za-z0-9_\-%]+))\'si',"get_username_link",$msg); //For Reference. Original no quote @username

	$msg = preg_replace_callback("'\[user=([0-9]+)\]'si", "get_userlink", $msg);
	$msg = preg_replace_callback("'\[forum=([0-9]+)\]'si", "get_forumlink", $msg);
	$msg = preg_replace_callback("'\[thread=([0-9]+)\]'si", "get_threadlink", $msg);
	$msg = preg_replace_callback("'\[username=([[A-Za-z0-9 _\-%]+)\]'si", "get_username_link", $msg);


	$msg = preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si", '<a href=\\1>\\2</a>', $msg);

	$msg = preg_replace("'\[reply=\"(.*?)\" id=\"(.*?)\"\]'si", '<blockquote><span class="quotedby"><small><i><a href=showprivate.php?id=\\2>Sent by \\1</a></i></small></span><hr>', $msg);
	$msg = preg_replace("'\[quote=\"(.*?)\" id=\"(.*?)\"\]'si", '<blockquote><span class="quotedby"><small><i><a href=thread.php?pid=\\2#\\2>Posted by \\1</a></i></small></span><hr>', $msg);
	$msg = preg_replace("'\[quote=(.*?)\]'si", '<blockquote><span class="quotedby"><i>Posted by \\1</i></span><hr>', $msg);
	$msg = preg_replace("'>>([0-9]+)'si", '>><a href=thread.php?pid=\\1#\\1>\\1</a>', $msg);
	//dynamically convert SSL and non-SSL links
	if (isssl()) {
		$msg = str_replace($config['base'], $config['sslbase'], $msg);
	} else {
		$msg = str_replace($config['sslbase'], $config['base'], $msg);
	}

	//$msg=preg_replace(":reggie:","<img src='img/reggie.jpg'>",$msg); //Reggie? Image file not present in tree
	// [Mega-Mario] this can cause security issues, and who uses [swf] anyway?
	//$msg=preg_replace_callback("'\[swf ([0-9]+) ([0-9]+)\](.*?)\[/swf\]'si",'makeswf',$msg);
	//[KAWA] Youtube tag.
	$msg = preg_replace("'\[youtube\]([\-0-9_a-zA-Z]*?)\[/youtube\]'si", '<iframe width="420" height="315" src="http://www.youtube.com/embed/\\1" frameborder="0" allowfullscreen></iframe>', $msg);

	if ($htmlcomcolor = has_badge_perm("show-html-comments")) {
		if ($htmlcomcolor == "1")
			$htmlcomcolor = "#66ff66";
		$msg = str_replace('<!--', '<span style="color:' . $htmlcomcolor . ';">&lt;!--', $msg);
		$msg = str_replace('-->', '--></span>', $msg);
	}

	return $msg;
}

function amptags($post, $s) {
	global $sql;
	if (!$post['num'])
		$post['num'] = $post['uposts'];
	$exp = calcexp($post['uposts'], (ctime() - $post['uregdate']) / 86400);
	$s = str_replace("&postnum&", $post['num'], $s);
	$s = str_replace("&numdays&", floor((time() - $post['uregdate']) / 86400), $s);
	$s = str_replace("&postcount&", $post['uposts'], $s);
	$s = str_replace("&level&", $lvl = calclvl($exp), $s);
	$s = str_replace("&exp&", $exp, $s);
	$s = str_replace("&expdone&", $edone = ($exp - calclvlexp($lvl)), $s);
	$s = str_replace("&expnext&", $eleft = calcexpleft($exp), $s);
	$s = str_replace("&lvlexp&", calclvlexp($lvl + 1), $s);
	$s = str_replace("&lvllen&", lvlexp($lvl), $s);
	$s = str_replace("&expgain&", calcexpgainpost($post['uposts'], (ctime() - $post['uregdate']) / 86400), $s);
	$s = str_replace("&expgaintime&", calcexpgaintime($post['uposts'], (ctime() - $post['uregdate']) / 86400), $s);
	$s = str_replace("&exppct&", sprintf("%d", $edone * 100 / lvlexp($lvl)), $s);
	$s = str_replace("&exppct2&", sprintf("%d", $eleft * 100 / lvlexp($lvl)), $s);
	$s = str_replace("&rank&", $post['ranktext'], $s);
	$s = str_replace("&rankname&", preg_replace("'<(.*?)>'si", "", $post['ranktext']), $s);
	$s = str_replace("&lvlbar&", drawrpglevelbar($exp, '166'), $s);
	$s = str_replace("&mood&", $post['mood'], $s);
	$s = str_replace("&postrank&", $sql->result($sql->query("SELECT count(*) FROM users WHERE posts>" . $post['uposts']), 0, 0), $s); //Added by request of Acmlm
	// e modifier is no longer supported... using preg_replace_callback to stop the complaining.
	$replace_callback = function($match) use ($post) {
		return max($match[1] - $post['num'], 0); 
	};
	$s = preg_replace_callback('@&(\d+)&@si', $replace_callback, $s);
	return $s;
}

//2007-02-19 //blackhole89 - table depth validation
function tvalidate($str) {
	$l = strlen($str);
	$isquot = 0;
	$istag = 0;
	$isneg = 0;
	$iscomment = 0;
	$params = 0;
	$iscode = 0;
	$t_depth = 0;

	for ($i = 0; $i < $l;  ++$i) {
		if ($iscode) {
			if (!strcasecmp(substr($str, $i, 7), '[/code]'))
				$iscode = 0;
			else
				continue;
		}
		if (!strcasecmp(substr($str, $i, 6), '[code]'))
			$iscode = 1;
		if (($str[$i] == '\"' || $str[$i] == '\'') && $str[$i - 1] != '\\')
			$isquot = !$isquot;
		if ($str[$i] == '<' && !$isquot) {
			$istag = 1;
			$isneg = 0;
			$params = 0;
		} elseif ($str[$i] == '>' && !$isquot)
			$istag = 0;
		if ($str[$i] == '/' && !$isquot && $istag)
			$isneg = 1;
		if (!strcmp(substr($str, $i, 4), "<!--"))
			$iscomment = 1;
		if (!strcmp(substr($str, $i, 3), "-->"))
			$iscomment = 0;
		if ($istag && !$params && !$iscomment && !strcasecmp(substr($str, $i, 5), 'table'))
			$t_depth+=($isneg == 1 ? -1 : 1);
		if ($t_depth < 0)
			return -1;  //disrupture
		if ($istag && !$params && !$iscomment && $t_depth == 0 && !strcasecmp(substr($str, $i, 2), 'td'))
			return -1;  //td on top level
		if ($istag && !$params && !$iscomment && $t_depth == 0 && !strcasecmp(substr($str, $i, 2), 'tr'))
			return -1;  //tr on top level
		if ($istag && $str[$i] != ' ' && $str[$i] != '/' && $str[$i] != '<')
			$params = 1;
	}
	return $t_depth;
}

function htmlval($text) {
	$text = str_replace('&', '&amp;', $text);
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('"', '&quot;', $text);
	$text = str_replace('>', '&gt;', $text);
	return $text;
}

function forcewrap($text) {
	$l = 0;
	$text2 = '';
	for ($i = 0; $i < strlen($text); $i++) {
		$text2.=$text[$i];
		if ($text[$i] == ' ')
			$l = 0;
		else {
			$l++;
			if (!($l % 30))
				$text2.=' ';
		}
	}
	return $text2;
}

function posttoolbutton($e, $name, $title, $leadin, $leadout, $names = "") {
	if ($names == "")
		$names = $name;
	return "<td class=\"b n3\" id='tbk$names' style='width:16px;text-align:center'><a href=\"javascript:buttonProc('$e','tbk$names','$leadin','$leadout')\"><font size='0.1'><input type=\"button\" class=\"submit\" title='$title' value='$name' tabindex=\"-1\"></font></a></td>";
}

function posttoolbar() {
	return posttoolbutton("message", "B", "Bold", "[b]", "[/b]")
			. posttoolbutton("message", "I", "Italic", "[i]", "[/i]")
			. posttoolbutton("message", "U", "Underline", "[u]", "[/u]")
			. posttoolbutton("message", "S", "Strikethrough", "[s]", "[/s]")
			. "<td class=\"b n2\">&nbsp;</td>"
			. posttoolbutton("message", "_", "IRC", "[irc]", "[/irc]")
			. posttoolbutton("message", "/", "URL", "[url]", "[/url]")
			. posttoolbutton("message", "!", "Spoiler", "[spoiler]", "[/spoiler]", "sp")
			. posttoolbutton("message", "&#133;", "Quote", "[quote]", "[/quote]", "qt")
			. posttoolbutton("message", ";", "Code", "[code]", "[/code]", "cd")
			. "<td class=\"b n2\">&nbsp;</td>"
			. posttoolbutton("message", "[]", "IMG", "[img]", "[/img]")
			. posttoolbutton("message", "%", "SVG", "[svg <WIDTH> <HEIGHT>]", "[/svg]", "sv")
			. posttoolbutton("message", "YT", "YouTube", "[youtube]", "[/youtube]", "yt");
}

function moodlist($mid = -1, $userid = 0) { // 2009-07 Sukasa: It occurred to me that this would be better off in function.php, but last I checked
	// it was owned by root.
	// 2013-06 Mega-Mario: wish granted :)
	global $sql, $loguser;
	
	// I don't know if I can trust the input, so, I'm casting to int anyways...
	$mid = (int)$mid;
	$userid = (int)$userid;
	
	$moodset = $userid > 0 ? $userid : $loguser['id'];
	
	$moods = $sql->query("select '-Normal Avatar-' label, -1 id union select label, id from mood where user=$moodset");
	$moodst = "";
	while ($mood = $sql->fetch($moods))
		$moodst.= "<option value=\"{$mood['id']}\"" . ($mood['id'] == $mid ? "selected=\"selected\"" : "") . ">" . stripslashes($mood['label']) . "</option>";
	$moodst.= "</select>";
	
	return $moodst;
}

?>