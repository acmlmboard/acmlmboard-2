<?php
  require 'lib/common.php';
  pageheader('IP bans');
  $action=$_GET['action'];//Stop gap to allow esiting without major changes. -Emuz
  $what=$_GET['what'];//Ditto -Emuz
  function ipfmt($a) {
    $expl=explode(".",$a);
    $dot="<font~color=#808080>.</font>";
    return str_replace("~"," ",str_replace(" ","&nbsp;",sprintf("%3s%s%3s%s%3s%s%3s",$expl[0],$dot,$expl[1],$dot,$expl[2],$dot,$expl[3])));
  }

  if(!has_perm('edit-ip-bans')) {
    noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>"); 
    pagefooter(); 
    die();
  } else {
    if($action=="del")
    {
      $data=explode(",",decryptpwd($what));
      $sql->query("DELETE FROM ipbans WHERE ipmask='$data[0]' AND expires='$data[1]'");
    } else if($action=="add") {
      if($_POST[ipmask]) {
      $sql->query("INSERT INTO ipbans (ipmask,hard,expires,banner,reason) VALUES "
                 ."('$_POST[ipmask]','$_POST[hard]','".($_POST[expires]>0?($_POST[expires]+time()):0)."','".addslashes($loguser[name])."','$_POST[reason]')");
      } else {
      $err="You must enter an IP mask";
      }
    }
    $ipbans=$sql->query("SELECT * FROM ipbans");
    if($err) noticemsg("Error", $err);
    echo "<form action=ipbans.php?action=add method=post>
".       "<table cellspacing=\"0\" class=\"c1\">
".       "  <tr class=\"h\">
".       "    <td class=\"b h\" colspan=9>New IP ban</td>
".       "  <tr>
".       "    <td class=\"b n1\">&nbsp;IP&nbsp;mask&nbsp;
".       "    <td class=\"b n2\"><input type=\"text\" name=ipmask>
".       "    <td class=\"b n1\">&nbsp;Hard&nbsp;ban?&nbsp;
".       "    <td class=\"b n2\"><input type=\"checkbox\" name=hard value=1>
".       "    <td class=\"b n1\">&nbsp;Expires?&nbsp;
".       "    <td class=\"b n2\">".fieldselect("expires",0,array("600"=>"10 minutes",
						      "3600"=>"1 hour",
						      "10800"=>"3 hours",
						      "86400"=>"1 day",
						      "172800"=>"2 days",
						      "259200"=>"3 days",
						      "604800"=>"1 week",
						      "1209600"=>"2 weeks",
						      "2419200"=>"1 month",
						      "4838400"=>"2 months",
						      "0"=>"never"))."
".       "    <td class=\"b n1\">&nbsp;Comment&nbsp;
".       "    <td class=\"b n2\" style=width:100%><input type=\"text\" name=reason style=width:100%>
".       "    <td class=\"b n2\" align=\"center\" colspan=8><input type=\"submit\" class=\"submit\" name value='Add IP ban'>
".       "</table></form><br>
".       "<table cellspacing=\"0\" class=\"c1\">
".       "  <tr class=\"h\">
".       "    <td class=\"b h\" colspan=6>IP bans</td>
".       "  <tr class=\"c\">
".       "    <td class=\"b\">IP mask</td>
".       "    <td class=\"b\">hard?</td>
".       "    <td class=\"b\">Expires</td>
".       "    <td class=\"b\">Banner</td>
".       "    <td class=\"b\" width=100%>Comment</td>
".       "    <td class=\"b\">Actions
";
    while($i=$sql->fetch($ipbans)) {
      echo "<tr>
".         "  <td class=\"b n1\"><font face='courier new'>".ipfmt($i[ipmask])."</font>
".         "  <td class=\"b n2\" align=\"center\"><font color=".($i[hard]?"red>Yes":"green>No")."</font>
".         "  <td class=\"b n2\" align=\"center\">".($i[expires]?
		 cdate($loguser[dateformat],$i[expires])."&nbsp;".cdate($loguser[timeformat],$i[expires])
		:"never")."
".         "  <td class=\"b n2\" align=\"center\">$i[banner]
".         "  <td class=\"b n2\">".stripslashes($i[reason])."
".         "  <td class=\"b n2\" align=\"center\"><a href=ipbans.php?action=del&what=".urlencode(encryptpwd($i[ipmask].",".$i[expires])).">del</a>
";
    }
    echo "</table>";
  }


  pagefooter();
?>
