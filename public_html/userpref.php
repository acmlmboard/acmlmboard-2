<?php

require 'lib/function.php';
require 'lib/login.php';

$field = isset($_GET['field']) ? $_GET['field'] : '';
$value = isset($_GET['value']) ? (int)$_GET['value'] : 0;

$ret = -1;
if ($log) {
	if (has_perm('update-own-profile')) {
		$whitelisted_fields = array('hidequickreply');
		if (in_array($field, $whitelisted_fields)) {
			$sql->prepare_query("UPDATE `users` SET `$field` = ? WHERE id= ?", array($value, $loguser['id']));
			$ret = 0;
		}
	}
}
echo $ret;
?>