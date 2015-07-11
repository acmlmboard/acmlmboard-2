<?php
require 'lib/common.php';  

/*
 * ajax.php
 * Provides a simple API to do some basic tasks on the board.
 */

$response_data = array();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch($action) {
	case 'usersearch':
		$name = isset($_REQUEST['name']) ? $sql->escape($_REQUEST['name']) : '';
		$maxresults = isset($_REQUEST['maxresults']) ? (int)$_REQUEST['maxresults'] : 10;
		
		if(empty($name)) {
			$response_data['result'] = 'error';
			$response_data['message'] = 'Invalid name provided provided.';
			break;
		}
		
		if($maxresults <= 0) {
			$response_data['result'] = 'error';
			$response_data['message'] = 'No users found';
			break;
		}
		
		$results = $sql->fetchq("SELECT `id`, `name`, `displayname`, `sex` FROM `users` WHERE `name` LIKE '$name%' ORDER BY `name` LIMIT 0,$maxresults;");
		if($results) {
			$response_data['result'] = 'success';
			$response_data['data'] = $results;
		} else {
			$response_data['result'] = 'error';
			$response_data['message'] = 'No users found';
		}
		break;
		
	default:
		$response_data['result'] = 'error';
		$response_data['message'] = 'Invalid action specified.';
		break;
}

header('Content-type: application/json');
echo json_encode($response_data);