<?php if (!function_exists('pageheader')) die('Access denied.'); ?>
<table cellspacing="0" style="width:100%;">
	<tr>
		<td class="b" style="width:33.33%; vertical-align:top; padding-right:0.5em;">
			<table cellspacing="0" class="c1">
				<tr class="h"><td class="b h">Categories</td></tr>
				<?php
				if (isset($tpl_vars['category_list']) && count($tpl_vars['category_list']) > 0) {
					$c = 1;
					foreach ($tpl_vars['category_list'] as $item) {
						echo "\t\t\t<tr><td class=\"b n$c\"><a href=\"?action=edit-category&amp;id={$item['id']}\">{$item['title']}</a></td></tr>\n";
						$c = ($c == 1) ? 2 : 1;
					}
				} else {
					echo "\t\t\t<tr><td class=\"b n1\">No categories found</td></tr>\n";
				}
				?>
				<tr class="h"><td class="b h">&nbsp;</td></tr>
				<tr><td class="b n1"><a href="?action=create-category">Create new category</a></td></tr>
			</table>
		</td>
		<td class="b" style="width:33.33%; vertical-align:top; padding-left:0.5em; padding-right:0.5em;">
			<table cellspacing="0" class="c1">
				<tr class="h"><td class="b h">Forums</td></tr>
				<?php
				if (isset($tpl_vars['forum_list']) && count($tpl_vars['forum_list']) > 0) {
					$c = 1;
					foreach ($tpl_vars['forum_list'] as $item) {
						echo "\t\t\t<tr><td class=\"b n$c\"><a href=\"?action=edit-forum&amp;id={$item['id']}\">{$item['title']}</a></td></tr>\n";
						$c = ($c == 1) ? 2 : 1;
					}
				} else {
					echo "\t\t\t<tr><td class=\"b n1\">No categories found</td></tr>\n";
				}
				?>
				<tr class="h"><td class="b h">&nbsp;</td></tr>
				<tr><td class="b n1"><a href="?action=create-forum">Create new forum</a></td></tr>
			</table>
		</td>
		<td class="b" style="width:33.33%; vertical-align:top; padding-left:0.5em;">
			<table cellspacing="0" class="c1">
				<tr class="h"><td class="b h">Channels</td></tr>
				<?php
				if (isset($tpl_vars['channel_list']) && count($tpl_vars['channel_list']) > 0) {
					$c = 1;
					foreach ($tpl_vars['channel_list'] as $item) {
						echo "\t\t\t<tr><td class=\"b n$c\"><a href=\"?action=edit-channel&amp;id={$item['id']}\">{$item['chan']}</a></td></tr>\n";
						$c = ($c == 1) ? 2 : 1;
					}
				} else {
					echo "\t\t\t<tr><td class=\"b n1\">No channels found</td></tr>\n";
				}
				?>
				<tr class="h"><td class="b h">&nbsp;</td></tr>
				<tr><td class="b n1"><a href="?action=create-channel">Create new channel</a></td></tr>
			</table>
		</td>
	</tr>
</table>
<br />