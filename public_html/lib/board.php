<?php

  //2007-07-01 blackhole89
  //xkeeper: fadding width/height to make it load better, adding align to move it away from the IP somewhat
  function flagip($ip, $flag = '') {
    if (!$flag) {
	  global $sql; 
	  $flag = $sql->resultq("SELECT cc2 FROM ip2c WHERE ip_from<=inet_aton('{$ip}') AND ip_to>=inet_aton('{$ip}')");
	} 
    return ($flag != '-' ?" <img src=\"img/flags/".strtolower($flag).".png\" style='width: 16px; height: 11px; float: right'>" : "") . $ip . " <small>[<a href='http://google.com/search?q={$ip}'>G</a>]</small>";
  }

  function feedicon($icon,$para,$text="RSS feed"){
    return "<a href='$para'><img src='$icon' border='0' style='margin-right:5px' title='$text'></a>"
          ."<link rel='alternate' type='application/rss+xml' title='$text' href='$para'>";
  }

?>