<?php
    require 'lib/common.php';
    
    if (!has_perm('view-kcs')) {
        pageheader('Access Denied');
        no_perm();
    }
//KCS STEEL
  $past=$_GET[past];
  checknumeric($past);
    $time=86400;
  $kcscap=10;
  $kcspoints=array(1=>10,7,5,3,1,0);
  function dtime($ktc) {
    return $ktc+7200;
  }
//CALANE STEEL
    $daynames = array('Sunday','Monday','Tuesday','Wednesday',
                      'Thursday','Friday','Saturday');
    $monthnames = array(1=>'January',  'February','March',   'April',
                           'May',      'June',   'July',    'August',
                           'September','October','November','December');
    
    $today = getdate(ctime());
    
    if (isset($_REQUEST['m']) && is_numeric($_REQUEST['m'])) {
        $month = $_REQUEST['m'];
    } else {
        $month = $today['mon'];
    }
    if (isset($_REQUEST['y']) && is_numeric($_REQUEST['y'])) {
        $year = $_REQUEST['y'];
    } else {
        $year = $today['year'];
    }
    if (isset($_REQUEST['d']) && is_numeric($_REQUEST['d'])) {
        $day = $_REQUEST['d'];
    } else if ($year == $today['year'] && $month == $today['mon']) {
        $day = $today['mday'];
    } else {
        $day = 31;
    }
        
    
    $mtstamp = mktime(0,0,0,$month,1,$year);
    $mdays = intval(date('t', $mtstamp));
    $wday = intval(date('w', $mtstamp));
    
    pageheader('Frankenstein KCS Volume 1 -- A horror story of code.');
    print "$L[TBL] width=\"100%\">
".        "    $L[TR]>
".        "        $L[TDc] colspan=7 style=\"font-size:200%\">$monthnames[$month] $year</td>
".        "    </tr>
".        "    $L[TRh]>
";

    for ($w = 0; $w < 7; $w++) {//days of the week
        print "        $L[TDh] width=\"14%\">$daynames[$w]</td>\n";
    }
    
    print "    </tr>
".        "    $L[TR] style=\"height:80\">\n";

    for ($w = 0; $w < $wday; $w++) {//unused cells in the first week
        print "$L[TD]></td>";
    }

    for ($mday = 1; $mday <= $mdays; $mday++, $wday++) {//main day cells
        if ($wday > 6) {  //week wrap around
            $wday = 0;
            print "</tr>$L[TR] style=\"height:80\">\n";
        }
        $l = ($mday == $day) ? $L['TD1l'] : $L['TD2l'];
        print "$l width=\"14%\" valign=\"top\"><a href=\"frank.php?d=$mday&m=$month&y=$year\">$mday</a>";
//Think dis right place.
  $dstr=strtotime($mday.' '.$monthnames[$month].' '.$year);
  $query='SELECT id,posts,regdate,name,sex,power,SUM(num) num FROM ('
          .'SELECT u.id,u.posts,regdate,u.name,u.sex,u.power,CASE WHEN COUNT(*)>'.$kcscap.' THEN '.$kcscap.' ELSE COUNT(*) END num '
          .'FROM users u '
          .'LEFT JOIN posts p ON p.user=u.id '
          .'WHERE p.date>'.($dstr-(dtime($dstr)%86400)).' AND p.date<'.($dstr-(dtime($dstr)%86400-86400)).' '
          .'GROUP BY p.thread,u.id'
	.') inter GROUP BY id ORDER BY num DESC';
  $users=$sql->query($query);
  $pqry=@mysql_result(mysql_query("SELECT count(*) FROM posts WHERE date>".($dstr-(dtime($dstr)%86400))." AND date<".($dstr-(dtime($dstr)%86400-86400))),0,0);

            print " -- <i>Total Posts: $pqry</i><table>";
 $q=1; $p=-1;
  for($i=1;$user=$sql->fetch($users);$i++){
    if($user[num]!=$p) $q=$i;
    if($q<=5) {
    if($mday <= $day){
	$uid=$user[id];
	$points[$uid]=$points[$uid]+$kcspoints[$q];
    }
    print
	"<tr><td>$q</td><td>".userlink($user)."</td><td>$user[num]</td></tr>";
    $p=$user[num];
  }
}
        print "</table></td>\n";
        
    }
    
    for (;$wday <= 6; $wday++) { //unused cells in the last week
        print "$L[TD]></td>";
    }
    
    print "    </tr>
