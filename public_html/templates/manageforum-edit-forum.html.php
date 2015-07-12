<?php if (!function_exists('pageheader')) die('Access denied.'); ?>
<form action="manageforum.php?action=edit-forum&amp;subaction=update" method="POST">
	<table cellspacing="0" class="c1">
		<tr class="h"><td class="b h" colspan=2><?php echo $tpl_vars['section-title']; ?></td></tr>
		<tr>
			<td class="b n1" align="center">Title:</td>
			<td class="b n2"><?php tpl_input_text('title', $tpl_vars['title'], 50, 500); ?></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Description:<br><small>HTML allowed.</small></td>
			<td class="b n2"><?php tpl_input_textarea('descr', $tpl_vars['descr'], 3, 50); ?></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Category:</td>
			<td class="b n2">
				<?php echo $tpl_vars['category_list']; ?>
			</td>
		</tr>
		<tr>
			<td class="b n1" align="center">Display order:</td>
			<td class="b n2"><?php tpl_input_text('ord', $tpl_vars['ord'], 4, 10); ?></td>
		</tr>
		<tr>
			<td class="b n1" align="center">Report to IRC channel:<br><small>Leave this to default if you don't use IRC reporting.</small></td>
			<td class="b n2">
				<?php echo $tpl_vars['channel_list']; ?>
			</td>
		</tr>
		<tr>
			<td class="b n1" align="center">&nbsp;</td>
			<td class="b n2">
				<?php
					tpl_input_checkbox('private', 'Private forum', $tpl_vars['private']);
					tpl_input_checkbox('readonly', 'Read-only', $tpl_vars['readonly']);
					tpl_input_checkbox('trash', 'Trash forum', $tpl_vars['trash']);
				?>
			</td>
		</tr>
		<tr class="h"><td class="b h" colspan=2>&nbsp;</td></tr>
		<tr>
			<td class="b n1" align="center">&nbsp;</td>
			<td class="b n2">
				<input type="submit" class="submit" name="saveforum" value="Save forum"> 
				<input type="submit" class="submit" name="delforum" value="Delete forum" onclick="if (!confirm('Really delete this forum?'))
							return false;"> 
				<button type="button" class="submit" id="back" onclick="window.location = 'manageforums.php';">Back</button>
			</td>
		</tr>
	</table>
</form>
<br />