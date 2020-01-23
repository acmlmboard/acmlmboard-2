<?php
    $file='../userpic/'.$_GET['id'];
  if(isset($_GET['r'])) $r="?r=".$_GET['r']; else $r="";
  if((is_numeric($_GET['id']) || preg_match("/\d+_\d\d?/", $_GET['id'])) && file_exists($file))
    Header("Location:$file$r");
  else
    Header("Location:../img/_.png");
?>