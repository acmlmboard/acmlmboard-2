<?php

/*** Acmlmboard 2 - Configuration ***
  Please look through the file and will in the apropriate information.
  For the settings that call SQL resultp query functions, 
  Please look into the Administrator Tools page on your board to configure them.
*/

  $sqlhost='localhost';
  $sqluser='sqlusername';

  $spritesalt='please change me!';
  $pwdsalt='Please change me!';
  $pwdsalt2='Addtional Salt. Please Change me!';

  $sqlpass='sqlpassword';
  $sqldb  ='sqldatabase';


  $config['sqlconfig'] = true; //Configure settings through Administrator Tools and the misc table.

  $trashid=2; // Designates the id for your trash forum. 

  $boardtitle="Insert title here";// This is what will be displayed at the top of your browser window.
  $defaultlogo="theme/abII.png";// Replace with the logo of your choice. Note: This is used if a theme doesn't have it's own logo. It is replaced per theme depending on the theme used.
  $boardlogo="<img style='border: 0px' src='$defaultlogo' title=\"$boardtitle\">"; // This defines the logo. Recommended you leave this the default.
  $favicon="theme/fav.png";// Replace with your favicon of choice
  $defaulttheme="dailycycle2";// Select the default theme to be used. This will also be showed to users who are not logged in.
  $defaultfontsize=70;// Overall font scale of the board. Default is 70%
  $homepageurl="http://something/"; // This defines the link for the header image.

  /*Registeration Bot Protection *
  Currently the default protection is a simple passphrase with question array used to pull random questions.
  The way it is coded you could easily replace it with something stronger, or even simpler. 
  The register.php take $puzzleAnswer and $puzzle. Feel free to write something around it. */
  
  $puzzleAnswer = 12;//This should be changed
  //$puzzleAnswer = "Sekrit Key!";// This can also be a string
  $puzzleVariations = array(
    "What is six times two?",
    "What is twenty four minus fourteen plus two?",
    "What is the square of 4 minus four?",
    "What is six plus six?",
    "What is ten thousand twenty four divided by sixteen minus fifty two?",
    "What is twelve times one?",
  );//This also should be changed. It has to match $puzzleAnswer in this example.
  $puzzle = $puzzleVariations[array_rand($puzzleVariations)]; 
  
  $config['log']    = 0; // Enables logging to the database of moderator & administrative actions. 0=off; 1=profile; 2=thread & post; 5=access
  $config['ckey']   = "configckey";
  $config['address']   = "url";  // Hostname or IP address of your server (this will be public)
  $config['base']   = "http://".$config['address']; // Replace if you need fine control of the address
  $config['sslbase']= "https://".$config['address']; // Replace if you need fine control of the address
  $config['path']   = "/";// If you run your copy in a specific path (ie: http://www.example.gov/board) than this would be 'board/''
  $config['meta']   = "<meta name='description' content=\"Stuff goes here!\"><meta name='keywords' content=\"Acmlmboard, Your Stuff\">";// This is used for search engine keywords.
  $config['showssl'] = false; // Shows a link/icon to allow a user to switch to ssl. Enable if you are using on a https server.

