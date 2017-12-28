<?php
	require 'lib/common.php';
	pageheader();
	
	$server = (int)$_GET['server'];

//	$servers[1]		= "irc.nolimitzone.com"; //the round robin is fucking up, apparently.
	$servers[1]		= "akaneiro.irc.nolimitzone.com";
	$servers[2]		= "kuroi.irc.nolimitzone.com";
	if ($server > count($servers) || $server <= -1) $server = 0;

	$serverlist		= "";
	foreach ($servers as $num => $name) {
		if ($num != 1) $serverlist .= " - ";
		if ($server == $num) $serverlist .= "<u>";
		$serverlist .= "<a href=\"irc.php?server=". $num . ($_GET['jul'] ? "&jul=1" : "") ."\">". $name ."</a>";
		if ($server == $num) $serverlist .= "</u>";
		if ($num == 1) $serverlist .= " (preferred)";
	}

	$channel					= "#kafuka";
//	$channel					= "#rohmacking";
//	if ($_GET['jul']) $channel	= "#x";


	print	"<table cellspacing=\"0\" class=\"c1\">
".			"  <tr class=\"h\">
".			"    <td class=\"b h\">Java IRC Chat - $channel on NoLimitNET</td>
".			"  <tr>
".			"    <td class=\"b n1\" align=\"center\">Servers: $serverlist</td>
".			"  </tr>     
".			"  <tr>
".			"    <td class=\"b n2\" align=\"center\">";
	if ($server) {

		$badchars = array("~", "&", "@", "?", "!", ".", ",", "=", "+", "%", "*");

		$name = str_replace(" ", "", $loguser['name']);
		$name = str_replace($badchars, "_", $name);
		if (!$name) { 
			$name = "AB-Guest";
			$guestmsg	= "Welcome, guest. When you connect to the IRC network, please use the command <tt>/nick NICKNAME</tt>.<br>";
		}
		
	print "		
		$guestmsg
		<applet code=\"IRCApplet.class\" codebase=\"irc/\"  
		archive=\"irc.jar,pixx.jar\" width=\"100%\" height=400>
		<param name=\"CABINETS\" value=\"irc.cab,securedirc.cab,pixx.cab\">
		
		<param name=\"nick\" value=\"". $name ."\">
		<param name=\"alternatenick\" value=\"". $name ."_??\">
		<param name=\"fullname\" value=\"Acmlmboard IRC User\">
		<param name=\"host\" value=\"". $servers[$server] ."\">
		<param name=\"port\" value=\"6666\">
		<param name=\"gui\" value=\"pixx\">
		<param name=\"authorizedcommandlist\" value=\"all-server-s\">
		<param name=\"authorizedleavelist\" value=\"all-$channel\">
		<param name=\"authorizedjoinlist\" value=\"all-#nobodysworld-#knuck-#acmlm-#akane-#somethingwitty\">

		<param name=\"quitmessage\" value=\"Java IRC @   ".$config[base]."/irc.php\">
		<param name=\"autorejoin\" value=\"true\">
		
		<param name=\"style:bitmapsmileys\" value=\"false\">
		<param name=\"style:backgroundimage\" value=\"false\">
		<param name=\"style:backgroundimage1\" value=\"none+Channel all 2 background.png.gif\">
		<param name=\"style:sourcecolorrule1\" value=\"all all 0=000000 1=ffffff 2=0000ff 3=00b000 4=ff4040 5=c00000 6=c000a0 7=ff8000 8=ffff00 9=70ff70 10=00a0a0 11=80ffff 12=a0a0ff 13=ff60d0 14=a0a0a0 15=d0d0d0\">
		
		<param name=\"pixx:timestamp\" value=\"true\">
		<param name=\"pixx:highlight\" value=\"true\">
		<param name=\"pixx:highlightnick\" value=\"true\">
		<param name=\"pixx:nickfield\" value=\"false\">
		<param name=\"pixx:styleselector\" value=\"true\">
		<param name=\"pixx:setfontonstyle\" value=\"true\">

		<param name=\"command1\" value=\"/join $channel\">
		
		</applet>";

	} else {

	print "&nbsp;<br>Please choose a server to connect to.<br>&nbsp;";

	}


	print	"  </table>
".			"  <br>
".			"<table cellspacing=\"0\" class=\"c1\">
".			"  <tr class=\"h\">
".			"    <td class=\"b h\">Quick Help - Commands</td>
".			"  <tr>
".			"    <td class=\"b n1\">		
".			"      <tt>/nick [name]</tt> - changes your name
".			"      <br><tt>/me [action]</tt> - does an action (try it)
".			"      <br><tt>/msg [name] [message]</tt> - send a private message to another user
".			"      <br><tt>/join [#channel]</tt> - joins a channel
".			"      <br><tt>/part [#channel]</tt> - leaves a channel
".			"      <br><tt>/quit [message]</tt> - disconnects from the server
".			"  </tr>     
".			"  </table>";

	pagefooter();
?>
