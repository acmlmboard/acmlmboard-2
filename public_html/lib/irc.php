<?php

/* IRC library 					*
 * This file contains all the code related to IRC. The sendirc() function will pass the final string to a function
 * in config.php that will handle all the bot specific functions required. This will allow each board owner to
 * easily connect the board to their bot.
 */

function get_irc_color($color, $bcolor) {
	switch ($color) {
		case "white":
		case "00":
		case "0":
			$irccolor = "\x0300";
			break;
		case "black":
		case "01":
		case "1":
			$irccolor = "\x0301";
			break;
		case "blue":
		case "02":
		case "2":
			$irccolor = "\x0302";
			break;
		case "green":
		case "03":
		case "3":
			$irccolor = "\x0303";
			break;
		case "red":
		case "04":
		case "4":
			$irccolor = "\x0304";
			break;
		case "brown":
		case "05":
		case "5":
			$irccolor = "\x0305";
			break;
		case "purple":
		case "06":
		case "6":
			$irccolor = "\x0306";
			break;
		case "orange":
		case "07":
		case "7":
			$irccolor = "\x0307";
			break;
		case "yellow":
		case "08":
		case "8":
			$irccolor = "\x0308";
			break;
		case "lt_green":
		case "09":
		case "9":
			$irccolor = "\x0309";
			break;
		case "teal":
		case "10":
			$irccolor = "\x0310";
			break;
		case "lt_cyan":
		case "11":
			$irccolor = "\x0311";
			break;
		case "lt_blue":
		case "12":
			$irccolor = "\x0312";
			break;
		case "pink":
		case "13":
			$irccolor = "\x0313";
			break;
		case "grey":
		case "14":
			$irccolor = "\x0314";
			break;
		case "lt_grey":
		case "15":
			$irccolor = "\x0315";
			break;
		case "normal":
		case "16":
		default:
			$irccolor = "\x0316";
			break;
	}

	switch ($bcolor) {
		case "white":
		case "00":
		case "0":
			$irccolor .= ",00";
			break;
		case "black":
		case "01":
		case "1":
			$irccolor .= ",01";
			break;
		case "blue":
		case "02":
		case "2":
			$irccolor .= ",02";
			break;
		case "green":
		case "03":
		case "3":
			$irccolor .= ",03";
			break;
		case "red":
		case "04":
		case "4":
			$irccolor .= ",04";
			break;
		case "brown":
		case "05":
		case "5":
			$irccolor .= ",05";
			break;
		case "purple":
		case "06":
		case "6":
			$irccolor .= ",06";
			break;
		case "orange":
		case "07":
		case "7":
			$irccolor .= ",07";
			break;
		case "yellow":
		case "08":
		case "8":
			$irccolor .= ",08";
			break;
		case "lt_green":
		case "09":
		case "9":
			$irccolor .= ",09";
			break;
		case "teal":
		case "10":
			$irccolor .= ",10";
			break;
		case "lt_cyan":
		case "11":
			$irccolor .= ",11";
			break;
		case "lt_blue":
		case "12":
			$irccolor .= ",12";
			break;
		case "pink":
		case "13":
			$irccolor .= ",13";
			break;
		case "grey":
		case "14":
			$irccolor .= ",14";
			break;
		case "lt_grey":
		case "15":
			$irccolor .= ",15";
			break;
		case "white2":
		case "16":
			$irccolor .= ",16";
			break;
		default:
			break;
	}
	return $irccolor;
}

function get_irc_style($style) {
	switch ($style) {
		case "bold":
		case "2":
		case "02":
			$ircstyle = "\x02";
			break;
		case "fixed":
		case "17":
		case "11":
			$ircstyle = "\x11";
			break;
		case "reverse":
		case "18":
		case "12":
			$ircstyle = "\x12";
			break;
		case "italic":
		case "29":
		case "1D":
			$ircstyle = "\x1D";
			break;
		case "underline":
		case "31":
		case "1F":
			$ircstyle = "\x1F";
			break;
		case "normal":
		case "15":
		case "0F":
		default:
			$ircstyle = "\x0F";
			break;
	}
	return $ircstyle;
}

function set_irc_style($fcolor, $bcolor, $style) {
	return get_irc_style($style) . get_irc_color($fcolor, $bcolor);
}

function get_irc_displayname() {
	global $loguser, $config, $sql, $irccolor;
	$q = $sql->fetch($sql->query("SELECT `char`,`irc_color` FROM `group` WHERE id=$loguser[group_id]"));
	$group_prefix = $q['char'];
	$group_color = $q['irc_color'];

	//Since $loguser[sex] give me nothing..
	$qu = $sql->fetch($sql->query("SELECT `sex` FROM users WHERE id=$loguser[id]"));
	$sex = $qu['sex'];

	if ($group_prefix && $config['ircnickprefix']) {
		$name = get_irc_style("bold");
		if ($group_color) {
			$name .= get_irc_color($group_color, "");
		}
		$name .= "$group_prefix" . get_irc_style("normal");
		$name .="{irccolor-name}";
	}

	if ($group_color && $config['ircnickcolor']) {
		$name .=get_irc_color($group_color);
	} elseif ($config['ircnicksex']) {
		switch ($sex) {
			case "0":
				$name .=get_irc_color($irccolor['male']);
				break;
			case "1":
				$name .=get_irc_color($irccolor['female']);
				break;
		}
	}

	$name .= ($loguser['displayname'] ? $loguser['displayname'] : $loguser['name']);
	return ($name);
}

function get_irc_usercolor() {
	//Note: This should/will be used to return more than just the logged in user. 
	global $loguser, $sql;
	$q = $sql->fetch($sql->query("SELECT `irc_color` FROM `group` WHERE id=$loguser[group_id]"));
	$group_color = $q['irc_color'];

	if ($group_color) {
		return get_irc_color($group_color);
	} else {
		return false;
	}
}

function get_irc_groupname($gid) {
	global $sql;

	$group = $sql->fetchp("SELECT * FROM `group` WHERE id=?", array($gid));
	if (!$group['title']) {
		return "";
	} else {
		return get_irc_usercolor() . $group['title'];
	}
}

function sendirc($text, $channel = null) {
	global $config, $irccolor;

	if (!$config['enableirc'])
		return false;
	//str_replace method to replace the board address. replaces {boardurl} with the link to the board thread/post.
	$text = str_replace('{boardurl}', $config[ircbase], $text);
	//str_replace method to fill in color codes
	$text = str_replace('{irccolor-base}', set_irc_style($irccolor[base]), $text);
	$text = str_replace('{irccolor-name}', set_irc_style($irccolor[name]), $text);
	$text = str_replace('{irccolor-title}', set_irc_style($irccolor[title]), $text);
	$text = str_replace('{irccolor-url}', set_irc_style($irccolor[url]), $text);
	$text = str_replace('{irccolor-yes}', set_irc_style($irccolor[yes]), $text);
	$text = str_replace('{irccolor-no}', set_irc_style($irccolor[no]), $text);

	if ($channel != null)
		$chan = $channel;
	else
		$chan = $config[pubchan];

	send_to_ircbot($text, $chan);
}

?>