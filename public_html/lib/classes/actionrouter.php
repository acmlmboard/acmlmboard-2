<?php

/**
 * Every page has actions, and a certain set of input variables it expects.
 * This class will enforce rules on how things should be run.
 *
 * @author Xeon
 */
class ActionRouter {
	private $supported_request_methods = array('GET', 'POST');
	private $mapped_actions = array();
	
	const REQUEST_METHOD_GET = 'GET';
	const REQUEST_METHOD_POST = 'POST';
	const DEFAULT_ACTION = 'default';
	
	/**
	 * 
	 * @param string $action The name of the action
	 * @param string $request_method The request method required to invoke the action.
	 * @param array $request_variables An array of variables that are expected or required.
	 * @param callable $callback The callback for when an action is invoked.
	 * @param callable $permission_callback An optional callback for checking the permissions of an action. This callback should return false to deny access.
	 * @throws Exception
	 */
	public function register($action, $request_method, $request_variables, $callback, $permission_callback = null) {
		
		if(empty($action)) {
			throw new Exception('Action cannot be empty.');
		}

		if(!in_array($request_method, $this->supported_request_methods)) {
			throw new Exception('Unsupported request method.');
		}
		
		if(!is_callable($callback)) {
			throw new Exception('Bad callback.');
		}
		
		$this->mapped_actions[$request_method.'_'.$action] = array('request_variables' => $request_variables, 
			'request_method' => $request_method, 'callback' => $callback, 
			'permission_callback' => $permission_callback);
	}
	
	public function handle($action) {
		if(empty($action)) {
			$action = self::DEFAULT_ACTION;
		}
		
		if(!in_array($_SERVER['REQUEST_METHOD'], $this->supported_request_methods)) {
			throw new Exception('Unsupported request method.');
		}
		
		$action_key = $_SERVER['REQUEST_METHOD'].'_'.$action;
		if(!isset($this->mapped_actions[$action_key])) {
			throw new Exception('Unmapped action.');
		}
		
		$mapped_action = $this->mapped_actions[$action_key];
		
		$request_arguments = array();
		if(count($mapped_action['request_variables']) > 0) {
			foreach($mapped_action['request_variables'] as $req) {
				if(strtoupper($req[0]) == 'GET') {
					$request_arguments[$req[1]] = Request::get($req[1], $req[2], $req[3]);
				} elseif(strtoupper($req[0]) == 'POST') {
					$request_arguments[$req[1]] = Request::post($req[1], $req[2], $req[3]);
				} elseif(strtoupper($req[0]) == 'COOKIE') {
					$request_arguments[$req[1]] = Request::cookie($req[1], $req[2], $req[3]);
				} else {
					
				}
			}
		}
		
		if(isset($mapped_action['permission_callback']) && is_callable($mapped_action['permission_callback'])) {
			if(!call_user_func($mapped_action['permission_callback'], $request_arguments)) {
				throw new Exception('Permission denied.');
			}
		}
		
		return call_user_func($mapped_action['callback'], $request_arguments);
	}
}
