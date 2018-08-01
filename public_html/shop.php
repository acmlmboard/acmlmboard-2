<?php
	require 'lib/common.php';
	
	const TOKEN_SHOP = 60;
	
	// Check for login right away
	needs_login(1);
	
	// Same thing for checking if we have access to the item shop / shop editor
	$_GET['id']     = isset($_GET['id'])     ? (int)$_GET['id'] : 0;
	$_GET['cat']    = isset($_GET['cat'])    ? (int) $_GET['cat'] : 0;
	$_GET['action'] = isset($_GET['action']) ? $_GET['action'] : "";
	
	// Add shop editor specific actions here
	$shopedit = in_array($_GET['action'], array('edit','save'));
	$canedit  = has_perm('manage-shop-items');
	
	if (!has_perm('use-item-shop')) {
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
	} else if ($shopedit && !$canedit) {
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
	}
	
	$user = $sql->fetchq("
		SELECT u.name, u.posts, u.regdate, r.* 
		FROM users u 
		LEFT JOIN usersrpg r ON u.id = r.id 
		WHERE u.id = {$loguser['id']}
	");
	
	$st = getstats($user);
	$coins  = $st['GP'];
	$gcoins = $st['gcoins'];
	
	// refresh-o-rama
	switch ($_GET['action']) {
		case 'save': //Added (Sukasa)
			check_token($_POST['auth'], TOKEN_SHOP);
			if (!$_GET['id']) {
				error("Error", "No item specified.<br> <a href=./>Back to main</a>");
			}
			// Delete checkbox marked
			if ($_GET['id'] != -1 && isset($_POST['delete']) && $_POST['delete']) {
				if ($_GET['id']) { // Can't delete nothing
					$sql->query("DELETE FROM items WHERE id = {$_GET['id']}");
					// Remove deleted item from users' inventories
					for ($i = 1; $i < 7; $i++)
						$sql->query("UPDATE usersrpg SET `eq{$i}` = 0 WHERE `eq{$i}` = {$_GET['id']}");
				}
				redirect("?", -5);
			}
			
			// derp
			$_POST['name']   = isset($_POST['name'])   ? $_POST['name'] : "";
			$_POST['desc']   = isset($_POST['desc'])   ? $_POST['desc'] : "";
			$_POST['coins']  = isset($_POST['coins'])  ? (int)$_POST['coins']  : 0;
			$_POST['coins2'] = isset($_POST['coins2']) ? (int)$_POST['coins2'] : 0;
			$_POST['cat']    = isset($_POST['cat'])    ? (int)$_POST['cat']    : 0;
			$_POST['hidden'] = isset($_POST['hidden']) ? (int)$_POST['hidden'] : 0;
			
			$realshop = $sql->resultq("SELECT COUNT(*) FROM itemcateg WHERE id = {$_POST['cat']}");
			if (!$realshop && $_POST['cat'] != 99)
				error("Error", "The selected category does not exist.");
			if (!trim($_POST['name']))
				error("Error", "The item must have a name.");
			if ($_POST['coins'] < 0 || $_POST['coins2'] < 0)
				error("Error", "You cannot save items with negative cost!"); //error("uh oh", "everybody is banned now.<br>good job");
			
			// Convert stats to the format stored in the db
			$set   = "";
			$stype = "";
			for ($i = 0; $i < 9; $i++) {
				$status = (isset($_POST[$stat[$i]]) && trim($_POST[$stat[$i]])) ? (string)$_POST[$stat[$i]] : '0';
				$mode = strtolower(substr($status, 0, 1)) == 'x' ? 'm' : 'a'; // only the x operator in the first character matters
				$val  = ($mode == 'm') ? substr($status, 1) * 100 : $status; // m -> .2 decimal; a -> int
				$set   .= "s{$stat[$i]} = ".(int)$val.", ";
				$stype .= $mode;
			}
			// other item info
			$set .= "`name`=?, `desc`=?, `stype`='{$stype}', `coins`={$_POST['coins']}, `coins2`={$_POST['coins2']}, `cat`='{$_POST['cat']}', `hidden`={$_POST['hidden']}";
			$vals = array(stripslashes($_POST['name']), stripslashes($_POST['desc']));
			
			if ($_GET['id'] == -1) {
				$sql->prepare("INSERT INTO items SET {$set}", $vals);
				$id = $sql->insertid();
			} else {
				$sql->prepare("UPDATE items SET {$set} WHERE id = {$_GET['id']}", $vals);
				$id = $_GET['id'];
			}
			
			redirect("shop.php?action=desc&id={$id}#{$id}", -4);
			break;
		case 'buy':
			check_token($_GET['auth'], TOKEN_SHOP);
			$item     = $sql->fetchq("SELECT * FROM items WHERE id={$_GET['id']}");
			$realshop = $sql->resultq("SELECT COUNT(*) FROM itemcateg WHERE id = '{$item['cat']}'"); // Never buy category 99 items
			
			if ($item['coins'] <= $coins && $item['coins2'] <= $gcoins && $realshop) {
				// If a previous item was equipped, unequip it now (automatically sell it)
				if (!$user['eq'.$item['cat']])
					$pitem = array('coins' => 0, 'coins2' => 0); // No item equipped
				else
					$pitem = $sql->fetchq("SELECT coins, coins2 FROM items WHERE id = ".$user['eq'.$item['cat']]);
				
				$sql->query("
					UPDATE usersrpg SET 
						eq{$item['cat']} = {$_GET['id']},
						spent  = spent  - {$pitem['coins']}  * 0.6 + {$item['coins']},
						gcoins = gcoins + {$pitem['coins2']} * 0.6 - {$item['coins2']}
					WHERE id = {$loguser['id']}
				");
				
				if ($config['ircshopnotice'])
					sendirc("{irccolor-name}" . get_irc_displayname() . " {irccolor-base}is now equipped with {irccolor-title}$item[name]{irccolor-base}.");
				redirect("shop.php", -2); // The {$item['name']} has been bought and equipped!, 'shop.php','the shop'
			}
			redirect("shop.php", -3);
			break;
		case 'sell':
			check_token($_GET['auth'], TOKEN_SHOP);
			
			$pitem = $sql->fetchq("SELECT name, coins, coins2 FROM items WHERE id = ".$user["eq{$_GET['cat']}"]);
			if ($pitem) {
				$sql->query("
					UPDATE usersrpg SET 
						eq{$_GET['cat']} = 0,
						spent  = spent  - {$pitem['coins']}  * 0.6,
						gcoins = gcoins + {$pitem['coins2']} * 0.6
					WHERE id = {$loguser['id']}
				");
			} else {
				redirect("shop.php", -3); //error("uh oh", "No.");
			}
			redirect("shop.php", -1); // "The {$pitem['name']} has been unequipped and sold.", 'shop.php', 'the shop'
			break;
	}
	
	
	// Cookie status messages
	$rdmsg = "";
	if (isset($_COOKIE['pstbon'])) {
		switch ($_COOKIE['pstbon']) {
			case -1: $rdmsg = cookiemsg("Item Sold", "The item has been unequipped and sold.");	break;
			case -2: $rdmsg = cookiemsg("Item Bought", "The item has been bought and equipped!"); break;
			case -3: $rdmsg = cookiemsg("uh oh", "You aren't allowed to do this."); break;
			case -4: $rdmsg = cookiemsg("Item Saved", "The item data has been saved."); break;
			case -5: $rdmsg = cookiemsg("Item Deleted", "The item has been deleted."); break;
		}
	}
	
	
	pageheader('Item shop');
?>
<style>
	.disabled {color:#888888}
	.higher   {color:#abaffe}
	.equal    {color:#ffea60}
	.lower    {color:#ca8765}
	.selected {color:#cbeffe; font-weight: bold}
</style>
<?php
	
	switch ($_GET['action']) {		
		case '':
			// Shop category list
			$shops   = $sql->query('SELECT * FROM itemcateg ORDER BY corder');
			$eq      = $sql->fetchq("SELECT * FROM usersrpg WHERE id = {$loguser['id']}");
			
			// Only grab necessary items, not everything
			$eqitems = $sql->getresultsbykey("SELECT id, name FROM items WHERE id IN (".implode(',', $eq).")", 'id', 'name');
			
			$shoplist = "";
			while ($shop = $sql->fetch($shops)) {
				$id = $eq["eq{$shop['id']}"];
				if ($id) {
					$itemlink = "<a href='shop.php?action=desc&id={$id}#{$id}'>{$eqitems[$id]}</a>";
				} else {
					$itemlink = ""; // "-";
				}
				$shoplist .= "
				$L[TR]>
					$L[TD2]>
						<a href='shop.php?action=items&cat={$shop['id']}#status'>{$shop['name']}</a>
						<br><span class='sfont'>{$shop['description']}</span>
					</td>
					$L[TD1c]>{$itemlink}</td>
				</tr>";
				
			}
			
			print "
			{$rdmsg}
			<br>
			<table style='border-spacing: 0px'>
				<tr>
					<td><img src='gfx/status.php?u={$loguser['id']}'></td>
					<td style='width: 10px; display: block; vertical-align: top'></td>
					<td style='width: 100%; vertical-align: top'>
						$L[TBL1]>
							$L[TRh]>
								$L[TDh]>Shop</td>
								$L[TDh]>Item equipped</td>
							</tr>
							{$shoplist}
						$L[TBLend]
					</td>
				</tr>
			</table>
			";
			
			break;
		case 'edit': //Added (Sukasa)
			// Edit / add item
			if (!$_GET['id']) {
				noticemsg("Error", "No item specified.<br> <a href=./>Back to main</a>");
				pagefooter();
				die;
			}

			// Create dummy item if a new one is being added
			$item = $sql->fetchq("SELECT * FROM items WHERE id='{$_GET['id']}' UNION SELECT * FROM items WHERE id='-1'");
			if ($_GET['id'] == -1) { // Default to the category specified via url (and not to the default value 99)
				$item['cat'] = $_GET['cat'];
			}
			$_GET['cat'] = $item['cat'];
			
			// For the category select box
			$shops     = $sql->getresultsbykey('SELECT id, name FROM itemcateg ORDER BY corder', 'id', 'name');
			$shops[99] = "*** Not listed ***";
			
			// Item attributes headers
			$stathdr = "";
			for ($i = 0; $i < 9; $i++) {
				$stathdr .= "$L[TDhc] style='width: 50px'><b>{$stat[$i]}</b></td>";
			}
			
			$statlist = itemrow($item, true); // Don't hide +0 / x1.00
			
			print "
			<form action='shop.php?action=save&id={$item['id']}' method='POST'>
			$L[TBL1]>$L[TR1]>$L[TD1c]><a href='shop.php'>Return to shop list</a> | <a href='shop.php?action=items&cat={$item['cat']}'>Return to item list</a> </tr>$L[TBLend]
			<br>
			<table style='width: 100%; border-spacing: 0px'>
				<tr>
					<td style='vertical-align: top'><img src='gfx/status.php?u={$loguser['id']}'></td>
					<td style='vertical-align: top'>
						$L[TBL1]>
							$L[TRh]>$L[TDhc] colspan=12>".($_GET['id'] == -1 ? "Adding a new item" : "Editing '".htmlspecialchars($item['name'])."'")."</td></tr>
							$L[TR1]>
								$L[TD1c] style='width: 100px'><b>Item name:</b></td>
								$L[TD2] colspan=9>$L[INPt]='name' size='40' value=\"". htmlspecialchars($item['name']) ."\"></td>
							</tr>
							$L[TR1]>
								$L[TD1c]><b>Description:</b></td>
								$L[TD2] colspan=9>$L[INPt]='desc' size='75' value=\"". htmlspecialchars($item['desc']) ."\"></td>
							</tr>
							$L[TR1]>
								$L[TD1c]><b>Category:</b></td>
								$L[TD2] colspan=9>".fieldselect('cat', $item['cat'], $shops)."</td>
							</tr>
							$L[TR1]>
								$L[TD1c]><b>Cost:</b></td>
								$L[TD2] colspan=9>
									<img src='img/coin.gif' align='absmiddle'> $L[INPt]='coins' size='6' value=\"" . htmlspecialchars($item['coins']) . "\"> - 
									<img src='img/coin2.gif' align='absmiddle'> $L[INPt]='coins2' size='6' value=\"" . htmlspecialchars($item['coins2']) . "\">
								</td>
							</tr>
							$L[TR1]>
								$L[TD1c]><b>Options:</b></td>
								$L[TD2] colspan=9>
									<label>$L[INPc]='hidden'".($item['hidden'] ? " checked" : "")."> Hidden Item</label> 
									".($_GET['id'] != -1 ? "<label style='float: right'>$L[INPc]='delete' value=1> Delete Item&nbsp;</label>" : "")."
								</td>
							</tr>
							$L[TR1]>
								$L[TD1c]><b>Stats:</b></td>
								{$stathdr}
							</tr>
							$L[TR1]>
								$L[TD1c]>&nbsp;</td>
								{$statlist}
							</tr>
							$L[TR1]>
								$L[TD1]></td>
								$L[TD2] colspan=9>$L[INPs]='Save' value='Save'>".auth_tag(TOKEN_SHOP)."</td>
							</tr>
						$L[TBLend]
					</td>
				</tr>
			</table>
			</form>";
			//break;
		case 'desc': 
			// Get the category to highlight the item
			if ($_GET['action'] != 'edit') {
				$_GET['cat'] = $sql->resultq("SELECT cat FROM items WHERE id = {$_GET['id']}");
				if (!$_GET['cat']) {
					noticemsg("Error", "This item does not exist!");
					pagefooter();
					die;
				}
			}
		
		case 'items': // Item selection in a category
			
			$realshop = $sql->resultq("SELECT COUNT(*) FROM itemcateg WHERE id = {$_GET['cat']}");
			if ($realshop) {
				// Normal category
				$eqitem = $sql->fetchq("SELECT i.* FROM items i	INNER JOIN usersrpg r ON i.id = r.eq{$_GET['cat']} WHERE r.id = {$loguser['id']}");
			} else if ($_GET['cat'] == 99) {
				// Default one, with unequipable items
				$eqitem = false;
			} else {
				noticemsg("Error", "This category does not exist!");
				pagefooter();
				die;
			}
			
			$edit = "";
			if ($canedit)
				$edit = " | <a href='shop.php?action=edit&id=-1&cat={$_GET['cat']}'>Add new item</a>";
			
			// Not necessary when editing an item (it only bloats the page)
			if ($_GET['action'] != 'edit') {
				print "
				$L[TBL1]>$L[TR1]>$L[TD1c]><a href='shop.php'>Return to shop list</a> {$edit}</td></tr>$L[TBLend]
				<br>
				$L[TBL] id='status'>
					<tr>
						$L[TDn] style='width: 256px'><img src='gfx/status.php?u={$loguser['id']}'></td>
						$L[TDnc] style='width: 150px'>
						<div id='pr' class='sfont'></div>
						</td>
					$L[TDn]><img src=img/_.png id=prev>$L[TBLend]
				<br>";
				?>
<script type="text/javascript">
  function preview(user,item,cat,name){
    document.getElementById('prev').src='gfx/status.php?u='+user+'&it='+item+'&ct='+cat+'&'+Math.random();
    document.getElementById('pr').innerHTML='Equipped with<br>'+name+'<br>---------->';
  }
</script>
				<?php
			}
			
			// Item attributes headers
			$stathdr = "";
			for ($i = 0; $i < 9; $i++) {
				$stathdr .= "$L[TDh] style='width: 50px'>{$stat[$i]}</td>";
			}
			
			// Hidden items can be only seen by those who can use the shop editor
			$seehidden = (int)$canedit;
			
			// Display items for this category
			$items = $sql->query("
				SELECT * FROM items 
				WHERE cat = {$_GET['cat']} AND `hidden` <= {$seehidden} 
				ORDER BY type,coins
			");
			
			print "
			$L[TBL1]>
				$L[TRh]>
					$L[TDh] style='width: 150px'>Commands</td>
					$L[TD2] width=1 rowspan=10000>&nbsp;</td>
					$L[TDh]>Item</td>
					{$stathdr}
					$L[TDh] width=6%><img src=img/coin.gif></td>
					$L[TDh] width=6%><img src=img/coin2.gif></td>
				</tr>";
			
			while ($item = $sql->fetch($items)) {
				
				// Only display edit action when editing an item
				$edit    = "<a href='shop.php?action=edit&id={$item['id']}'>Edit</a>";
				if ($_GET['action'] == 'edit') {
					$comm = $edit;
				} else {
					$tokenstr = auth_url(TOKEN_SHOP);
					$buy     = "<a href='shop.php?action=buy&id={$item['id']}{$tokenstr}'>Buy</a>";
					$sell    = "<a href='shop.php?action=sell&cat={$_GET['cat']}{$tokenstr}'>Sell</a>";
					$preview = "<a href='#status' onclick=\"preview({$loguser['id']},{$item['id']},{$_GET['cat']},'".addslashes($item['name'])."')\">Preview</a>";
					
					// Determine when to display the buy / preview text
					if      ($eqitem && $item['id'] == $eqitem['id'])                             $comm = $sell;
					else if ($realshop && $item['coins'] <= $coins && $item['coins2'] <= $gcoins) $comm = "$buy | $preview";
					else                                                                          $comm = $preview;
				
					// Extra link appended
					if ($canedit) $comm .= ($comm ? " | " : "").$edit;
				
				}
				
				// Row colors
				if      ($_GET['id'] == $item['id'])                           $color = " class='selected'";
				else if ($eqitem && $item['id'] == $eqitem['id'])              $color = " class='equal'";
				else if ($item['coins'] > $coins || $item['coins2'] > $gcoins) $color = " class='disabled'";
				else                                                           $color = " class=''";
				
				print "
				$L[TR]{$color} id='{$item['id']}'>
					$L[TD2c]>{$comm}</td>
					$L[TD1]>
						<b class='equal'>{$item['name']}</b> 
						<span class='sfont'>- {$item['desc']}</span>
					</td>
					".itemrow($item, false, $color, $eqitem)."
					$L[TD1r]>{$item['coins']}</td>
					$L[TD1r]>{$item['coins2']}</td>
				</tr>";
			}
			print "$L[TBLend]";
			break;

		default:
			noticemsg("Error", "What do you think you're doing?");
	}

	pagefooter();

// Render the status attributes for an item
function itemrow($item, $editmode = false, $color = '', $eqitem = false) {
	global $L, $stat;
	
	$atrlist = "";
	for ($i = 0; $i < 9; $i++) {
		$st = $item["s{$stat[$i]}"];
		// Determine if the status boost is treated as multiplier (fixed point decimal) or sum (integer)
		if ($item['stype'][$i] == 'm') {
			$st = vsprintf('x%1.2f', $st / 100);
			if (!$editmode && $st == 100) // Do not display x1.00
				$st = '&nbsp;';
		} else {
			if ($st > 0)
				$st = "+$st";
			if (!$editmode && !$st) // Do not display +0
				$st = '&nbsp;';
		}
		$itst = $item["s$stat[$i]"];
		
		// Comparision with the current equipped item is disabled when editing an item
		if ($eqitem !== false) {
			$eqst = $eqitem["s$stat[$i]"];
			$sametype = ($item['stype'][$i] == $eqitem['stype'][$i]); // multiplier type must match to show color
		} else {
			$eqst = 0;
			$sametype = true;
		}
		
		// Color selection
		if (!$color && $sametype) {
			if      ($itst >  $eqst) $cl = 'higher';
			else if ($itst == $eqst) $cl = 'equal';
			else if ($itst <  $eqst) $cl = 'lower';
		} else {
			$cl = '';
		}
		
		if ($editmode)
			$atrlist .= "$L[TD2c]>$L[INPt]='{$stat[$i]}' size='6' value='{$st}'></td>";
		else
			$atrlist .= "$L[TD2oc] {$cl}'>{$st}</td>";
	}
	return $atrlist;
}
