<?php

require 'lib/common.php';

class BanManager {
	static function permissionCheck($request_arguments) {
		global $sql, $loguser;
		
		if (!has_perm('ban-users')) {
			return FALSE;
		}
		
		$id = $request_arguments['id'];
		$tuser = $sql->fetchp("SELECT `group_id` FROM `users` WHERE `id` = ?", array($id));
		if ((is_root_gid($tuser['group_id']) || (!can_edit_user_assets($tuser['group_id']) && $id != $loguser['id'])) && !has_perm('no-restrictions')) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	static function banUserForm($request_arguments) {
		global $sql;
		
		$id = $request_arguments['id'];
		
		if(!$request_arguments['id']) {
			tpl_display('generic-error', array('error_message'=>'Invalid user ID provided.'));
			return true;
		}
		
		$result = $sql->fetchp("SELECT `id`, `name` FROM `users` WHERE `id` = ?", array($id));
		if (!$result) {
			tpl_display('generic-error', array('error_message'=>'Invalid user ID provided.'));
			return true;
		}

		tpl_display('banmanager-banuser', array('id' => $id, 'name' => $result['name']));
		
		return true;
	}
	
	static function banUserSubmit($request_arguments) {
		global $sql;
		
		$id = $request_arguments['id'];
		$expiration_time = $request_arguments['expiration_time'];
		
		$banned_group_id = $sql->resultq("SELECT `id` FROM `group` WHERE `banned` = 1;");
		
		$user = $sql->fetchp("SELECT * FROM `users` WHERE `id` = ?;", array($id));
		if(!isset($user)) {
			tpl_display('generic-error', array('error_message' => 'Invalid user.'));
			return true;
		}
		
		if ($expiration_time > 0) {
			$expiration_time = ctime() + $expiration_time;
			$ban_reason = "Banned until " . date("m-d-y h:i A", $expiration_time);
		} else {
			$ban_reason = "Banned permanently";
		}
		
		if (!empty($request_arguments['reason'])) {
			$ban_reason .= ': ' . htmlspecialchars($request_arguments['reason']);
		}

		$sql->prepare("UPDATE `users` SET `group_id` = ? WHERE `id` = ?;", array($banned_group_id, $user['id']));
		$sql->prepare("UPDATE `users` SET `title` = ? WHERE `id` = ?;", array($ban_reason, $user['id']));
		$sql->prepare("UPDATE `users` SET `tempbanned` = ? WHERE id=' ?;", array($expiration_time, $user['id']));

		redirect("profile.php?id={$user['id']}", -1);
		
		return true;
	}
	
	static function unbanUserForm($request_arguments) {
		global $sql;
		
		$id = $request_arguments['id'];
		
		if(!$request_arguments['id']) {
			tpl_display('generic-error', array('error_message' => 'Invalid user ID provided.'));
			return true;
		}
		
		$result = $sql->fetchp("SELECT `id`, `name` FROM `users` WHERE `id` = ?", array($id));
		if (!$result) {
			tpl_display('generic-error', array('error_message' => 'Invalid user ID provided.'));
			return true;
		}

		tpl_display('banmanager-unbanuser', array('id' => $id, 'name' => $result['name']));
		
		return true;
	}
	
	static function unbanUserSubmit($request_arguments) {
		global $sql;
		
		$id = $request_arguments['id'];
		$banned_group_id = $sql->resultq("SELECT `id` FROM `group` WHERE `banned` = 1;");
		$default_group_id = $sql->resultq("SELECT `id` FROM `group` WHERE `default` = 1;");
		
		$user = $sql->fetchp("SELECT * FROM `users` WHERE `id` = ?;", array($id));
		if(!isset($user)) {
			tpl_display('generic-error', array('error_message' => 'Invalid user.'));
			return true;
		}
		
		if ($user['group_id'] != $banned_group_id) {
			tpl_display('generic-error', array('error_message' => 'This user is not currently banned.'));
			return true;
		}
		
		$sql->prepare("UPDATE `users` SET `group_id` = ? WHERE `id` = ?;", array($default_group_id, $user['id']));
		$sql->prepare("UPDATE `users` SET `title` = ? WHERE `id` = ?;", array('', $user['id']));
		$sql->prepare("UPDATE `users` SET `tempbanned` = ? WHERE id=' ?;", array(0, $user['id']));

		redirect("profile.php?id=$user[id]", -2);
		
		return true;
	}
}

$router = new ActionRouter();

$router->register('ban-user', 
	ActionRouter::REQUEST_METHOD_GET, 
	array(
		array('GET', 'id', 0, 'integer')
	),
	array('BanManager', 'banUserForm'),
	array('BanManager', 'permissionCheck')
);

$router->register('ban-user', 
	ActionRouter::REQUEST_METHOD_POST, 
	array(
		array('POST', 'id', 0, 'integer'),
		array('POST', 'reason', 'none', 'string'),
		array('POST', 'expiration_time', 600, 'integer')
	),
	array('BanManager', 'banUserSubmit'),
	array('BanManager', 'permissionCheck')
);

$router->register('unban-user', 
	ActionRouter::REQUEST_METHOD_GET, 
	array(
		array('GET', 'id', 0, 'integer')
	),
	array('BanManager', 'unbanUserForm'),
	array('BanManager', 'permissionCheck')
);

$router->register('unban-user', 
	ActionRouter::REQUEST_METHOD_POST, 
	array(
		array('POST', 'id', 0, 'integer')
	),
	array('BanManager', 'unbanUserSubmit'),
	array('BanManager', 'permissionCheck')
);

try {
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$router->handle($action);
} catch(Exception $e) {
	error('Error', $e->getMessage());
}

?>