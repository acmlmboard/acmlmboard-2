<?php
if (!function_exists('pageheader')) {
	die('Access denied.');
}
pageheader('Error');
?>
<table cellspacing="0" class="c1">
	<tr class="h">
		<td class="b h" align="center">Error
	</tr>
	<tr>
		<td class="b n1" align="center"><?php echo $tpl_vars['error_message']; ?></td>
	</tr>
</table>
<?php
pagefooter();
?>