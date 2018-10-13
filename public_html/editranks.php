<?php
//This is ranks.php + editrankset.php merged lazily.
  $nourltranker=1;
  require "lib/common.php";
  if(!has_perm('edit-ranks')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

if(!isset($_GET['id'])){
  $js='onclick=\'if(!confirm("Are you sure you wish to delete?")){return false;}\'';
    pageheader("Rankset Listing");
   
    $getrankset = $_GET['rankset']; // Changed to allow the Kirby Rank to show.
    if (!is_numeric($getrankset)) $getrankset = 1; //Double checking.. 
    $totalranks = $sql->resultq("SELECT count(*) FROM `ranksets` WHERE id > '0';");


    $rankposts = array();
   
    
    $allranks = $sql->query("SELECT r.*, rs.* FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
                       ORDER BY `p`");
    $ranks    = $sql->query("SELECT r.*, rs.name FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
                       WHERE `rs`='$getrankset' ORDER BY `p`");
                       
   while($rank = $sql->fetch($allranks))
    {
    if ($rank['rs'] == $getrankset)
      $rankposts[] = $rank['p'];
    if (!$rankselection)
      $rankselection .= "<a href=\"editranks.php?rankset=$rank[id]\">$rank[name]</a>";
    else
     {
     if ($usedranks[$rank['rs']] != true)
      $rankselection .= " | <a href=\"editranks.php?rankset=$rank[id]\">$rank[name]</a>";
     }
     $usedranks[$rank['rs']] = true;
    }
                          
    print "$cookiemsg $L[TBL]>
             $L[TR]>
               <td>
                 $L[TBL1]>
                   $L[TRh]>
                     $L[TD1] width=\"50%\">Rank Set</td>
                   </tr>
                   $L[TR1]>
                     $L[TD1]>$rankselection</td>
                   </tr>
                 </table>
               </td>
             </tr>
           </table><br>
           $L[TBL1]>
            $L[TRh]>
               $L[TD]>Rank</td>
               $L[TD]>Posts</td>
               $L[TD]></td>
             </tr>";
    
    $i = 1;

   while($rank = $sql->fetch($ranks))
    {
     $neededposts     = $rank['p'];
     $nextneededposts = $rankposts[$i];
     $rankid       = $rank['id'];

    if ($rank['image'])
     {
      $rankimage .= "<img src=\"img/ranksets/$rank[dirname]/$rank[image]\">";
     }
     print "
             $L[TR]>
               $L[TD1]>".$rank['str']."</td>
               $L[TD2c]>".$neededposts."</td>
               $L[TD2c]><a href=\"editranks.php?id=".$rankid."\">Edit</a> | <a href=\"editranks.php?id=".$rankid."&rankset=".$_GET['rankset']."&del\" $js>Delete</a></td>
             </tr>";

     unset($rankimage, $usersonthisrank);
     $i++;
    }
     print "
             $L[TR]>
               $L[TD1]></td>
               $L[TD2c]></td>
               $L[TD2c]><a href=\"editranks.php?id=-2\">New Rank</a></td>
             </tr></table>";
    pagefooter();
}
//Copied Form.
if(isset($_GET['id'])){
if(isset($_GET['del'])){
//We're deleting a rankset
  if(!is_numeric($_GET['id']))  error("Error","Invalid ID detected.");
  $sql->query("DELETE FROM `ranks` WHERE `id`=".$_GET['id']);
  redirect("editranks.php?rankset=".$_GET['rankset'], "Rank deleted.", "Message");
} else{
if(!isset($_POST['str'])){
  pageheader("Edit Ranks");
  $rankcateg = array();
  $qrankcateg = $sql->query("SELECT `id`, `name` FROM `ranksets` WHERE `id`>=1");
  
  while ($allspcquery= $sql->fetch($qrankcateg))
  { 
    $rankcateg[$allspcquery['id']]= $allspcquery['name'];
  
  }
    $t=$sql->fetchp('SELECT * FROM `ranks` WHERE id='.$_GET['id']);
RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editranks.php', array(
      'id' => $_GET['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Rankset Metadata',
      'fields' => array(
        'str' => array(
          'title' => 'Name',
          'type' => 'text',
          'length' => 250,
          'size' => 100,
'value' => $t['str'],
        ),
        'p' => array(
          'title' => 'Posts',
          'type' => 'text',
          'length' => 10,
          'size' => 10,
'value' => $t['p'],
        ),
        'rs' => array(
          'title' => 'Rankset',
          'type' => 'text',
          'length' => 4,
          'type' => 'dropdown',
          'choices' => $rankcateg,
'value' => $t['rs'],
        ),
      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($_GET['id']=="-2")?'Create new rank':'Update rank',
          'type' => 'submit',
        ),
      ),
    ),    
  ),
);

RenderForm($form);
  pagefooter();
} else {
//Data Submitted for Rank Category.
  if(!is_numeric($_GET['id']))  error("Error","Invalid ID detected.");
  if(!is_numeric($_POST['p']))   error("Error","Invalid data detected.");
  if(!is_numeric($_POST['rs']))  error("Error","Invalid data detected.");
  if($_GET['id']==-2){
    $sql->query("INSERT INTO `ranks` VALUES ('".$_POST['rs']."','".$_POST['p']."','".addslashes($_POST['str'])."',null)");
    redirect("editranks.php?rankset=".$_POST['rs'], "Rank created.", "Message");
  } else {
    $sql->query("UPDATE `ranks` SET `p`='".$_POST['p']."', `str`='".addslashes($_POST['str'])."', `rs`='".$_POST['rs']."' WHERE `id`=".$_GET['id']);
    redirect("editranks.php?rankset=".$_POST['rs'], "Rank updated.", "Message");
  }
}
}
}

 ?>