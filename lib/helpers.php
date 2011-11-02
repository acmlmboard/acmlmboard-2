<?php
  function checknumeric(&$var){
    if(!is_numeric($var)) {
      $var=0;
      return false;
    }
    return true;
  }

?>