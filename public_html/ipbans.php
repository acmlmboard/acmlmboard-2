<?php
  require 'lib/common.php';
  pageheader('IP bans');

  function ipfmt($a) {
    $expl=explode(".",$a);
    $dot="<font~color=#808080>.</font>";
    return str_replace("~"," ",str_replace(" ","&nbsp;",sprintf("%3s%s%3s%s%3s%s%3s",$expl[0],$dot,$expl[1],$dot,$expl[2],$dot,$expl[3])));
  }

  if(!has_perm('edit-ip-bans')) {
    no_perm();
  } else {
    if($action=="del")
    {
      $data=explode(",",decryptpwd($what));
      $sql->query("DELETE FROM ipbans WHERE ipmask='$data[0]' AND expires='$data[1]'");
    } else if($action=="add" && $_POST[ipmask] ) {
      $sql->query("INSERT INTO ipbans (ipmask,hard,expires,banner,reason) VALUES "
                 ."('$_POST[ipmask]','$_POST[hard]','".($_POST[expires]>0?($_POST[expires]+time()):0)."','".addslashes($loguser[name])."','$_POST[reason]')");
    }
    $ipbans=$sql->query("SELECT * FROM ipbans");
    echo "<form action=ipbans.php?action=add method=post>
".       "$L[TBL1]>
".       "  $L[TRh]>
".       "    $L[TDh] colspan=9>New IP ban</td>
".       "  $L[TR]>
".       "    $L[TD1]>&nbsp;IP&nbsp;mask&nbsp;
".       "    $L[TD2]>$L[INPt]=ipmask>
".       "    $L[TD1]>&nbsp;Hard&nbsp;ban?&nbsp;
".       "    $L[TD2]>$L[INPc]=hard value=1>
".       "    $L[TD1]>&nbsp;Expires?&nbsp;
".       "    $L[TD2]>".fieldselect("expires",0,array("600"=>"10 minutes",
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
".       "    $L[TD1]>&nbsp;Comment&nbsp;
".       "    $L[TD2] style=width:100%>$L[INPt]=reason style=width:100%>
".       "    $L[TD2c] colspan=8>$L[INPs] value='Add IP ban'>
".       "$L[TBLend]</form><br>
".       "$L[TBL1]>
".       "  $L[TRh]>
".       "    $L[TDh] colspan=6>IP bans</td>
".       "  $L[TRg]>
".       "    $L[TD]>IP mask</td>
".       "    $L[TD]>hard?</td>
".       "    $L[TD]>Expires</td>
".       "    $L[TD]>Banner</td>
".       "    $L[TD] width=100%>Comment</td>
".       "    $L[TD]>Actions
";
    while($i=$sql->fetch($ipbans)) {
      echo "$L[TR]>
".         "  $L[TD1]><font face='courier new'>".ipfmt($i[ipmask])."</font>
".         "  $L[TD2c]><font color=".($i[hard]?"red>Yes":"green>No")."</font>
".         "  $L[TD2c]>".($i[expires]?
		 cdate($loguser[dateformat],$i[expires])."&nbsp;".cdate($loguser[timeformat],$i[expires])
		:"never")."
".         "  $L[TD2c]>$i[banner]
".         "  $L[TD2]>".stripslashes($i[reason])."
".         "  $L[TD2c]><a href=ipbans.php?action=del&what=".urlencode(encryptpwd($i[ipmask].",".$i[expires])).">del</a>
";
    }
    echo "$L[TBLend]";
  }


  pagefooter();
?>
