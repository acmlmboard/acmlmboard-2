<?php
if (!function_exists('pageheader')) {
	die('Access denied.');
}

pageheader('Ban User');

if (isset($tpl_vars['error_message'])) {
	noticemsg("Error", $tpl_vars['error_message']);
}
?>
<form action="banmanager.php" method="post">
	<input type="hidden" name="action" value="ban-user" />
	<input type="hidden" name="id" value="<?php echo $tpl_vars['id']; ?>" />
	<table cellspacing="0" class="c1">
		<tr class="h">
			<td class="b h" colspan="2">Ban User</td>
		</tr>
		<tr>
			<td class="b n1" align="center">User:</td>
			<td class="b n2"><?php echo $tpl_vars['name']; ?></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Reason:</td>
			<td class="b n2"><input type="text" name="reason" class="right" /></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Expires?</td>
			<td class="b n2">
				<?php
				tpl_input_list("expiration_time", 0, 
					array(
						"600" => "10 minutes",
						"3600" => "1 hour",
						"10800" => "3 hours",
						"86400" => "1 day",
						"172800" => "2 days",
						"259200" => "3 days",
						"604800" => "1 week",
						"1209600" => "2 weeks",
						"2419200" => "1 month",
						"4838400" => "2 months",
						"0" => "never"
					));
				?>
			</td>
		</tr>
		<tr class="n1">
			<td class="b">&nbsp;</td>
			<td class="b">
				<input type="submit" class="submit" name="banuser" value="Ban User">
			</td>
		</tr>
	</table>
</form>
<?php
pagefooter();
?>