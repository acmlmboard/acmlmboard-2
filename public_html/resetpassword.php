<?php

require 'lib/common.php';

pageheader('Reset Password');

function generatePasswordToken() {
	$size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
	$iv = mcrypt_create_iv($size, MCRYPT_DEV_RANDOM);
	return bin2hex($iv);
}

function sendResetEmail($to, $token) {
	$subject = "{$config['boardtitle']} - Reset Password";
	$message = "You requested to reset your password.\r\n"
			. "You can reset your password at {$config['base']}/{$config['path']}resetpassword.php?token=$token";
	$headers = "From: admin@acmlm.org\r\n"
			. "X-Mailer: PHP/" . phpversion();
	mail($to, $subject, $message, $headers);
}

function displayResetForm() {
echo <<<HTML
	<form action="resetpassword.php?action=reset" method="post">
		<table cellspacing="0" class="c1">
			<tr class="h">
				<td class="b h" colspan="2">Reset Password</td>
			</tr>
			<tr>
				<td class="b n1" align="center" width="120">Username:</td>
				<td class="b n2"><input type="text" name="name" size="25" maxlength="25" /></td>
			</tr>
			<tr>
				<td class="b n1">&nbsp;</td>
				<td class="b n2">You will only be able to reset your password if you've provided a valid email address during registration or on your profile.</td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b"><input type="submit" class="submit" name="action" value="Login" /></td>
			</tr>
		</table>
	</form>
HTML;
}
function displayChangePasswordForm($token) {
	$token = htmlentities($token);
echo <<<HTML
	<form action="resetpassword.php?action=change" method="post">
		<input type="hidden" name="token" value="$token">
		<table cellspacing="0" class="c1">
			<tr class="h">
				<td class="b h" colspan="2">Reset Password</td>
			</tr>
			<tr>
				<td class="b n1" align="center">Password:</td>
				<td class="b n2"><input type="password" name="pass" size="13" maxlength="32" /></td>
			</tr>
			<tr>
				<td class="b n1" align="center">Password (confirm):</td>
				<td class="b n2"><input type="password" name="pass2" size="13" maxlength="32" /></td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b"><input type="submit" class="submit" name="action" value="Login" /></td>
			</tr>
		</table>
	</form>
HTML;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'reset-form';

// is something strange going on?
$reset_requests = $sql->fetchp("SELECT COUNT(*) as `count` FROM `resetpass` WHERE `ip` = ? AND `date` > ? LIMIT 1", array($_SERVER['REMOTE_ADDR'], (time() - 3600 * 24) ));
if($reset_requests['count'] > 3) {
	$action = "rate-limit";
}
$token_expiration = (time() - 3600 * 24);

switch ($action) {
	case 'reset-form':
		displayResetForm();
		break;
	
	case 'reset':
		$name = isset($_POST['name']) ? $_POST['name'] : '';
		if(empty($name)) {
			noticemsg("Error", "Empty username provided.");
			displayResetForm();
		} else {
			$user = $sql->fetchp("SELECT id, name, email FROM users WHERE name = ? LIMIT 1", array($name));
			if($user != null) {
				if(empty($user['email'])) {
					noticemsg("Error", "Unfortunately your account doesn't have any email associated with it, so we are unable to reset your password.");
				} else {
					$password_token = generatePasswordToken();
					if($sql->prepare("INSERT INTO `resetpass` (`user`, `date`, `ip`, `token`) VALUES (?, ?, ?, ?);",
						array($user['id'], time(), $_SERVER['REMOTE_ADDR'], $password_token))) {
						
						sendResetEmail($user['email'], $password_token);
						
						noticemsg("Success!", "A link will be emailed to you shortly which will allow you to change your password.");
					} else {
						noticemsg("Error", "We encountered a problem while trying to process your request.");
					}
				}
			} else {
				noticemsg("Error", "Invalid username provided.");
				displayResetForm();
			}
		}
		break;
		
	case 'change-form':
		$token = isset($_GET['token']) ? $_GET['token'] : '';
		$change_request = $sql->fetchp("SELECT `user`, `date`, `ip` FROM `resetpass` WHERE `token` = ? AND `completed` = 0 AND `date` > ? LIMIT 1", 
				array($token, $token_expiration));
		
		if($change_request != null) {
			displayChangePasswordForm($token);
		} else {
			noticemsg("Access Denied", "Invalid or expired password token.");
		}
		break;
	
	case 'change':
		$token	= isset($_POST['token'])	? $_POST['token']	: '';
		$pass	= isset($_POST['pass'])		? $_POST['pass']	: '';
		$pass2	= isset($_POST['pass2'])	? $_POST['pass2']	: '';
		
		if(empty($token) || strlen($token) != 32) {
			noticemsg("Access Denied", "Invalid password token.");
		} elseif($pass != $pass2) {
			noticemsg("Error", "Password and confirm password don't match. Try again.");
			displayChangePasswordForm($token);
		} elseif(strlen($pass) < 4) {
			noticemsg("Error", "Password needs to be at least 4 characters long.");
			displayChangePasswordForm($token);
		} else {
			$change_request = $sql->fetchp("SELECT `user`, `date`, `ip` FROM `resetpass` WHERE `token` = ? AND `completed` = 0 AND `date` > ? LIMIT 1", array($token, $token_expiration));
			if($change_request != null) {
				$user = $sql->fetchp("SELECT `pass` FROM `users` WHERE `id` = ?", array($change_request['user']));
				if($user != null) {
					$salted_password = md5($pwdsalt2 . $pass . $pwdsalt);
					$sql->prepare( "UPDATE `users` SET `pass` = ? WHERE `id` = ?;", 
							array($salted_password, $change_request['user']) );
					$sql->prepare( "UPDATE `resetpass` SET `oldpass` = ?, `newpass` = ?, `completed` = 1 WHERE `token` = ?;", 
							array($user['pass'], $salted_password, $token) );
					noticemsg("Success!", "Your password has been reset! <a href=\"login.php\">Click here to login!</a>");
				} else {
					noticemsg("Error", "User couldn't be found.");
				}
			} else {
				noticemsg("Access Denied", "Invalid password token.");
			}
		}
		break;
	
	case 'rate-limit':
		noticemsg("Access Denied", "You have made too many password reset requests in the past 24 hours.");
		break;

	default:
		noticemsg("Error", "Unknown action.");
		break;
}

pagefooter();