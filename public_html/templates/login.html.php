<?php 
if (!function_exists('pageheader')) {
	die('Access denied.');
}

pageheader('Login');

if(isset($tpl_vars['error_message'])) {
	noticemsg("Error", $tpl_vars['error_message']);
}
?>
<form action="login.php" method="post">
	<input type="hidden" name="action" value="login" />
	<table cellspacing="0" class="c1">
		<tr class="h">
			<td class="b h" colspan="2">Login</td>
		</tr>
		<tr>
			<td class="b n1" align="center" width="120">Username:</td>
			<td class="b n2"><input type="text" name="username" size="25" maxlength="25" /></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Password:</td>
			<td class="b n2"><input type="password" name="password" size="13" maxlength="32" /></td>
		</tr>
		<tr>
			<td class="b n1">&nbsp;</td>
			<td class="b n2">
				<ul>
					<li><a href="register.php">Register</a></li>
					<li><a href="resetpassword.php">Forgot your password?</a></li>
				</ul>
			</td>
		</tr>
		<tr class="n1">
			<td class="b">&nbsp;</td>
			<td class="b"><input type="submit" class="submit" name="submit" value="Login" /></td>
		</tr>
	</table>
</form>
<?php
pagefooter();
?>