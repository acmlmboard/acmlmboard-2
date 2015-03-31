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
        $L[TBL1]>
          $L[TR]>
             $L[TD2]><input type=\"submit\" value=\"Show\">: $L[INPt]=\"past\" size=\"3\" maxlength=\"4\" value=\"$past\"> days ago
                    | $L[INPc]=\"easymode\" id=\"emode\" ".(isset($easymode)?"checked":"")."><label for=\"emode\" title=\"Without this, at most 5 posts per day in any single thread are counted.\">EASY MODE</label>
        $L[TBLend]</form>";

  print "Active users on ".cdate($loguser['dateformat'], ctime() - $past * 86400).":
         $L[TBL1]>
           $L[TRh]>
             $L[TDh] width=\"30\">#</td>
             $L[TDh]>Username</td>
             $L[TDh] width=\"150\">Registered on</td>
             $L[TDh] width=\"50\">Posts</td>
             $L[TDh] width=\"50\">Total</td>
";
  $q = 1;
  $p =- 1;
  for($i=1;$user=$sql->fetch($users);$i++){
    if($p!=$user[num]) {
      if($q<=5 && $i>5) {
        print "
               $L[TR1c]>
                 $L[TD] colspan=\"5\" style=\"height:3px\"><div style=\"height:1px\"></div></td>
               </tr>";
      }
      $q = $i;
    }
    $tr = ($i%2 ? "TR2" : "TR3")."c";
    $tdtype = "TD";
    if($q <= 5) {
      $tr = ($i%2 ? "TR1": "TR2")."c";
      $tdtype = "TD";
    }
    $tdtypel = $tdtype."l";
    print "
	          $L[$tr]>
             $L[$tdtype]>$q.</td>
             $L[$tdtypel]>".userlink($user)."</td>
             $L[$tdtype]>".cdate($dateformat,$user['regdate'])."</td>
             $L[$tdtype]><b>$user[num]</b></td>
             $L[$tdtype]>$user[posts]</b></td>
";
    $p = $user['num'];
  }
  print "$L[TBLend]";

  pagefooter();

  function timelink($time){
    return " <a href=\"activeusers.php?time=$time\">".timeunits2($time)."</a> ";
  }
?>
