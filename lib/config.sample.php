<?php

  function hex2str($hex)
  {
    for($i=0;$i<strlen($hex);$i+=2)
    {
      $str.=chr(hexdec(substr($hex,$i,2)));
    }
    return $str;
  }

  $trashid=20;
  $sqlhost='localhost';
  $sqluser='sqlusername';

  $spritesalt='please change me!';

  $sqlpass='sqlpassword';
  $sqldb  ='sqldatabase';

  $boardtitle="Insert title here";
  $boardlogo="<img style='border: 0px' src='img/logo.png' title=\"ABANDON THE POST\nGET A DRINK\">";
  $favicon="/fav.png"; //Replace with your favicon of choice
  $defaulttheme="dailycycle2";
  
  $config[log]    = 0;
  $config[ckey]   = "configckey";
  $config[base]   = "http://url";
  $config[sslbase]= "https://url";
  $config[path]   = "/";
  $config[meta]   = "<meta name='description' content=\"A forum about ROM hacking, video gaming, life, the universe, maths and everything else. Successor-of-sorts of Acmlm's Board.\">";

// User GFX limits
 $minipicsize=16; // traditionally a square image. $minipicsize x $minipicsize
 $avatardimx=100; // Avatar X Scale
 $avatardimy=100; // Avatar Y Scale
 $avatardimxs=60;
 $avatardimys=60;
 $avatarsize=2*30720;

  function sendirc($text){
	//provide code for post reporting here
  }
?>
