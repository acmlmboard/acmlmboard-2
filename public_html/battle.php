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
".      "$L[TBL1]>
".      "  $L[TRh]>
".      "    $L[TDh]>Name</td>
".      "    $L[TDh] width=30>Level</td>
";
  for($i=0;$i<9;$i++)
    print
        "    $L[TDh] width=40>$stat[$i]</td>
";
  for($i=1;$user=$sql->fetch($users);$i++){
    $p=$user[posts];
    $d=(ctime()-$user[regdate])/86400;
    $st=getstats($user,$items);


    $tr=($i%2?'TR2':'TR3').'c';
    print "  $L[$tr]>
".        "    $L[TDl]>".userlink($user)."</td>
".        "    $L[TD1]>$st[lvl]</td>
";
    for($k=0;$k<9;$k++)
      print
          "    $L[TD]>".$st[$stat[$k]]."</td>
";
  }

  print "$L[TBLend]
";

  pagefooter();
?>