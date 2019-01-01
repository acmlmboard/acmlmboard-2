<?php
  $nourltranker=1;
  require("lib/common.php");
  if(!has_perm('edit-ranks')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

if(!isset($_GET['id'])){
  $js='onclick=\'if(!confirm("Are you sure you wish to delete?")){return false;}\'';
  $catq = $sql->query("SELECT * FROM `ranksets` WHERE `id`>=1"); //-1 is reserved for dots. 0 is None.
  $rankcat="";
  for($i=1;$rankset=$sql->fetch($catq);$i++){
    $rankcat.="<a href=\"editrankset.php?id=".$rankset['id']."\">".$rankset['name']."</a>
    <small>[<a href=\"editrankset.php?id=".$rankset['id']."&del\" $js>Delete</a>]</small> | ";
  }
  pageheader("Edit Ranks");
  print "$cookiemsg $L[TBL1]>
  ".    "  $L[TRh]>$L[TD]>Select Rankset
  ".    "  $L[TR]>$L[TD1c]>
  ".    "    $rankcat <a href=\"editrankset.php?id=-2\">New Category</a>
  ".    "    <br>
  ".    "$L[TBLend]
  ";
}
if(isset($_GET['id'])){
if(isset($_GET['del'])){
//We're deleting a rankset
  if(!is_numeric($_GET['id']))  error("Error","Invalid ID detected.");
  if($_GET['id']==-1 || $_GET['id']==0) error("Error","Special rankset ID is protected.");
  $sql->query("DELETE FROM `ranksets` WHERE `id`=".$_GET['id']);
  redirect("editrankset.php", "Rankset deleted.", "Message");
} else{
if(!isset($_POST['name'])){
  pageheader("Edit Ranks");
    $t=$sql->fetchp('SELECT * FROM `ranksets` WHERE id='.$_GET['id'],array());
if(isset($pagebar)) RenderPageBar($pagebar);
$form = array(
  'action' =>
    urlcreate('editrankset.php', array(
      'id' => $_GET['id'],
    )
    ),
  'method' => 'POST',
  'categories' => array(
    'metadata' => array(
      'title' => 'Rankset Metadata',
      'fields' => array(
        'name' => array(
          'title' => 'Name',
          'type' => 'text',
          'length' => 15,
          'size' => 10,
'value' => $t['name'],
        ),

      ),
    ),
    'actions' => array(
      'fields' => array(
        'act' => array(
          'title' => ($_GET['id']=="-2")?'Create new rankset category':'Update rankset category',
          'type' => 'submit',
        ),
      ),
    ),    
  ),
);

RenderForm($form);
} else {
//Data Submitted for Rank Category.
  if(!is_numeric($_GET['id']))  error("Error","Invalid ID detected.");
  if($_GET['id']==-1 || $_GET['id']==0) error("Error","Special rankset ID is protected.");
  if($_GET['id']==-2) $id="null"; else $id= $_GET['id'];
  $sql->query("REPLACE INTO `ranksets` VALUES ($id,'".addslashes($_POST['name'])."')");
  redirect("editrankset.php", "Rankset updated.", "Message");
}
}
}
  pagefooter();
?>