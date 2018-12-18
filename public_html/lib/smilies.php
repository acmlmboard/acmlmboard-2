<?php

  function loadsmilies(){
    global $sql,$smilies;
    $i=0;
    $s=$sql->query("SELECT * FROM smilies");
    while($smilies[$i++]=$sql->fetch($s));
    $smilies['num']=$i-1; //Adjusting so smilie conversion and toolbar don't work on a blank. [Epele]
  }
  
?>