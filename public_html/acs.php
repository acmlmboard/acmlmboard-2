<?php
  require 'lib/common.php';
  pageheader('Active users (server time day blocks)');

  $time = $_GET['time'];
  $past = $_GET['past'];
  $easymode = $_GET['easymode'];
  checknumeric($time);
  checknumeric($past);
  if($time < 1)
    $time = 86400;

  function dtime() {
    return ctime()+7200;
  }

  $query = "SELECT posts,regdate,".userfields().",SUM(num) num FROM (
          SELECT u.posts,regdate,".userfields('u').",CASE WHEN COUNT(*) >10 THEN 10 ELSE COUNT(*) END num 
          FROM users u 
          LEFT JOIN posts p ON p.user=u.id 
          LEFT JOIN threads t ON t.id=p.thread 
          LEFT JOIN forums f ON f.id=t.forum 
          WHERE p.date>".(ctime() - (dtime()%86400) - $past * 86400)." AND p.date < ".(ctime() - (dtime()%86400) - ($past-1) * 86400)." 
          AND f.private=0 AND p.deleted=0 
          GROUP BY p.thread,u.id) inter GROUP BY id ORDER BY num DESC";
  if(isset($easymode)) {
    $query = "SELECT u.posts,regdate,".userfields('u').",COUNT(*) num
          FROM users u
          LEFT JOIN posts p ON p.user=u.id
          LEFT JOIN threads t ON t.id=p.thread
          LEFT JOIN forums f ON f.id=t.forum
          WHERE p.date>".(ctime() - (dtime()%86400) - $past * 86400)." AND p.date < ".(ctime()-(dtime()%86400)-($past-1)*86400)."
          AND f.private=0 AND p.deleted=0
          GROUP BY u.id ORDER BY num DESC";
  }
  $users = $sql->query($query);

  print "<form action=\"\" method=\"get\">
        <table cellspacing=\"0\" class=\"c1\">
          <tr>
             <td class=\"b n2\"><input type=\"submit\" value=\"Show\">: <input type=\"text\" name=\"past\" size=\"3\" maxlength=\"4\" value=\"$past\"> days ago
                    | <input type=\"checkbox\" name=\"easymode\" id=\"emode\" ".(isset($easymode)?"checked":"")."><label for=\"emode\" title=\"Without this, at most 5 posts per day in any single thread are counted.\">EASY MODE</label>
        </table></form>";

  print "Active users on ".cdate($loguser['dateformat'], ctime() - $past * 86400).":
         <table cellspacing=\"0\" class=\"c1\">
           <tr class=\"h\">
             <td class=\"b h\" width=\"30\">#</td>
             <td class=\"b h\">Username</td>
             <td class=\"b h\" width=\"150\">Registered on</td>
             <td class=\"b h\" width=\"50\">Posts</td>
             <td class=\"b h\" width=\"50\">Total</td>
";
  $q = 1;
  $p =- 1;
  for($i=1;$user=$sql->fetch($users);$i++){
    if($p!=$user[num]) {
      if($q<=5 && $i>5) {
        print "
               <tr class=\"n1\" align=\"center\">
                 <td class=\"b\" colspan=\"5\" style=\"height:3px\"><div style=\"height:1px\"></div></td>
               </tr>";
      }
      $q = $i;
    }

	if($q <= 5) {
		$tr = ($i % 2 ? "n1" : "n2");
	} else {
		$tr = ($i % 2 ? 'n2' :'n3');
	}
	
    print "<tr class=\"$tr\" align=\"center\">
             <td class=\"b\">$q.</td>
             <td class=\"b\" align=\"left\">".userlink($user)."</td>
             <td class=\"b\">".cdate($dateformat,$user['regdate'])."</td>
			 <td class=\"b\"><b>{$user['num']}</b></td>
			 <td class=\"b\">{$user['posts']}</b></td>
";
    $p = $user['num'];
  }
  print "</table>";

  pagefooter();

  function timelink($time){
    return " <a href=\"activeusers.php?time=$time\">".timeunits2($time)."</a> ";
  }
?>
