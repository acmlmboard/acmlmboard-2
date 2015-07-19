<?php

require 'lib/common.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';
switch($action) {
	case 'login':
		$f_username = isset($_POST['username']) ? $_POST['username'] : '';
		$f_password = isset($_POST['password']) ? $_POST['password'] : '';

		if ($userid = checkuser($f_username, md5($pwdsalt2 . $f_password . $pwdsalt))) {
			setcookie('user', $userid, 2147483647);
			setcookie('pass', packlcookie(md5($pwdsalt2 . $f_password . $pwdsalt), implode(".", array_slice(explode(".", $_SERVER['REMOTE_ADDR']), 0, 2)) . ".*"), 2147483647);
			header("Location: ./");
		} else {
			$tpl_vars = array('error_message' => 'Invalid username or password.');
			tpl_display('login', $tpl_vars);
		}
		break;
	
	case 'logout':
		setcookie('user', 0);
		setcookie('pass', '');
		header("Location: ./");
		break;
	
	default:
		$tpl_vars = array();
		tpl_display('login', $tpl_vars);
		break;
}
?>
