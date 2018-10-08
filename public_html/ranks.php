<?php
    require "lib/common.php";
    pageheader("Rankset Listing");
   
    $getrankset = $_GET['rankset']; // Changed to allow the Kirby Rank to show.
    if (!is_numeric($getrankset)) $getrankset = 1; //Double checking.. 
    $totalranks = $sql->resultq("SELECT count(*) FROM `ranksets` WHERE id > '0';");

    //Disabled this due to conflict made by management tools if a rank category is deleted.
    //if ($getrankset < 1 || $getrankset > $totalranks) $getrankset = 1; //Should be made dynamic based on rank sets.

    $linkuser = array();
    $allusers = $sql->query("SELECT ".userfields().", `posts`, `minipic`, `lastview` FROM `users` WHERE `rankset` = ".$getrankset." ORDER BY `id`");
   //$linkuser = $sql->fetchq($allusers);
    /*while ($user2 = $sql->fetchq($allusers))
     {;
      print "$user2[id]";
      $linkuser[$user2['id']] = $user2;
     }*/
    while ($row = $sql->fetch($allusers)) 
    {
      //printf("ID: %s  Name: %s Post: %s", $row[id], $row[name], $row[posts]); 
      $linkuser[$row['id']] = $row;
    }
    $blockunknown = true;

    $rankposts = array();
    
   if (has_perm("view-allranks"))
    {
     $linktoggle = "1\">View";
    if ($_GET['viewall'] == 1)
     {
      $blockunknown = false;
      $linktoggle = "0\">Hide";
     }
     $linkviewall = " | <a href=\"ranks.php?rankset=$getrankset&viewall=$linktoggle All Hidden</a>";
    }
    $editlinks = "";
    
    $allranks = $sql->query("SELECT r.*, rs.* FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
                       ORDER BY `p`");
    $ranks    = $sql->query("SELECT * FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
                       WHERE `rs`='$getrankset' ORDER BY `p`");
              
   while($rank = $sql->fetch($allranks))
    {
    if ($rank['rs'] == $getrankset)
      $rankposts[] = $rank['p'];
    if (!$rankselection)
      $rankselection .= "<a href=\"ranks.php?rankset=$rank[id]\">$rank[name]</a>";
    else
     {
     if ($usedranks[$rank['rs']] != true)
      $rankselection .= " | <a href=\"ranks.php?rankset=$rank[id]\">$rank[name]</a>";
     }
     $usedranks[$rank['rs']] = true;
    }
    if($_GET['rankset']){
	if(!$_GET['showinactive']) $inaclnk=" | <a href=\"ranks.php?rankset=".$_GET['rankset']."&showinactive=1\">Show Inactive</a>";
	else $inaclnk=" | <a href=\"ranks.php?rankset=".$_GET['rankset']."\">Hide Inactive</a>";
    } else {
	if(!$_GET['showinactive']) $inaclnk=" | <a href=\"ranks.php?showinactive=1\">Show Inactive</a>";
	else $inaclnk=" | <a href=\"ranks.php\">Hide Inactive</a>";
    }
                          
    print "$L[TBL]>
             $L[TR]>
               <td>
                 $L[TBL1]>
                   $L[TRh]>
                     $L[TD1] width=\"50%\">Rank Set</td>
                   </tr>
                   $L[TR1]>
                     $L[TD1]>$rankselection$inaclnk$linkviewall$editlinks</td>
                   </tr>
                 </table>
               </td>
             </tr>
           </table><br>
           $L[TBL1]>
            $L[TRh]>
               $L[TD] width=\"150px\">Rank</td>
               $L[TD] width=\"50px\">Posts</td>
               $L[TD] width=\"100px\">Users On Rank</td>
               $L[TD]>Users On Rank</td>
             </tr>";
    
    $i = 1;

   while($rank = $sql->fetch($ranks))
    {
     $neededposts     = $rank['p'];
     $nextneededposts = $rankposts[$i];
     $usercount       = 0;
     $idlecount       = 0;
  //$allusers = $sql->query('SELECT `id`, `name`, `displayname`, `posts` FROM `users` ORDER BY `id`');
    foreach ($linkuser as $user)
     {
      //print "$user[id] moo $user[name] <br>";
      $climbingagain = "";
      $postcount = $user['posts'];
     if ($postcount > 5100)
      {
       $postcount = $postcount - 5100;
       $climbingagain = " (Climbing Again (5100))";
      }
      //print "$user[name]: ($postcount => $neededposts) && ($postcount < $nextneededposts)<br>";
     if (($postcount >= $neededposts) && ($postcount < $nextneededposts))
      {
//    if(!$_GET['showinactive']) $inact=" AND `lastview` > ".(time()-(86400 * $inactivedays)); else $inact="";
       //$usersonthisrank .= linkuser($user['id']).$climbingagain;
    if($_GET['showinactive'] || $user['lastview']>(time()-(86400 * $inactivedays))){
      if ($usersonthisrank)
        $usersonthisrank .= ", ";
        if($user['minipic']) $minpic = "<img style='vertical-align:text-bottom' src='".$user['minipic']."'/> ";
        else $minpic = "";
       $usersonthisrank .= $minpic.userlink_by_id($user['id']).$climbingagain;
    } else $idlecount++;
       $usercount++;
      }
     }
    if ($rank['image'])
     {
      $rankimage .= "<img src=\"img/ranksets/$rank[dirname]/$rank[image]\">";
     }
     print "
             $L[TR]>
               $L[TD1]>".(($usercount-$idlecount) || $blockunknown == false ? "$rank[str]" : "???")."</td>
               $L[TD2c]>".(($usercount-$idlecount) || $blockunknown == false ? "$neededposts" : "???")."</td>
               $L[TD2c]>$usercount</td>
               $L[TD1c]>$usersonthisrank ".($idlecount?"($idlecount inactive)":"")."</td>
             </tr>";

     //"<!--$rankset[neededposts] $rankset[title] $rankimage<br>-->\n";
     unset($rankimage, $usersonthisrank);
     $i++;
    }
    
    /*
   while($rankset = fetchquery($allranksets, true))
    {
     $neededposts = $rankset['neededposts'];
    foreach ($linkuser as $user)
     {
      $postcount = $user['postcount'];
     if (($neededposts - $postcount) > 0)
     }
    if ($rankset['image'])
      $rankimage = "<img src=\"img/ranksets/$rankset[dirname]/$rankset[image]\">";
     print "
             $L[TR]>
               $L[TD1]>$rankset[title]<br>$rankimage</td>
               $L[TD]>$neededposts</td>
               $L[TD] width=\"100px\">(amount of users who rank this)</td>
               $L[TD]>$usersonthisrank</td>
             </tr>";

     //"<!--$rankset[neededposts] $rankset[title] $rankimage<br>-->\n";
     unset($rankimage);
    }
    */
    print "</table>";
    pagefooter();
 ?>
