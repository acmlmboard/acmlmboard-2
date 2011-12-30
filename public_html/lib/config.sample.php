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
  $pwdsalt='Please change me!';

  $sqlpass='sqlpassword';
  $sqldb  ='sqldatabase';

  $boardtitle="Insert title here";
  //$boardlogo="<img style='border: 0px' src='img/logo.png' title=\"ABANDON THE POST\nGET A DRINK\">";
  $defaultlogo="theme/abII.png";//Replace with the logo of your choice. Note: This is used if a theme doesn't have it's own logo. It is replaced per theme depending on the theme used.
  $favicon="theme/fav.png"; //Replace with your favicon of choice
  $defaulttheme="dailycycle2";
  
  $config[log]    = 0;
  $config[ckey]   = "configckey";
  $config[address]   = "url";  //Hostname or IP address of your server (this will be public)
  $config[base]   = "http://$config[address]"; //Replace if you need fine control of the address
  $config[sslbase]= "https://$config[address]"; //Replace if you need fine control of the address
  $config[path]   = "/";
  $config[meta]   = "<meta name='description' content=\"Stuff goes here!\">";

// User GFX limits
 $minipicsize=16; // traditionally a square image. $minipicsize x $minipicsize
 $avatardimx=100; // Avatar X Scale
 $avatardimy=100; // Avatar Y Scale
 $avatardimxs=60;
 $avatardimys=60;
 $avatarsize=2*30720;

   // xkeeper 07/15/2007 - adding horrible spatula quotes for fis^H^H^H^H spatula
  $spatulas = array(
  "Value1",
  "Value2",
  );
  $spaturand  = array_rand($spatulas);

  function sendirc($text){
	//provide code for post reporting here
  }
?>
