<?php
  require 'lib/common.php';
  pageheader('Active users');

  $time=$_GET[time];
  checknumeric($time);
  if($time<1)
    $time=86400;

  $query='SELECT '.userfields('u').',u.posts,u.regdate,COUNT(*) num '
        .'FROM users u '
        .'LEFT JOIN posts p ON p.user=u.id '
        .'WHERE p.date>'.(ctime()-$time).' '
        .'GROUP BY u.id ORDER BY num DESC';
  $users=$sql->query($query);

  print 'Active users during the last '.timeunits2($time).":
".      "<br>
".       timelink(3600).'|'.timelink(86400).'|'.timelink(604800).'|'.timelink(2592000)."
".      "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\" width=30>#</td>
".      "    <td class=\"b h\">Username</td>
".      "    <td class=\"b h\" width=150>Registered on</td>
".      "    <td class=\"b h\" width=50>Posts</td>
".      "    <td class=\"b h\" width=50>Total</td>
";
  $post_total = 0;
  $post_overall = 0;
  $j=0;
  $tr = 'n3';
  for($i=1;$user=$sql->fetch($users);$i++){
    $post_total+=$user['num'];
    $post_overall+=$user['posts'];
    $tr=($i % 2 ? 'n2': 'n3');
    print
        "<tr class=\"$tr\" align=\"center\">
".      "    <td class=\"b\">$i.</td>
".      "    <td class=\"b\" align=\"left\">".userlink($user)."</td>
".      "    <td class=\"b\">".cdate($dateformat,$user[regdate])."</td>
".      "    <td class=\"b\"><b>$user[num]</b></td>
".      "    <td class=\"b\">$user[posts]</b></td>
";
  $j++;
  }
  print "<tr class=\"h\"><td class=\"b h\" colspan=5>Totals</td></tr>
".        "<tr class=\"$tr\" align=\"center\">
".      "    <td class=\"b\"><b>$j.</b></td>
".      "    <td class=\"b\" align=\"left\"></td>
".      "    <td class=\"b\"></td>
".      "    <td class=\"b\"><b>$post_total</b></td>
".      "    <td class=\"b\"><b>$post_overall</b></td>
";
  print "</table>
";

  pagefooter();

  function timelink($timex){
    global $time;
    return ($time==$timex ? " ".timeunits2($timex)." " : " <a href=activeusers.php?time=$timex>".timeunits2($timex).'</a> ');
  }
?>