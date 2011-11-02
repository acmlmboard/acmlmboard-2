<?php

/*Returns associative array of specified request values, slashed, regardless
 *of PHP.ini setting
*/
function request_variables($varlist) {
  $quoted = get_magic_quotes_gpc();
  $out = array();
  foreach ($varlist as $key) {
    if (isset($_REQUEST[$key])) {
      $out[$key] = ($quoted) ? 
      ($_REQUEST[$key]) : (addslashes($_REQUEST[$key]));
    }
  }
  return $out;
}



/* Legacy Support */

if(!get_magic_quotes_gpc()){
    if(is_array($GLOBALS)) while(list($key,$val)=each($GLOBALS)) if(is_string($val)) $GLOBALS[$key]=addslashes($val);
    if(is_array($_POST  )) while(list($key,$val)=each($_POST  )) if(is_string($val)) $_POST[$key]  =addslashes($val);
  }

function isssl(){
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on');
}

function redirect($url,$msg,$delay=2){
  return "You will now be redirected to <a href=$url>$msg</a> ...<META HTTP-EQUIV=REFRESH CONTENT=$delay;URL=$url>";
}


/* End Legacy Support */


?>