".        "    $L[TR]>
".        "        $L[TDc] colspan=7> Month:";
    
    for ($i = 1; $i <= 12; $i++) {//month links
        if ($i == $month) {
            print " $i\n";
        } else {
            print " <a href=\"frank.php?m=$i&amp;y=$year\">$i</a>\n";
        }
    }
    
    print "             | Year:\n";
    
    for ($i = $year-2; $i <= $year+2; $i++) {//year links
        if ($i == $year) {
            print " $i\n";
        } else {
            print " <a href=\"frank.php?m=$month&amp;y=$i\">$i</a>\n";
        }
    }
    
    print "        </td>
".        "    </tr>
".         $L['TBLend'];

  //The old calendar ends here. Write the report!.
  //Copy Pasta'd code. 'cause this is how I Rick Roll!
  $dstr=strtotime($day.' '.$monthnames[$month].' '.$year);
  $query='SELECT id,posts,regdate,name,sex,power,SUM(num) num FROM ('
          .'SELECT u.id,u.posts,regdate,u.name,u.sex,u.power,CASE WHEN COUNT(*)>'.$kcscap.' THEN '.$kcscap.' ELSE COUNT(*) END num '
          .'FROM users u '
          .'LEFT JOIN posts p ON p.user=u.id '
          .'WHERE p.date>'.($dstr-(dtime($dstr)%86400)).' AND p.date<'.($dstr-(dtime($dstr)%86400-86400)).' '
          .'GROUP BY p.thread,u.id'
	.') inter GROUP BY id ORDER BY num DESC';
  $users=$sql->query($query);
  $pqry=@mysql_result(mysql_query("SELECT count(*) FROM posts WHERE date>".($dstr-(dtime($dstr)%86400))." AND date<".($dstr-(dtime($dstr)%86400-86400))),0,0);
	print "$L[TBL] width=\"100%\">
".        "    $L[TRh]>
".        "        $L[TDc] colspan=2>KCS Report for $monthnames[$month] $year</td>
".        "    </tr>
".        "    $L[TR]>
$L[TD2l]>".strtoupper($monthnames[$month])." $day<hr style=\"width: 100px; margin-left: 0px;\">Total amount of posts: $pqry<br><br><table cellspacing=0>";
$report=strtoupper($monthnames[$month])." $day<hr style=\"width: 100px; margin-left: 0px;\">Total amount of posts: $pqry<br><br><table cellspacing=0>";
//Results for posts
 $q=1; $p=-1;
  for($i=1;$user=$sql->fetch($users);$i++){
    if($user[num]!=$p) $q=$i;
    if($q<=5) {
    $usr=$user[id];
    $dpur[$usr]=$kcspoints[$q];
    $report.="<tr><td>$q</td><td>[user=$usr]</td><td>$user[num]</td></tr>";
    print "<tr><td>$q</td><td>".userlink($user)."</td><td>$user[num]</td></tr>";
    $p=$user[num];
  }
}
$report.="</table><br><br>Daily Points<hr style=\"width: 100px; margin-left: 0px;\"><table cellspacing=0>";
print "</table><br><br>Daily Points<hr style=\"width: 100px; margin-left: 0px;\"><table cellspacing=0>";
//Daily Points
if($dpur){
arsort($dpur);
$r=0; $q=1; $t=9999;
foreach($dpur as $usr => $pnts){
	if($pnts<$t) $r=$q;
    	$mpur = $sql->fetch($sql->query("SELECT id,name,displayname,sex,power FROM users WHERE id=$usr"));
	$report.="<tr><td>$r</td><td>[user=$usr]</td><td>$pnts</td></tr>";
	print "<tr><td>$r</td><td>".userlink($mpur)."</td><td>$pnts</td></tr>";
	$q++; $t=$pnts;
}
}
//Monthly Points
$report.="</table><br><br>Monthly Points<hr style=\"width: 100px; margin-left: 0px;\"><table cellspacing=0>";
print "</table><br><br>Monthly Points<hr style=\"width: 100px; margin-left: 0px;\"><table cellspacing=0>";
if($points){
arsort($points);
$r=0; $q=1; $t=9999;
foreach($points as $usr => $pnts){
	if($pnts<$t) $r=$q;
    	$mpur = $sql->fetch($sql->query("SELECT id,name,displayname,sex,power FROM users WHERE id=$usr"));
	$report.="<tr><td>$r</td><td>[user=$usr]</td><td>$pnts</td></tr>";
	print "<tr><td>$r</td><td>".userlink($mpur)."</td><td>$pnts $ico</td></tr>";
	$t=$pnts;
	$q++;
}
}
    $report.="</table>";
  print   "</table></td>$L[TD1l] style=\"width: 50%\" valign=\"top\">
".        "<textarea style=\"width: 100%; height: 400px;\" readonly=\"readonly\">$report</textarea></td>
".        "    </tr>
".         $L['TBLend'];
    pagefooter();
?>