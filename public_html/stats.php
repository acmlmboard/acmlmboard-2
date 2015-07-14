<?php
  require 'lib/common.php';
  pageheader('Stats');

  $tstats=$sql->query('SHOW TABLE STATUS');
  while($t=$sql->fetch($tstats))
    $tbl[$t[Name]]=$t;

  function sp($sz){
    $b=number_format($sz,0,'.',' ');
    return $b;
  }
  function tblinfo($n){
    global $tbl;
    $t=$tbl[$n];
    return
        "  <tr align=\"right\">
".      "    <td class=\"b n1\" align=\"left\">$t[Name]</td>
".      "    <td class=\"b n2\">$t[Rows]</td>
".      "    <td class=\"b n2\">".sp($t[Avg_row_length])."</td>
".      "    <td class=\"b n2\">".sp($t[Data_length])."</td>
".      "    <td class=\"b n2\">".sp($t[Index_length])."</td>
".      "    <td class=\"b n2\">".sp($t[Data_free])."</td>
".      "    <td class=\"b n2\">".sp($t[Data_length]+$t[Index_length])."
";
  }

  $fields=array('maxpostsday','maxpostshour','maxpostsdaydate','maxpostshourdate','maxusers','maxusersdate');
  foreach($fields as $field)
    $$field=$sql->resultq("SELECT intval FROM misc WHERE field='$field'");
  $maxuserstext=$sql->resultq('SELECT txtval FROM misc WHERE field="maxuserstext"');

  print "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\" width=180>Records</td>
".      "    <td class=\"b h\">&nbsp;</td>
".      "  <tr>
".      "    <td class=\"b n1\">Most posts within 24 hours:</td>
".      "    <td class=\"b n2\">$maxpostsday, on ".cdate($dateformat,$maxpostsdaydate)."</td>
".      "  <tr>
".      "    <td class=\"b n1\">Most posts within 1 hour:</td>
".      "    <td class=\"b n2\">$maxpostshour, on ".cdate($dateformat,$maxpostshourdate)."</td>
".      "  <tr>
".      "    <td class=\"b n1\">Most users online:</td>
".      "    <td class=\"b n2\">$maxusers, on ".cdate($dateformat,$maxusersdate).": $maxuserstext</td>
".      "</table>
".      "<br>
".      "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\" width=16%>Table name</td>
".      "    <td class=\"b h\" width=14%>Rows</td>
".      "    <td class=\"b h\" width=14%>Avg. data/row</td>
".      "    <td class=\"b h\" width=14%>Data size</td>
".      "    <td class=\"b h\" width=14%>Index size</td>
".      "    <td class=\"b h\" width=14%>Unused data</td>
".      "    <td class=\"b h\" width=14%>Total size</td>
".       tblinfo('poststext')."
".       tblinfo('posts')."
".       tblinfo('threads')."
".       tblinfo('users')."
".       tblinfo('pmsgs')."
".       tblinfo('pmsgstext')."
".      "</table>
".      "<br>
".      "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\" colspan=9>Daily stats</td>
".      "  <tr class=\"c\">
".      "    <td class=\"b\">Date</td>
".      "    <td class=\"b\">Total users</td>
".      "    <td class=\"b\">Total posts</td>
".      "    <td class=\"b\">Total threads</td>
".      "    <td class=\"b\">Total views</td>
".      "    <td class=\"b\">New users</td>
".      "    <td class=\"b\">New posts</td>
".      "    <td class=\"b\">New threads</td>
".      "    <td class=\"b\">New views</td>
";

//".       tblinfo('pmsgs_text')."
//".       tblinfo('pmsgs')."
//".       tblinfo('postlayouts')."
//".       tblinfo('poll')."
//".       tblinfo('poll_choices')."
//".       tblinfo('pollvotes')."
//".       tblinfo('announcements')."
//".       tblinfo('forumread')."
//".       tblinfo('userratings')."
//".       tblinfo('postradar')."
//".       tblinfo('favorites')."

  $users=0;
  $posts=0;
  $threads=0;
  $views=0;
  $stats=$sql->query('SELECT * FROM dailystats');
  while($day=$sql->fetch($stats)){
    print
        "  <tr align=\"center\">
".      "    <td class=\"b n1\">$day[date]</td>
".      "    <td class=\"b n2\">$day[users]</td>
".      "    <td class=\"b n2\">$day[posts]</td>
".      "    <td class=\"b n2\">$day[threads]</td>
".      "    <td class=\"b n2\">$day[views]</td>
".      "    <td class=\"b n2\">".($day[users]-$users)."</td>
".      "    <td class=\"b n2\">".($day[posts]-$posts)."</td>
".      "    <td class=\"b n2\">".($day[threads]-$threads)."</td>
".      "    <td class=\"b n2\">".($day[views]-$views)."</td>
";
    $users=$day[users];
    $posts=$day[posts];
    $threads=$day[threads];
    $views=$day[views];
  }
  print "</table>";

  pagefooter();
?>
