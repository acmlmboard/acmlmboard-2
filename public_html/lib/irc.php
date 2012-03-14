<?php
/* IRC library 					*
 * This file contains all the code related to IRC. The sendirc() function will pass the final string to a function
 * in config.php that will handle all the bot specific functions required. This will allow each board owner to
 * easily connect the board to their bot.
 */
function get_irc_color($color){
	switch ($color)	{
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
		case "white2":
		case "16":
			$irccolor = "\x0316";
			break;
	}
	return $irccolor;
}
?>