<?php
  require 'lib/common.php';
  pageheader('Active users');

  $time=$_GET[time];
  checknumeric($time);
  if($time<1)
    $time=86400;

  $query='SELECT u.id,u.posts,regdate,u.name,u.displayname,u.sex,u.power,COUNT(*) num '
        .'FROM users u '
        .'LEFT JOIN posts p ON p.user=u.id '
        .'WHERE p.date>'.(ctime()-$time).' '
        .'GROUP BY u.id ORDER BY num DESC';
  $users=$sql->query($query);

  print 'Active users during the last '.timeunits2($time).":
".      "<br>
".       timelink(3600).'|'.timelink(86400).'|'.timelink(604800).'|'.timelink(2592000)."
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh] width=30>#</td>
".      "    $L[TDh]>Username</td>
".      "    $L[TDh] width=150>Registered on</td>
".      "    $L[TDh] width=50>Posts</td>
".      "    $L[TDh] width=50>Total</td>
";
  $post_total = 0;
  $j=0;
  for($i=1;$user=$sql->fetch($users);$i++){
    $post_total+=$user['num'];
    $tr=($i%2?'TR2':'TR3').'c';
    print
        "  $L[$tr]>
".      "    $L[TD]>$i.</td>
".      "    $L[TDl]>".userlink($user)."</td>
".      "    $L[TD]>".cdate($dateformat,$user[regdate])."</td>
".      "    $L[TD]><b>$user[num]</b></td>
".      "    $L[TD]>$user[posts]</b></td>
";
  $j++;
  }
  print "$L[TRh]>$L[TDh] colspan=5>Totals</td></tr>
".        "  $L[$tr]>
".      "    $L[TD]>$j.</td>
".      "    $L[TDl]></td>
".      "    $L[TD]></td>
".      "    $L[TD]><b>$post_total</b></td>
".      "    $L[TD]>$user[posts]</b></td>
";
  print "$L[TBLend]
";

  pagefooter();

  function timelink($timex){
    global $time;
    return ($time==$timex ? " ".timeunits2($timex)." " : " <a href=activeusers.php?time=$timex>".timeunits2($timex).'</a> ');
  }
?>