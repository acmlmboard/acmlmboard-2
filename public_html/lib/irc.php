<?php
/* IRC library 					*
 * This file contains all the code related to IRC. The sendirc() function will pass the final string to a function
 * in config.php that will handle all the bot specific functions required. This will allow each board owner to
 * easily connect the board to their bot.
 */
function get_irc_color($color){
	switch ($color)	{
		case "black":
			$irccolor = "\x0301";
			break;
		case "blue":
			$irccolor = "\x0302";
			break;
		case "green":
			$irccolor = "\x0303";
			break;
		case "red":
			$irccolor = "\x0304";
			break;
		case "brown":
			$irccolor = "\x0305";
			break;
		case "purple":
			$irccolor = "\x0306";
			break;
		case "olive":
			$irccolor = "\x0307";
			break;
		case "yellow":
			$irccolor = "\x0308";
			break;
		case "lt_green":
			$irccolor = "\x0309";
			break;
		case "teal":
			$irccolor = "\x0310";
			break;
		case "lt_aqua":
			$irccolor = "\x0311";
			break;
		case "ry_blue":
			$irccolor = "\x0312";
			break;
		case "pink":
			$irccolor = "\x0313";
			break;
		case "dk_grey":
			$irccolor = "\x0314";
			break;
		case "lt_grey":
			$irccolor = "\x0315";
			break;
		case "white":
			$irccolor = "\x0316";
			break;
	}
	return $irccolor;
}
?>