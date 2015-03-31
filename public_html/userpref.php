<?php

require 'lib/function.php';
require 'lib/login.php';

$ret = -1;
if ($log) {
	if (has_perm('update-own-profile')) {
		$field = $_GET['field'];
		$value = $_GET['value'];
		if ($field == 'hidequickreply') {
			$dbfield = 'hidequickreply';
			$dbvalue = $value;
			checknumeric($value);
			if ($value != 0 && $value != 1) $value = 0;
		}
		else $dbfield = 0;
		if ($dbfield) {
			$sql->prepare("UPDATE `users` SET `$dbfield`=? WHERE id=?",array($dbvalue,$loguser['id']));
			$ret = 0;
		}
	}
}
echo $ret;
?>