<?php
  require 'lib/common.php';
    pageheader('Mood avatars');

    $a=$sql->query("SELECT users.* FROM mood,users WHERE users.id=mood.user GROUP BY users.id ORDER BY users.id ASC");

    print "Mood avatars:
".        "<table cellspacing=\"0\" class=\"c1\">
".        "  <tr class=\"h\">
".        "    <td class=\"b h\" width=30>ID</td>
".        "    <td class=\"b h\" width=300>Username</td>
".        "    <td class=\"b h\">Mood avatars</td>
";

    for($i=1;$m=$sql->fetch($a);$i++){
      $tr = ($i % 2 ? 'n2' :'n3');
      print
          "<tr class=\"$tr\" align=\"center\">
".        "    <td class=\"b\">$m[id].</td>
".        "    <td class=\"b\" align=\"left\">".userlink($m)."</td>
".        "    <td class=\"b\">
";
      $b=$sql->query("SELECT * FROM mood WHERE user=$m[id]");
      while($n=$sql->fetch($b)) echo "<a href=\"usermood.php?a=e&i=$n[id]&uid=$n[user]\"</a><img src='gfx/userpic.php?id=$n[user]_$n[id]' title='$n[label]'></a>";
    }
    print "</table>
";
//  }
  pagefooter();

?>
