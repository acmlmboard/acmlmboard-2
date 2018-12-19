<?php
  require 'lib/common.php';
  pageheader('Stats');

  $tstats=$sql->query('SHOW TABLE STATUS');
  while($t=$sql->fetch($tstats))
    $tbl[$t['Name']]=$t;

  function sp($sz){
    $b=number_format($sz,0,'.',' ');
    return $b;
  }
  function tblinfo($n){
    global $tbl,$L;
    $t=$tbl[$n];
    return
        "  $L[TRr]>
".      "    $L[TD1l]>{$t['Name']}</td>
".      "    $L[TD2]>{$t['Rows']}</td>
".      "    $L[TD2]>".sp($t['Avg_row_length'])."</td>
".      "    $L[TD2]>".sp($t['Data_length'])."</td>
".      "    $L[TD2]>".sp($t['Index_length'])."</td>
".      "    $L[TD2]>".sp($t['Data_free'])."</td>
".      "    $L[TD2]>".sp($t['Data_length']+$t['Index_length'])."
";
  }

  $rec = $sql->fetchq("SELECT maxpostsday, maxpostshour, maxpostsdaydate, maxpostshourdate, maxusers, maxusersdate, maxuserstext FROM misc");

  print "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=180>Records</td>
".      "    $L[TDh]>&nbsp;</td>
".      "  $L[TR]>
".      "    $L[TD1]>Most posts within 24 hours:</td>
".      "    $L[TD2]>{$rec['maxpostsday']}, on ".cdate($dateformat,$rec['maxpostsdaydate'])."</td>
".      "  $L[TR]>
".      "    $L[TD1]>Most posts within 1 hour:</td>
".      "    $L[TD2]>{$rec['maxpostshour']}, on ".cdate($dateformat,$rec['maxpostshourdate'])."</td>
".      "  $L[TR]>
".      "    $L[TD1]>Most users online:</td>
".      "    $L[TD2]>{$rec['maxusers']}, on ".cdate($dateformat,$rec['maxusersdate']).": {$rec['maxuserstext']}</td>
".      "$L[TBLend]
".      "<br>
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=16%>Table name</td>
".      "    $L[TDh] width=14%>Rows</td>
".      "    $L[TDh] width=14%>Avg. data/row</td>
".      "    $L[TDh] width=14%>Data size</td>
".      "    $L[TDh] width=14%>Index size</td>
".      "    $L[TDh] width=14%>Unused data</td>
".      "    $L[TDh] width=14%>Total size</td>
".       tblinfo('poststext')."
".       tblinfo('posts')."
".       tblinfo('threads')."
".       tblinfo('users')."
".       tblinfo('pmsgs')."
".       tblinfo('pmsgstext')."
".      "$L[TBLend]
".      "<br>
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] colspan=9>Daily stats</td>
".      "  $L[TRg]>
".      "    $L[TD]>Date</td>
".      "    $L[TD]>Total users</td>
".      "    $L[TD]>Total posts</td>
".      "    $L[TD]>Total threads</td>
".      "    $L[TD]>Total views</td>
".      "    $L[TD]>New users</td>
".      "    $L[TD]>New posts</td>
".      "    $L[TD]>New threads</td>
".      "    $L[TD]>New views</td>
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
        "  $L[TRc]>
".      "    $L[TD1]>{$day['date']}</td>
".      "    $L[TD2]>{$day['users']}</td>
".      "    $L[TD2]>{$day['posts']}</td>
".      "    $L[TD2]>{$day['threads']}</td>
".      "    $L[TD2]>{$day['views']}</td>
".      "    $L[TD2]>".($day['users']-$users)."</td>
".      "    $L[TD2]>".($day['posts']-$posts)."</td>
".      "    $L[TD2]>".($day['threads']-$threads)."</td>
".      "    $L[TD2]>".($day['views']-$views)."</td>
";
    $users=$day['users'];
    $posts=$day['posts'];
    $threads=$day['threads'];
    $views=$day['views'];
  }
  print "</table>";

  pagefooter();
  