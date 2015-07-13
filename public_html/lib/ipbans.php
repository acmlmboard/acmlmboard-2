<?php
  //ipbans.php - core for new IP ban functions, started 2007-02-19 // blackhole89

  //delete expired IP bans
  $sql->query('DELETE FROM ipbans WHERE expires<'.ctime().' AND expires>0');

  //actual ban checking
  $r=$sql->query("SELECT * FROM ipbans WHERE '$userip' LIKE ipmask");
  if(@$sql->numrows($r)>0)
  {

    // report the IP as banned like before
    if ($loguser) $sql -> query("UPDATE `users` SET `ipbanned` = '1' WHERE `id` = '$loguser[id]'");
    else $sql -> query("UPDATE  `guests` SET `ipbanned` = '1' WHERE `ip` = '". $_SERVER['REMOTE_ADDR'] ."'");

    $bannedgroup = $sql->resultq("SELECT id FROM `group` WHERE `banned`=1");

    //a ban appears to be present. check for type
    //and restrict user's access if necessary
    $i=$sql->fetch($r);
    if($i[hard])
    {
      //hard IP ban; always restrict access fully

//	  header("Location: http://banned.ytmnd.com/");
//	  header("Location: http://board.acmlm.org/");
	  // fuck this shit
	  
      pageheader('IP banned');
      print
          "<table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"n2\">
".        "    <td class=\"b n1\" align=\"center\">
".        "      Sorry, but your IP address appears to be banned from this board.
".        "</table>
";
      pagefooter();
      die();
	  
    } else if(!$i[hard] && (!$log || $loguser[group_id]==$bannedgroup[id])) {
      //"soft" IP ban allows non-banned users with existing accounts to log on
      if(!strstr($_SERVER['PHP_SELF'],"login.php"))
      {
        pageheader('IP restricted');
        print
          "<table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"n2\">
".        "    <td class=\"b n1\" align=\"center\">
".        "      Access from your IP address to this board appears to be limited.<br>
".        "      <A HREF=login.php>Login</A>
".        "</table>
";
        pagefooter();
        die();
      }
    }
  }
?>