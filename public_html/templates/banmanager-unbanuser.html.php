<?php
if (!function_exists('pageheader')) {
	die('Access denied.');
}

pageheader('Unban User');

if (isset($tpl_vars['error_message'])) {
	noticemsg("Error", $tpl_vars['error_message']);
}
?>
<form action="banmanager.php" method="post">
	<input type="hidden" name="action" value="unban-user" />
	<input type="hidden" name="id" value="<?php echo $tpl_vars['id']; ?>" />
	<table cellspacing="0" class="c1">
		<tr class="h">
			<td class="b h" colspan="2">Unban User</td>
		</tr>
		<tr>
			<td class="b n1" align="center">User:</td>
			<td class="b n2"><?php echo $tpl_vars['name']; ?></td>
		</tr>
		<tr class="n1">
			<td class="b">&nbsp;</td>
			<td class="b">
				<input type="submit" class="submit" name="unbanuser" value="Unban User">
			</td>
		</tr>
	</table>
</form>
<?php
pagefooter();
?>