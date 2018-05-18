<?php

/*
	Core of the error reporting.
*/

// If the error handler is disabled, do not register any of the handlers
if ($config['enableerrorhandler']) {
	$er_errors = array();
	$er_hidden = 0;
	set_error_handler('error_reporter');
	set_exception_handler('exception_reporter');
	register_shutdown_function('error_printer');
	// error_reporting(E_ALL);
}

// For some dumb reason the slashes are wrong under Windows
function strip_doc_root($file) {
	$root_path = $_SERVER['DOCUMENT_ROOT'];
	if (PHP_OS == 'WINNT') {
		$root_path = str_replace("/", "\\", $root_path);
	}
	return str_replace($root_path, "", $file);
}

function error_reporter($type, $msg, $file, $line, $context) {
 	global $loguser, $er_errors, $config;
	
	// Is reporting for this error type allowed?
	if (!($config['reporterrors'] & $type)) { // is_numeric($type) && 
		global $er_hidden;
		++$er_hidden;
		return true;
	}

	// Error text definition
	$custom = false;
	switch($type) {
		case E_USER_ERROR:          $typetext = "User Error";      $custom = true;     break;
		case E_USER_WARNING:        $typetext = "User Warning";    $custom = true;     break;
		case E_USER_NOTICE:         $typetext = "User Notice";     $custom = true;     break;
		case E_ERROR:               $typetext = "Error";                break;
		case E_WARNING:             $typetext = "Warning";              break;
		case E_NOTICE:              $typetext = "Notice"; 				break;
		case E_STRICT:              $typetext = "Strict Notice";        break;
		case E_RECOVERABLE_ERROR:   $typetext = "Recoverable Error";    break;
		case E_DEPRECATED:          $typetext = "Deprecated";           break;
		case E_USER_DEPRECATED:     $typetext = "User Deprecated"; $custom = true;     break;
		//default: $typetext = $type; // Extra string
	}
	

	if ($custom) { // E_USER_*
		if (substr($file, -9) === "mysql.php") { // Query error most likely, get the parent
			for ($i = 1; substr($backtrace[$i]['file'], -9) === "mysql.php"; ++$i);
			$file = $backtrace[$i]['file'];
			$line = $backtrace[$i]['line'];
			$func = "mysql # " . $backtrace[$i]['function'];
			$args = $backtrace[$i]['args'];
		} else {
			// Do something similar if it didn't come from mysql
			// 0 -> error_reporter
			// 1 -> function the custom error was triggered
			// 2 -> parent function
			$backtrace = debug_backtrace();
			for ($i = 1; isset($backtrace[$i]) && $backtrace[$i]['function'] == 'trigger_error'; ++$i);
			if (isset($backtrace[$i])) { // Normal
				$func = $backtrace[$i]['function'];
				$args = $backtrace[$i]['args'];
			} else {
				$func = $args = ""; // If the error is triggered in (main), peter out and just use the last available file/line info
				--$i;               // It's not really consistent but better than nothing
			}
			$file = $backtrace[$i]['file'];
			$line = (int) $backtrace[$i]['line'];
		}
	} else {
		// Normal PHP Errors and Unhandled exception fall here.
		// The location of file and line are already correct, but there's no info for the function.
		// Get the *real* function where they are called from.
		// 0 -> error_reporter (skipped)
		// 1 -> parent function (or exception_reporter)
		// 2 -> real parent function if 1 is exception_reporter
		$backtrace = debug_backtrace();
		for ($i = 1; isset($backtrace[$i+1]) && $backtrace[$i]['function'] == 'exception_reporter'; ++$i);
		if (isset($backtrace[$i])) {
			$func = $backtrace[$i]['function'];
			$args = $backtrace[$i]['args'];
		} else {
			$func = $args = ""; // (main)
		}
	}
	
	// Mark suppression of errors
	if (!error_reporting())
		$typetext = "@{$typetext}";
	
	$file = strip_doc_root($file);
	if (in_array(substr($func, 0, 7), array('require', 'include'))) { // also prevent require(_once) & friends from showing the full directory
		$args = strip_doc_root($args);
	}

	// Local reporting
	$er_errors[] = array($typetext, $msg, $func, $args, $file, $line);
	// IRC reporting
	if ($config['ircerrors'] & $type) { // !is_numeric($type) ||
		$userstr = ($loguser['id'] ? "user: {irccolor-name}{$loguser['name']}{irccolor-base} ({irccolor-name}{$_SERVER['REMOTE_ADDR']}{irccolor-base})" : "guest's IP: {irccolor-name}{$_SERVER['REMOTE_ADDR']}")."{irccolor-base}";
		$loc = "{irccolor-url}{$file}{irccolor-title}#{irccolor-no}{$line}{irccolor-base}";
		sendirc("{irccolor-no}{$typetext}{irccolor-base}: \"{irccolor-title}{$msg}{irccolor-base}\" ({$userstr}, loc: {$loc})", $config['debugchan']);
	}
	
	return true;
}

