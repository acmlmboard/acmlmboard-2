<?php
  require 'lib/common.php';
  pageheader();

  $eqitems=$sql->query('SELECT * FROM items');
  while($item=$sql->fetch($eqitems))
    $items[$item[id]]=$item;

  $users=$sql->query('SELECT *,'.sqlexp().' '
                    .'FROM users u '
                    .'LEFT JOIN usersrpg r ON u.id=r.id '
                    .'WHERE lastact>'.(ctime()-30).' '
//                  .'WHERE lastview>'.(ctime()-86400).' '
                    .'ORDER BY exp DESC');

  print "Currently active users in the battle arena:
".      "<table cellspacing=\"0\" class=\"c1\">
".      "  <tr class=\"h\">
".      "    <td class=\"b h\">Name</td>
".      "    <td class=\"b h\" width=30>Level</td>
";
  for($i=0;$i<9;$i++)
    print
        "    <td class=\"b h\" width=40>$stat[$i]</td>
";
  for($i=1;$user=$sql->fetch($users);$i++){
    $p=$user['posts'];
    $d=(ctime()-$user['regdate'])/86400;
    $st=getstats($user,$items);


    $tr=($i%2?'n2':'n3');
    print "<tr class=\"$tr\" align=\"center\">
".        "    <td class=\"b\" align=\"left\">".userlink($user)."</td>
".        "    <td class=\"b n1\">$st[lvl]</td>
";
    for($k=0;$k<9;$k++)
      print
          "    <td class=\"b\">".$st[$stat[$k]]."</td>
";
  }

  print "</table>
";

  pagefooter();
?>