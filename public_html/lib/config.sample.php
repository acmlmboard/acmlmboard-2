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
  $defaultlogo="theme/abII.png";//Replace with the logo of your choice. Note: This is used if a theme doesn't have it's own logo. It is replaced per theme depending on the theme used.
  $boardlogo="<img style='border: 0px' src='$defaultlogo' title=\"$boardtitle\">"; // This defines the logo. Recommended you leave this the default.
  $favicon="theme/fav.png"; //Replace with your favicon of choice
  $defaulttheme="dailycycle2";
  
  $config['log']    = 0;
  $config['ckey']   = "configckey";
  $config['address']   = "url";  //Hostname or IP address of your server (this will be public)
  $config['base']   = "http://".$config['address']; //Replace if you need fine control of the address
  $config['sslbase']= "https://".$config['address']; //Replace if you need fine control of the address
  $config['path']   = "/";
  $config['meta']   = "<meta name='description' content=\"Stuff goes here!\"><meta name='keywords' content=\"Acmlmboard, Your Stuff\">";
  
  //The following settings allow you to enable minipics in various parts of the board. Currently they are specific but they may be simplifed in the future.
  $config['showminipic'] = false; // Show minipics in many generic places that don't need to be specifically controlled.
  $config['userlinkminipic'] = false; // Show minipics in [user=#] and @Name links.
  $config['indexminipic'] = false; // Show minipics on index
  $config['forumminipic'] = false; // Show minipics on forum listing
  $config['startedbyminipic'] = $config['forumminipic']; // Show minipics on forum started by col. (Seperate for testing purposes at this time.. may be merged)
  $config['threadminipic'] = false; // Show minipics in a thread
  $config['enableirc'] = true;

  //This section configures the board's interaction with an IRC bot.
  //You will need to build an interface to your board (see send_to_ircbot() below)
  $config['enableirc'] = false; //Enable to send messages to IRC 
  $config['ircbase'] = "http://".$config['address']."".$config['path'];
  $config['staffchan'] = '#staffchangoeshere PASSWORD';
  $config['pubchan'] = '#pubchangoeshere PASSWORD';
  $config['ircnickprefix'] = true; //Use an IRC prefix when sending messages to the IRC channel.
  $config['ircnickcolor'] = false; //Use the nick color for the whole nick on IRC.
  $config['ircnicksex'] = false; //Use a Color to reflect each user's Sex Color. N/A Defaults nothing.


//The following are optional values you can change to personalize your board
  $config['atnname']  = "News"; // Title of the attention box. It was 'News' on ABII and "Points of Required Attention™" on B2
//The following enables the classic style forum tags. This will possibly be replaced/in addition to user variable
  $config['classictags'] = false;
//This will create a delay between consecutive posts if you have the override perm. This is used exclusively to stop mobile double posting. 
  $config['secafterpost'] = 0; //(in seconds, 0 == off)
//The following enables the optional badge system
  $config['badgesystem'] = false; //This system is currently unfinished.
//IRC Color Defines. Color code numbers, and color names work here.
  $irccolor['base'] = "grey"; //default color for the irc output.
  $irccolor['name'] = "lt_green"; //used most often for usernames and other things that need emphasis.
  $irccolor['title'] = "orange"; // used for thread titles
  $irccolor['url'] = "green"; //used for URLs and for some accents

  $irccolor['yes'] = "green"; //used where you want a clear color for yes/on/good/etc
  $irccolor['no'] = "red"; //used where you want a clear color for no/off/bad/etc
  $irccolor['male'] = "lt_blue"; //Male Nick Color
  $irccolor['female'] = "pink"; //Female Nick Color

// User GFX limits
 $minipicsize=16; // traditionally a square image. $minipicsize x $minipicsize
 $avatardimx=100; // Avatar X Scale
 $avatardimy=100; // Avatar Y Scale
 $avatardimxs=60;
 $avatardimys=60;
 $avatarsize=2*30720;

 $syndromenable=1; //This variable controls the use of "Syndromes".  0 is off; 1 is on
 $inactivedays=30; //The number of days before a user is counted as "inactive"

   // xkeeper 07/15/2007 - adding horrible spatula quotes for fis^H^H^H^H spatula
  $spatulas = array(
  "Value1",
  "Value2",
  );
  $spaturand  = array_rand($spatulas);

  function send_to_ircbot($text,$chan){
    /* While the board does some pre-processing, there is no standard for reporting to IRC from Acmlmboard. This
     * function revives an IRC formatted string (currently includes colors, bold, underline etc) and an output 
     * channel. $chan also includes any channel passwords and this may soon be passed separately. */
	 
   //provide code for post reporting here
  }
?>