function exception_reporter($err) {
	$type = E_ERROR; //"Exception";
	$msg  = $err->getMessage(); // . "\n\n<span style='color: #FFF'>Stack trace:</span>\n\n". highlight_trace($err->getTrace());
	$file = $err->getFile();
	$line = $err->getLine();
	
	error_reporter($type, $msg, $file, $line, NULL);
	die;
}
/*
function highlight_trace($arr) {
	$out = "";
	foreach ($arr as $k => $v) {
		$out .= "<span style='color: #FFF'>{$k}</span><span style='color: #F44'>#</span> ".
		        "<span style='color: #0f0'>{$v['file']}</span>#<span style='color: #6cf'>{$v['line']}</span> ".
		        "<span style='color: #F44'>{$v['function']}<span style='color:#FFF'>(\n".print_r($v['args'], true)."\n)</span></span>\n";
	}
	return $out;
}
*/
function error_printer() {
	// Exit if we don't have permission to view the errors
	if (!has_perm('show-debugger')) return true;
	
	global $L, $er_errors, $er_hidden;
	
	//              0     1      2      3      4      5
	//array($typetext, $msg, $func, $args, $file, $line);
	if ($er_errors || $er_hidden) {
		$cnt = count($er_errors);	
		print "<br><br>
		$L[TBL1]>
			$L[TRh]>
				$L[TDhc] colspan=4>
					<b>PHP Errors: {$cnt}".($er_hidden ? " (+ {$er_hidden} hidden)" : "")."</b>
				</td>
			</tr>";
		if ($cnt) { // Extra check to hide this when the only errors are hidden
			print "
			$L[TRh]>
				$L[TDhc] style='width: 20px'>&nbsp;</td>
				$L[TDhc] style='width: 150px'>Error type</td>
				$L[TDhc]>Function</td>
				$L[TDhc]>Message</td>
			</tr>";
		}
		
		for ($i = 0; $i < $cnt; ++$i) {
			$cell = ($i%2)+1;
			$td = $L["TD{$cell}"];
			$tc = $L["TD{$cell}c"];
			
			if ($er_errors[$i][2]) {
				$func = $er_errors[$i][2]."(".print_args($er_errors[$i][3]).")";
			} else {
				$func = "<i>(main)</i>";
			}
			
			print "
				$L[TR1]>
					$tc>".($i+1)."</td>
					$tc>{$er_errors[$i][0]}</td>
					$tc>
						{$func}
						<div class='sfont'>{$er_errors[$i][4]}:{$er_errors[$i][5]}</div>
					</td>
					$td>{$er_errors[$i][1]}</td>						
				</tr>";
		}
			
		print "</table>";
	}
	//                            0       1            2           3           4           5              6
	//self::$query_list[] = array(1, $query, $b['pfunc'], $b['file'], $b['line'], $timetaken, isset($error));
	
	// Query debug list
	// Fairly similar to the error list
	if (class_exists('mysql') && mysql::$debug) {
		$i = 0;
		$cnt = count(mysql::$query_list);
		print "<br><br>
		$L[TBL1]>
			$L[TRh]>
				$L[TDhc] colspan=4>
					<b>SQL Query Debugger (Total: {$cnt})</b>
				</td>
			</tr>
			$L[TRh]>
				$L[TDhc] style='width: 20px'>&nbsp;</td>
				$L[TDhc] style='width: 250px'>Function</td>
				$L[TDhc]>Query</td>
				$L[TDhc]>Time</td>
			</tr>";
		for ($i = 0; $i < $cnt; ++$i) {
			$cell = ($i%2)+1;
			$td = $L["TD{$cell}"];
			$tc = $L["TD{$cell}c"];
			if (mysql::$query_list[$i][2]) {
				$func = mysql::$query_list[$i][2];
			} else {
				$func = "<i>(main)</i>";
			}
			
			// Mark errors as red
			if (!mysql::$query_list[$i][6]) {
				$query = mysql::$query_list[$i][1];
			} else {
				$query = "<span style='color:#F00; border-bottom:1px dotted red' title=\"".mysql::$query_list[$i][6]."\">".mysql::$query_list[$i][1]."</span>";
			}
			
			print "
				$L[TR1]>
					$tc>".($i+1)."</td>
					$tc>
						{$func}
						<div class='sfont'>".strip_doc_root(mysql::$query_list[$i][3]).":".mysql::$query_list[$i][4]."</div>
					</td>
					$tc>{$query}</td>
					$td>".sprintf("%01.6fs",mysql::$query_list[$i][5])."</td>						
				</tr>";
		}
		print "</table>";
	}
	
	return true;
}

// Nested argument print
function print_args($args, $i = 0) {
	$res = "";
	if ($i > 1) { // Control level of nesting
		return "Array";
	}
	foreach ($args as $val) {
		if (is_array($val)) {
			$tmp = print_args($val, $i+1);
			$res .= ($res !== "" ? "," : "")."<span class='fonts'>[{$tmp}]</span>";
		} else {
			$val = is_numeric($val) ? $val : "'".htmlspecialchars($val)."'";
			$res .= ($res !== "" ? "," : "")."<span class='fonts'>{$val}</span>";
		}
	}
	return $res;
}

