<?php

/*Returns associative array of specified request values, unslashed, regardless
 *of PHP.ini setting
*/

function request_id() {
 if($id=$_GET[id])
    checknumeric($id);
  else $id=0;
  return $id;
}

function request_variables($varlist) {
  $quoted = get_magic_quotes_gpc();
  $out = array();
  foreach ($varlist as $key) {
    if (isset($_REQUEST[$key])) {
      $out[$key] = ($quoted) ? 
      (stripslashes($_REQUEST[$key])) : ($_REQUEST[$key]);
    }
  }
  return $out;
}



/* Legacy Support */

if(!get_magic_quotes_gpc()){
    if(is_array($GLOBALS)) while(list($key,$val)=each($GLOBALS)) if(is_string($val)) $GLOBALS[$key]=addslashes($val);
    if(is_array($_POST  )) while(list($key,$val)=each($_POST  )) if(is_string($val)) $_POST[$key]  =addslashes($val);
  }

// This function should be used on $_POST input, before either a) using it in a prepared query or b) escaping it with $sql->escape() and using it in a raw query
// It should be removed once the reliance on magic_quotes (and the hack above) have been removed.
function autodeslash($v)
{
	return stripslashes($v);
}
  
function isssl(){
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on');
}

// New cookie message system
// Reintroduces classic redirect (for better or for worse)
function redirect($url, $msg = "", $title = "Message", $redirmsg = "", $delay = 2){
	global $loguser;
	if (!$redirmsg || !$loguser['redirtype']) {
		// 0 - Instant redirect (print optional message later)
		if ($msg) {
			setcookie('wndmsg', $msg, 0);
			setcookie('wndtitle', $title, 0);
		}
		header("Location: {$url}");
		die;
	} else {
		$loguser['blocksprites'] = 1;
		if ($loguser['redirtype'] == 2) {
			// 2 - Classic, no redirect (for debugging usually)
			error($title, "{$msg}<br>Click <a href=\"{$url}\">here</a> to be continue to {$redirmsg}.");
		} else { 
			// 1 - Classic, with redirect (like 1.x)
			error($title, "{$msg}<br>You will now be redirected to <a href=\"{$url}\">{$redirmsg}</a> ...<META HTTP-EQUIV=REFRESH CONTENT=\"{$delay}\";URL=\"{$url}\">");
		}
	}
}


/* End Legacy Support */


?>