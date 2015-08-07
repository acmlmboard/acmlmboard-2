<?php
if (!function_exists('pageheader')) {
	die('Access denied.');
}

pageheader('Registration');

if (isset($tpl_vars['error_message'])) {
	noticemsg("Error", $tpl_vars['error_message']);
}
?>
<form action="register.php" method="post">
	<table cellspacing="0" class="c1">
		<tr class="h">
			<td class="b h" colspan="2">Register</td>
		</tr>
		<tr>
			<td class="b n1" align="center" width=120>&nbsp;</td>
			<td class="b n2"><font class="sfont">Please take a moment to read the <a href="faq.php">FAQ</a> before registering.</font></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Username:</td>
			<td class="b n2"><input type="text" name="username" size=25 maxlength=25></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Password:</td>
			<td class="b n2"><input type="password" name="pass" size=13 maxlength=32></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Password (again):</td>
			<td class="b n2"><input type="password" name="pass2" size=13 maxlength=32></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Email Address (optional):</td>
			<td class="b n2"><input type="text" name="email" size=25 maxlength="254"></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Sex:</td>
			<td class="b n2"><?php tpl_input_options('sex', 2, $tpl_vars['sex_list']); ?></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Time Zone:</td>
			<td class="b n2"><?php tpl_input_list('timezone', 'UTC', $tpl_vars['time_zone_list']); ?></td>
		</tr>
<?php 
		if (!empty($tpl_vars['security_question'])) { 
?>
		<tr>
			<td class="b n1" align="center">Security:</td>
			<td class="b n2">
				<?php echo $tpl_vars['security_question']; ?><br />
				<input type="text" name="puzzle" size="13" maxlength="6" />
			</td>
		</tr>
<?php
		}
?>
		<tr class="n1">
			<td class="b">&nbsp;</td>
			<td class="b"><input type="submit" class="submit" name=action value=Register></td>
		</tr>
	</table>	
</form>
<?php
pagefooter();
?>