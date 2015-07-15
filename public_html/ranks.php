<?php
    require "lib/common.php";

 $rdmsg="";
  if($_COOKIE['pstbon']){
	header("Set-Cookie: pstbon=".$_COOKIE['pstbon']."; Max-Age=1; Version=1");
 $rdmsg="<script language=\"javascript\">
	function dismiss()
	{
		document.getElementById(\"postmes\").style['display'] = \"none\";
	}
</script>
	<div id=\"postmes\" onclick=\"dismiss()\" title=\"Click to dismiss.\"><br>
".      "<table cellspacing=\"0\" class=\"c1\" width=\"100%\" id=\"edit\"><tr class=\"h\"><td class=\"b h\">";
if($_COOKIE['pstbon']==1){
	$rdmsg.="Rankset Added<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
".	"<tr><td class=\"b n1\" align=\"left\">The rankset has been successfully added.</td></tr></table></div>";
} elseif($_COOKIE['pstbon']==2){
	$rdmsg.="Rankset Edited<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
".	"<tr><td class=\"b n1\" align=\"left\">The rankset has been successfully edited.</td></tr></table></div>";
} elseif($_COOKIE['pstbon']==3){
	$rdmsg.="Rankset Deleted<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
".	"<tr><td class=\"b n1\" align=\"left\">The rankset has been successfully deleted.</td></tr></table></div>"; }
}
   
    $getrankset = $_GET['rankset']; // Changed to allow the Kirby Rank to show.
    if (!is_numeric($getrankset)) $getrankset = 1; //Double checking.. 
    $totalranks = $sql->resultq("SELECT count(*) FROM `ranksets` WHERE id > '0';");

    if ($getrankset < 1 || $getrankset > $totalranks) $getrankset = 1; //Should be made dynamic based on rank sets.

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
    
   if (($_GET['action']=='addrankset'||$_GET['action']=='editrankset'||$_GET['action']=='deleterankset'||$_GET['action']=='editranks')&&!has_perm('edit-ranks')) {
     error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>"); }

   if ($_GET['action']=='deleterankset' && ($getrankset < 2 || $getrankset > $totalranks)) {
     error("Error", "The Mario, Dots, and None ranksets may not be deleted on the board.<br> <a href=./>Back to main</a>"); }

   if ($_GET['action']=='deleterankset' && $getrankset >= 2 && $getrankset != unpacksafenumeric($_GET['token'])) {
     error("Error", "Invalid token.<br> <a href=./>Back to main</a>"); }

  if (($_GET['action']=='addrankset' && $_POST['action']=='Submit' && $_POST['newname']=='') || ($_GET['action']=='editrankset' && $_POST['action']=='Submit' && $_POST['editname']=='')) {
     error("Error", "Please enter a name for this rankset.<br> <a href=./>Back to main</a>"); }

   if ($_GET['action']=='addrankset' && $_POST['action']=='Submit' && has_perm('edit-ranks')) {
     $newname = $sql->escape($_POST['newname']);
     $getrankset = $sql->resultq("SELECT MAX(id) FROM ranksets");
     if (!$getrankset) $getrankset = 0;
     $getrankset++;
     $sql->prepare("INSERT INTO ranksets (`id`,`name`) VALUES (?,?)", array($getrankset, $newname));
     redirect("ranks.php", 1); }

   if ($_GET['action']=='editrankset' && $_POST['action']=='Submit' && has_perm('edit-ranks')) {
     $getrankset = intval($getrankset);
     $editname = $sql->escape($_POST['editname']);
     $sql->prepare("UPDATE ranksets SET `name`=?  WHERE id=?", array($editname, $getrankset));
     redirect("ranks.php", 2); }

   if ($_GET['action']=='deleterankset' && $getrankset >= 2 && $getrankset == unpacksafenumeric($_GET['token']) && has_perm('edit-ranks')) {
     $getrankset = intval($getrankset);
     $sql->prepare("DELETE FROM ranksets WHERE id=?",array($getrankset));
     redirect("ranks.php", 3); }


   if (has_perm("view-allranks") || has_perm("edit-ranks"))
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
   if (has_perm("edit-ranks"))
    {
    if ($getrankset != 1)
     {
      $deletelink = " |  
                   <a href=\"ranks.php?action=deleterankset&rankset=$getrankset&token=" . urlencode(packsafenumeric($getrankset)) . "\" onclick=\"if (!confirm('Really delete this rankset?')) return false;\">Delete Rank</a>";
     }
     $editlinks = " | 
                   <a href=\"ranks.php?action=addrankset\">Add Rank</a> | 
                   <a href=\"ranks.php?action=editrankset&rankset=$getrankset\">Edit Rank</a>$deletelink";
    }
    
    $allranks = $sql->query("SELECT * FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
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
 
                         
   if ($_GET[action]=='addrankset' && has_perm('"edit-ranks')) {
   pageheader("Rankset Listing");
print "<form action='ranks.php?action=addrankset' method='post' enctype='multipart/form-data'>
".        " <table cellspacing=\"0\" class=\"c1\">
".
           catheader('New Rankset')."
".       "  <tr class=\"c\">
".        "  <tr>
".        "    <td class=\"b n1\" align=\"center\">Name:</td>
".        "      <td class=\"b n2\"><input type=\"text\" name='newname' size='40' maxlength='255' class='right'></td>
".        "  <tr class=\"n1\">
".        "    <td class=\"b\">&nbsp;</td>
".        "    <td class=\"b\"><input type=\"submit\" class=\"submit\" name=action value='Submit'></td>
".        " </table>
";
     pagefooter(); die(); }

   if ($_GET[action]=='editrankset' && has_perm('"edit-ranks')) {
   pageheader("Rankset Listing");
   $editrankset = $sql->resultq("SELECT `name` FROM `ranksets` WHERE `id`='$getrankset'");
print "<form action='ranks.php?action=editrankset&rankset=$getrankset' method='post' enctype='multipart/form-data'>
".        " <table cellspacing=\"0\" class=\"c1\">
".
           catheader('Edit Rankset')."
".       "  <tr class=\"c\">
".        "  <tr>
".        "    <td class=\"b n1\" align=\"center\">Name:</td>
".        "      <td class=\"b n2\"><input type=\"text\" name='editname' size='40' maxlength='255' value='".$editrankset."' class='right'></td>
".        "  <tr class=\"n1\">
".        "    <td class=\"b\">&nbsp;</td>
".        "    <td class=\"b\"><input type=\"submit\" class=\"submit\" name=action value='Submit'></td>
".        " </table>
";
     pagefooter(); die(); }

    pageheader("Rankset Listing");
        if($_COOKIE['pstbon']) { 
        print $rdmsg; }
    print "<table cellspacing=\"0\">
             <tr>
               <td>
                 <table cellspacing=\"0\" class=\"c1\">
                   <tr class=\"h\">
                     <td class=\"b n1\" width=\"50%\">Rank Set</td>
                   </tr>
                   <tr class=\"n1\">
                     <td class=\"b n1\">$rankselection$inaclnk$linkviewall$editlinks</td>
                   </tr>
                 </table>
               </td>
             </tr>
           </table><br>
           <table cellspacing=\"0\" class=\"c1\">
            <tr class=\"h\">
               <td class=\"b\" width=\"150px\">Rank</td>
               <td class=\"b\" width=\"50px\">Posts</td>
               <td class=\"b\" width=\"100px\">Users On Rank</td>
               <td class=\"b\">Users On Rank</td>
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
             <tr>
               <td class=\"b n1\">".(($usercount-$idlecount) || $blockunknown == false ? "$rank[str]" : "???")."</td>
               <td class=\"b n2\" align=\"center\">".(($usercount-$idlecount) || $blockunknown == false ? "$neededposts" : "???")."</td>
               <td class=\"b n2\" align=\"center\">$usercount</td>
               <td class=\"b n1\" align=\"center\">$usersonthisrank ".($idlecount?"($idlecount inactive)":"")."</td>
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
             <tr>
               <td class=\"b n1\">$rankset[title]<br>$rankimage</td>
               <td class=\"b\">$neededposts</td>
               <td class=\"b\" width=\"100px\">(amount of users who rank this)</td>
               <td class=\"b\">$usersonthisrank</td>
             </tr>";

     //"<!--$rankset[neededposts] $rankset[title] $rankimage<br>-->\n";
     unset($rankimage);
    }
    */
    print "</table>";
    pagefooter();
 ?>