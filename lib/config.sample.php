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

  $sqlpass='sqlpassword';
  $sqldb  ='sqldatabase';

  $boardtitle="Board 2";
  $boardlogo="<img style='border: 0px' src='img/board2_banner_generic3_green.png' title=\"ABANDON THE POST\nGET A DRINK\">";
/*  $boardlogo.="<div style=width:0px;height:0px;position:relative>";
  if($lololol || (1230768000-7200-$tzoff-time())<0) {
    $boardlogo.="<img src='gfx/text.php?r1=255&g1=255&b1=255&r2=210&g2=210&b2=210&r3=70&g3=70&b3=70&str=2009+MODE+LOL' style=position:absolute;top:-94px;left:-294px>";
  } else {
    $boardlogo.="<img src='gfx/text.php?r1=255&r2=180&r3=80&g1=80&b1=80&str=T' style=position:absolute;top:-94px;left:-294px>";
    $boardlogo.="<img src='gfx/text.php?b1=255&b2=180&b3=80&g1=130&r1=120&g2=60&r2=60&str=-".strftime("%H:%M:%S",1230768000-7200-$tzoff-time())."' style=position:absolute;top:-94px;left:-286px>";
  }
  $boardlogo.="</div>"; */

  $config[log]=0;
  $config[ckey]="configckey";
  $config[base]="http://url";
  $config[sslbase]="https://url";
  $config[path]="/";
  $config[meta]="<meta name='description' content=\"A forum about ROM hacking, video gaming, life, the universe, maths and everything else. Successor-of-sorts of Acmlm's Board.\">";

//  $randnum	= mt_rand(0,1);
//  if($randnum == 1) $boardlogo="<img src=img/pipeslol.jpg title=\"OMG HOSTILE TAKEOVER\">";
//  elseif($randnum == 2) $boardlogo="<img src=img/pipeslol2.png title=\"GUYS YOU FORGOT SOMETHING\">";
  $extratitle="
".            "         $L[TBL1] width=100% align=center>
".            "            $L[TRh]>
".            "              $L[TDh]><span title=\"Compliant with Adobe's bullshit trademark rules\">Points of Required Attention&trade;</span></td>
".            "            $L[TR2] align=center>
".            "              $L[TDs]>
".            "                <a href=\"thread.php?id=2932\">The Tower of Spatula</a> - More participants are needed.
".            "                <br><b>Summer mosts</b> - <a href=http://acmlm.kafuka.org/board/thread.php?id=3928>Results are in.</a>. 
".            "              </td>
".            "          $L[TBLend]
";


  function sendirc($text){
	//dongs
  }
?>
