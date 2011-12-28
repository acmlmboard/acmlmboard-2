<?php

/*
 * B2Sprites subsystem by Kawa
 *
 * Based on the dodongo thing, this is supposed to be some kind of collect and discover bullshit
 * where you sometimes randomly get some videogame beastie on the page. Clicking that beastie for
 * the first time would then get it registered in your own Pokédex rip off, and getting a specific
 * amount yields bonus features such as bigger avatars or the ability to see <!-- comments -->.
 *
 * $chance is the % chance a beastie will appear in the first place.
 * $roll is the top value to start taking beasties from, with a random entry picked from the results.
 * So if the list is
 *   1. Dodongo    2. Starman
 *   3. Ika-chan   4. Monkey
 *   5. Turret     6. Defective Turret
 * and $roll is 3, it'll pick Dodongo, Starman or Ika-chan.
 *
 * TODO
 * -> Determine the $roll somehow.
 * -> Add franchise grouping to the Dex page?
 * -> Make the beasties disappear someway other than just click-gone.
 * -> Add some protecting from easy captures - right now a logged-in user can just go to
 *    /sprites.php?catch=[NUMBER HERE] and get them all.
 * 
 */


require_once("lib/spritelib.php");
$chance = 5;
$roll = 100;

//Always fail to roll if disabled, effectively never appearing at all.
if(acl('disable-sprites'))
	$chance = 0;
	
$monRequest = mysql_query("SELECT * FROM sprites WHERE id <= ".$roll);
$monData = array();
while($mon = mysql_fetch_assoc($monRequest))
	$monData[] = $mon;
$monData = $monData[array_rand($monData)];

/*
//Old way to pick pics: randomly between two fields.
$pic = rand(0, 1) ? $monData['alt']: $monData['pic'];
*/
//New way to pick pics: always pick the main field, but split it up.
$pics = explode("|", $monData['pic']);
$pic = $pics[array_rand($pics)];

/*
//Naive dodongo way
$x = "top: ".rand(0, 900)."px";
$y = "left: ".rand(0, 600)."px";
*/
//Relative adjusting way
$x = rand(0, 100);
$y = rand(0, 100);
$x = ($x < 50) ? "top: ".$x."%" : "bottom: ".(100-$x)."%";
$y = ($y < 50) ? "left: ".$y."%" : "right: ".(100-$y)."%";


switch($monData['anchor'])
{
	case "left":
		$y = "left: 0px";
		break;
	case "right":
		$y = "right: 0px";
		break;
	case "sides":
		$y = (rand(0, 1) ? "left" : "right").": 0px";
		break;
	case "sidepic":
		$s = rand(0, 1);
		$y = ($s ? "left" : "right").": 0px";
		$pic = $s ? $monData['alt']: $monData['pic'];
		break;
	case "top":
		$x = "top: 0px";
		break;
	case "bottom":
		$x = "bottom: 0px";
		break;
}

$monMarkup = "<img id=\"sprite\" style=\"opacity: 0.75; -moz-opacity: 0.75; position: fixed; ".$x."; ".$y."; z-index: 999\" src=\"img/sprites/".$pic."\" title=\"".$monData['title']."\" onclick=\"capture()\" />";

$spritehash = generate_sprite_hash($loguser['id'],$monData['id']);

$monScript = "<script language=\"javascript\">
	function capture()
	{
		document.getElementById(\"sprite\").style['display'] = \"none\";
		
		x = new XMLHttpRequest();
		x.onreadystatechange = function()
		{
			if(x.readyState == 4)
			{
				if(x.responseText != \"OK\")
					alert(x.responseText);
			}
		};
		x.open('GET', 'sprites.php?catch=".$monData['id']."&t=".$spritehash."', 
true);
		x.send(null);
	}
</script>";

$dongs = "";
if(rand(0, 100) < $chance)
{
	$dongs = $monMarkup;
	$junk .= $monScript;
}

/** ORIGINAL DODONGO CODE FOLLOWS **/
/* DELETEME */
/*
$dongs="<script language='javascript'>function bomb(l,x,y) {  var k=document.getElementById(l); var d=document.getElementById(l+'d'); k.src='img/dongbomb.png'; d.style.top-=120; d.style.left-=40; setTimeout(function() { remv(l); },1000); d.innerHTML=d.innerHTML+\"<embed id='bombsnd' src='etc/LTTP_Bomb_Blow.wav' loop='false' autostart='true' type='audio/wav' hidden='true'>\"; dongsc-=1; } function remv(l) { document.getElementById(l+'d').style.top=-300; if(dongsc==0) { document.getElementById(l+'d').innerHTML=\"<embed id='bombsnd' src='etc/LTTP_ItemFanfare.wav' loop='false' type='audio/wav' autostart='true' hidden='true'>\"; dongsc-=1; } }</script>";

$did=0;
while(rand(0,1)==0) {
  $x=rand(0,900);
  $y=rand(0,600);
  $k="";
  if(rand(0,1)) $k="2";
  ++$did;
  $dongs.="<div id='dongs{$did}d' style='filter:Alpha(opacity=75,finishopacity=75,style=1);opacity:0.75;-moz-opacity:0.75;position:fixed;top:{$x}px;left:{$y}px;z-index:999'><img src='img/dodongo$k.gif' title=\"BOMB?\" id=\"dongs$did\" onclick=\"bomb('dongs$did',$x,$y)\"></div>";
}
$dongs.="<script language='javascript'>var dongsc=$did;</script>";
if(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE 6.0") !== false) $dongs="";
if(rand(0,100)) $dongs=""; */

?>