/* -- Everything past this point is optional.  It is recommended to get the board up and running first before adjusting the following                  --
   -- The amount of options may be overwelming at first. AB 2.5+ was designed to allow for great flexiblity. As such there are many optional features. -- */

 // User GFX limits
  $minipicsize=16;// traditionally a square image. $minipicsize x $minipicsize. (AB1/2's Default was 11x11)
  $avatardimx=180;// Avatar X Scale
  $avatardimy=180;// Avatar Y Scale
  $avatardimxs=60;// Avatar X Scale (Scaled Down) **CURRENTLY DISABLED**
  $avatardimys=60;// Avatar Y Scale (Scaled Down) **CURRENTLY DISABLED**
  $avatarsize=2*30720;// The Avatar size in bytes. The default is to 60kb.

  // The following settings allow you to enable minipics in various parts of the board. Currently they are specific but they may be simplifed in the future.
  $config['showminipic'] = false; // Show minipics in many generic places that don't need to be specifically controlled.
  $config['userlinkminipic'] = false; // Show minipics in [user=#] and @Name links.
  $config['indexminipic'] = false; // Show minipics on index
  $config['forumminipic'] = false; // Show minipics on forum listing
  $config['startedbyminipic'] = $config['forumminipic']; // Show minipics on forum started by col. (Seperate for testing purposes at this time.. may be merged)
  $config['threadminipic'] = false; // Show minipics in a thread

  //The following settings allow a board owner to override a board's theme and logo for special events, etc.
  $config['override_theme'] = "";// If you want to lock everyone to a specific theme.
  $config['override_logo'] = "";// If you want to replace the logo on all themes. 

  $syndromenable=1; //This variable controls the use of "Syndromes".  0 is off; 1 is on
  $inactivedays=30; //The number of days before a user is counted as "inactive"

  //The following sectionis related to guests (mostly reflected on online.php) 
  $config['oldguest']=300; //Number of seconds before a guest is deleted due to being "old"

  //This will create a delay between consecutive posts if you have the override perm. This is used exclusively to stop mobile double posting. 
  $config['secafterpost'] = 5; //(in seconds, 0 == off)
  //This will allow you to set the goal limits for 'Projected date' in profile.php
  $config['topposts'] = '5000'; //Number of posts to set the goal to.
  $config['topthreads'] = '200'; //Number of threads created to set the goal to.

  $config['threadprevnext'] = false; //Enables links to jump one thread newer/older
  $config['memberlistcolorlinks'] = false; //Toggles the use of more color in memberlist.php. Group links will use respective colors to gender searched.
  $config['registrationpuzzle'] = true;
  //The following enables the optional badge system
  $config['badgesystem'] = false; //The badge system allows you to assign graphic 'badges' to users. They can be set to trigger board effects!
  $config['spritesystem'] = true; //The sprite system allows users to catch graphic 'sprites' on the board. Collect them all!
  
  $config['extendedprofile'] = false; //This feature allows for an unlimited about of profile fields in a user's profile *DO NOT USE EXPEREMENTAL*

  $config['threadprevnext'] = false; //Enables a set of links on thread pages that allows you to go to the next or previous 'new' thread.

  $config['rootuseremail'] = false; //Enable the Root Administrator's email to be shown in the No Permission page to a user who can edit other users but not Root Administrators.
  $config['displayname'] = false; //Enable the use of the "Display Name" System. (allows a second name to be used instead of the User's)
  $config['perusercolor'] = false; //Enable the use of per-user colors.
  $config['usernamebadgeeffects'] = false; //Allows badges to change username colors Requires badge system.
  $config['useshadownccss'] = false; //Enables use of a CSS class name to all user names (and other elements) that should get a shadow on light color themes
  $config['nickcolorcss'] = false; //Enables use of CSS to define per theme colors via a span id. Note: You may need to customise CSS to fit your board groups. 

  //The following are related to how RPG elements are displayed. All these options allow for legacy AB1 RPG style and elements
  $config['userpgnum'] = false; //Enables 'graphical' RPG stats in user's sidebars when themes have images incorporated in the themes/themename/rpg/ dir.
  $config['userpgnumdefault'] = false; //Shows a default imageset on all themes. If $config['userpgnum'] is enabled the theme images will still show.
  $config['alwaysshowlvlbar'] = false; //Enable this to always show the exp bar. This allows you to use it without having to have the rest of the system.
  $config['rpglvlbarwidth'] = 96; // Set the size of the exp bar. Acmlmboard 1.x has a default of 56. 

  //This section configures the board's interaction with an IRC bot.
  //You will need to build an interface to your board (see send_to_ircbot() below)
  $config['enableirc'] = false; //Enable to send messages to IRC 
  $config['ircbase'] = "http://".$config['address']."".$config['path'];
  $config['staffchan'] = '#staffchangoeshere PASSWORD';
  $config['pubchan'] = '#pubchangoeshere PASSWORD';
  $config['ircnickprefix'] = true; //Use an IRC prefix when sending messages to the IRC channel.
  $config['ircnickcolor'] = false; //Use the nick color for the whole nick on IRC.
  $config['ircnicksex'] = false; //Use a Color to reflect each user's Sex Color. N/A Defaults nothing.

//IRC Color Defines. Color code numbers, and color names work here.
  $irccolor['base'] = "grey"; //default color for the irc output.
  $irccolor['name'] = "lt_green"; //used most often for usernames and other things that need emphasis.
  $irccolor['title'] = "orange"; // used for thread titles
  $irccolor['url'] = "green"; //used for URLs and for some accents

  $irccolor['yes'] = "green"; //used where you want a clear color for yes/on/good/etc
  $irccolor['no'] = "red"; //used where you want a clear color for no/off/bad/etc
  $irccolor['male'] = "lt_blue"; //Male Nick Color
  $irccolor['female'] = "pink"; //Female Nick Color

//IRC Message Definitions
  $config['ircshopnotice'] = false; //enables the option to print on your main irc channel anytime someone equips an item

//The following are optional values you can change to personalize your board
  $config['atnname']  = "News"; // Title of the attention box. It was 'News' on ABII and "Points of Required Attention™" on B2

//The following enables the classic style forum tags. This will possibly be replaced/in addition to user variable
  $config['classictags'] = false;
  
  //These two settings allow you to choose how user will be notfied about PMs. The "classic" Index table method, the "modern" Icon style, or Both
  //Note: You must have one or both enabled for you to have any PM info displayed to the user.
  $config['classicpms'] = false; //Enables the classic table style PM notification
  $config['disablemodernpms'] = false;//**DISABLES** the modern style system. The modern style is the supported method for PM notification.

  // these are fallback settings
  $config['trashid'] = $trashid;
  $config['boardtitle'] = $boardtitle;
  $config['defaulttheme'] = $defaulttheme;
  $config['defaultfontsize'] = $defaultfontsize;
  $config['avatardimx'] = $avatardimx;
  $config['avatardimy'] = $avatardimy;
  $config['avatardimy'] = $avatardimy;


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