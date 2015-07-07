<?php
$log=false;
$logpermset = array();

if(!empty($_COOKIE['user']) && !empty($_COOKIE['pass'])){
	if($user = checkuid($_COOKIE['user'], unpacklcookie($_COOKIE['pass']))) {
		$log=true;
		$loguser=$user;
		load_user_permset();
	} else {
		setcookie('user',0);
		setcookie('pass','');
		load_guest_permset();
	}
} else {
	load_guest_permset();
}

// error handler function
function ABErrorHandler($errno, $errstr, $errfile, $errline) {
	if (!(error_reporting() & $errno)) {
		// This error code is not included in error_reporting
		return;
	}

	switch ($errno) {
		case E_USER_ERROR:
			echo "<b>ERROR</b> [$errno] $errstr<br />\n";
			echo "  Fatal error on line $errline in file $errfile";
			echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			echo "Aborting...<br />\n";
			exit(1);
			break;

		case E_USER_WARNING:
			echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
			break;

		case E_USER_NOTICE:
			echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
			break;

		default:
			echo "Unknown error type: [$errno] $errstr<br />\n";
			break;
	}

	/* Don't execute PHP internal error handler */
	return true;
}

// function to test the error handling
function scale_by_log($vect, $scale) {
	if (!is_numeric($scale) || $scale <= 0) {
		trigger_error("log(x) for x <= 0 is undefined, you used: scale = $scale", E_USER_ERROR);
	}

	if (!is_array($vect)) {
		trigger_error("Incorrect input vector, array of values expected", E_USER_WARNING);
		return null;
	}

	$temp = array();
	foreach($vect as $pos => $value) {
		if (!is_numeric($value)) {
			trigger_error("Value at position $pos is not a number, using 0 (zero)", E_USER_NOTICE);
			$value = 0;
		}
		$temp[$pos] = log($scale) * $value;
	}

	return $temp;
}

if (has_perm('view-errors')) {
	// set to the user defined error handler
	//  $old_error_handler = set_error_handler("ABErrorHandler");
}
?>