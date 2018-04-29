<?php
	require "lib/common.php";
	
	// New IP ban management page
	// Now with pagination and filter functionality
	
	if (!has_perm('edit-ip-bans')) {
		error("Error", "You have no permissions to do this!<br> <a href='index.php'>Back to main</a>");
	}
	
	$_GET['newip']     = isset($_GET['newip'])     ? $_GET['newip']     : "";
	$_GET['newreason'] = isset($_GET['newreason']) ? $_GET['newreason'] : ""; // special reason text usually
	
	if (isset($_POST['ipban'])) {
		check_token($_POST['auth']);

		$_POST['newip']     = isset($_POST['newip']) ? str_replace('*', '%', trim($_POST['newip'])) : "";
		$_POST['reason']    = isset($_POST['reason']) ? stripslashes($_POST['reason']) : "";
		$_POST['expire']    = isset($_POST['expire']) ? ((int) $_POST['expire']) : 0;
		$_POST['hard']      = isset($_POST['hard'])   ? ((int) $_POST['hard'])   : 0;
		
		// A few sanity checks to make sure we're not doing something horribly wrong
		$wildcard = strpos($_POST['newip'], '%');
		$checkip  = str_replace('%', '', $_POST['newip']);
		
		if (!$checkip) {
			error("Error", "You didn't enter an IP mask.");
		} else if ($wildcard !== false && $wildcard != strlen($_POST['newip']) - 1) {
			error("Error", "The wildcard can only be the last character.");
		} else if (stripos($_SERVER['REMOTE_ADDR'], $checkip) === 0) {
			error("For your protection", "You cannot ban an IP range you're part of.");
		} else if ($sql->resultq("SELECT COUNT(*) FROM ipbans WHERE ipmask = '".addslashes($_POST['newip'])."' AND hard = '{$_POST['hard']}'")) {
			error("Error", "This IP mask is already ".($_POST['hard'] ? "hard" : "soft")." IP banned!");
		}
		
		if ($_POST['expire']) { // Get the unban date, if set
			$_POST['expire'] = ctime() + $_POST['expire'];
		}
		
		// Actually ban the IP now
		$send = array($_POST['newip'], $_POST['hard'], $_POST['expire'], $loguser['name'], $_POST['reason']);
		$sql->prepare("INSERT INTO ipbans (ipmask,hard,expires,banner,reason) VALUES (?,?,?,?,?)", $send);
		$what = $_POST['hard'] ? "an hard" : "a soft";
		sendirc("{irccolor-base}{irccolor-name}{$loguser['name']}{irccolor-base} added {$what} IP ban for {irccolor-name}{$_POST['newip']}{irccolor-base} (reason: '{$_POST['reason']}').", $config['staffchan']);
		
		redirect("?", -1);
	} else if (isset($_POST['dodel'])) {
		check_token($_POST['auth']);
		
		// Iterate over the sent IPs and add them to the query
		if (isset($_POST['delban']) && !empty($_POST['delban'])){
			$i = 0;
			foreach ($_POST['delban'] as $ban) {
				$data = explode(",", decryptpwd(urldecode($ban)));
				$sql->query("DELETE FROM ipbans WHERE ipmask = '".addslashes($data[0])."' AND expires = ".((int) $data[1])." AND hard = ".((int) $data[2]));
				++$i;
			}
			redirect("?", $i);
		} else {
			redirect("?", -2);
		}
	}
	
	
	// Query values
	$outres = array();
	$qdata  = array();
	
	if (isset($_GET['ip'])) {
		$searchip = $_GET['ip'];
	} else {
		$searchip = isset($_POST['searchip']) ? $_POST['searchip'] : "";
	}
	if (isset($_POST['setreason']) && $_POST['setreason']) {
		$reason = stripslashes($_POST['setreason']);
	} else {
		$reason = isset($_POST['searchreason']) ? stripslashes($_POST['searchreason']) : "";
	}	
	
	if ($reason) {
		$outres[] = $reason.'%';
		$qdata[]  = "i.reason LIKE ?";
	}
	if ($searchip) {
		$outres[] = str_replace('*', '%', $searchip);
		$qdata[]  = "i.ipmask LIKE ?";
	}
	$qwhere = $qdata ? "WHERE ".implode(" AND ", $qdata) : "";
	
	// Prepare data for the page selection
	$total  = $sql->resultp("SELECT COUNT(*) FROM ipbans i {$qwhere}", $outres);
	$ppp	= isset($_GET['ppp']) ? ((int) $_GET['ppp']) : 100;
	$ppp	= max(min($ppp, 500), 1);
	$_POST['page']  = isset($_POST['page']) ? ((int) $_POST['page']) : 0;
	$pagelist       = pageselect($total, $ppp); // Needs to be here, since it will fix the page number in case it's too high

	$bans  = $sql->prepare("
		SELECT i.ipmask, i.hard, i.expires, i.banner, i.reason, ".userfields()."
		FROM ipbans i
		LEFT JOIN users u ON i.banner = u.name
		{$qwhere}
		ORDER BY i.ipmask ASC
		LIMIT ".($_POST['page'] * $ppp).",{$ppp}
	", $outres);
	

	
	// Cookie status messages
	$cookiemsg = "";
	if (isset($_COOKIE['pstbon'])) {
		switch ($_COOKIE['pstbon']) {
			case -1: $cookiemsg = cookiemsg("Message", "Successfully added an IP ban."); break;
			case -2: $cookiemsg = cookiemsg("Message", "No bans selected for removal."); break;
			default: $cookiemsg = cookiemsg("Message", "Removed {$_COOKIE['pstbon']} IP ban".($_COOKIE['pstbon'] == 1 ? "" : "s")."."); break;
		}
	}
	
	pageheader("IP Bans");
	$auth_tag = auth_tag();
	
print "{$cookiemsg}
	<form method='POST' action='?ppp={$ppp}'>
	$L[TBL1]>
		$L[TRh]>
			$L[TDh] style='width: 120px'>&nbsp;</td>
			$L[TDh]>&nbsp;</td>
		</tr>
		$L[TR1]>
			$L[TD1c]><b>Search IP Mask:</b></td>
			$L[TD2]>
				$L[INPt]='searchip' size=10 maxlength=32 value=\"".htmlspecialchars($searchip)."\">
				<small>use * as wildcard</small>
			</td>
		</tr>
		$L[TR1]>
			$L[TD1c]><b>Reason:</b></td>
			$L[TD2]>
				$L[INPt]='searchreason' size=72 value=\"".htmlspecialchars($reason)."\"> or special: 
				<select name='setreason'>
					<option value=''></option>
					<option value='Banned'>Generic ban</option>
					<option value='online.php ban'>Online users ban</option>
				</select>
			</td>
		</tr>
		$L[TR1]>
			$L[TD1c]><b>View page:</b></td>
			$L[TD2]>{$pagelist}</td>
		</tr>
		<tr>$L[TD2] colspan='2'>$L[INPs]='dosearch' value='Search'></td></tr>
	</table>
	</form>
	<form method='POST' action='?ppp={$ppp}'>
	$L[TBL2]>
		$L[TRh]>
			$L[TDhc] style='width: 30px'>#</td>
			$L[TDhc]>IP Mask</td>
			<!-- $L[TDhc] style='width: 200px'>Ban date</td> -->
			$L[TDhc] style='width: 350px'>Expiration date</td>
			$L[TDhc] style='width: 90px'>Hard Ban</td>
			$L[TDhc]>Reason</td>
			$L[TDhc]>Banned by</td>
		</tr>
";

	for ($i = 0; $x = $sql->fetch($bans); ++$i) {
		$n = ($i%2)+1;
		$tr  = $L['TR'.$n];
		$td  = $L['TD'.$n];
		$tdc = $L['TD'.$n.'c'];
		
		print "
		$tr>
			$tdc>$L[INPc]='delban[]' value=\"".urlencode(encryptpwd($x['ipmask'].",".$x['expires'].",".$x['hard']))."\"></td>
			$tdc>".ipfmt($x['ipmask'])."</td>
			$tdc>".($x['expires'] ? cdate($dateformat, $x['expire'])." (".timeunits2($x['expires']-ctime()).")" : "Never")."</td>
			$tdc><span style='color: ".($x['hard'] ? "red'>Yes" : "green'>No")."</span></td>
			$td>" .($x['reason'] ? htmlspecialchars($x['reason']) : "None")."</td>
			$tdc>".($x['name'] ? userlink($x) : $x['banner'])."</td>
		</tr>
		";
	}

print "
		$L[TR1]>$L[TD1] colspan='6'>$L[INPs]='dodel' value='Delete selected'>{$auth_tag}</td></tr>
	</table>
	</form>
	<form method='POST' action='?ppp={$ppp}'>
	$L[TBL1] id='addban'>
		$L[TRh]>$L[TDhc] colspan='2'><b>Add IP ban</b></td></tr>
		
		$L[TR1]>
			$L[TD1c] style='width: 120px'><b>IP Mask:</b></td>
			$L[TD2]>
				$L[INPt]='newip' value=\"".htmlspecialchars($_GET['newip'])."\">
				<small>use * as a wildcard (ie: 192.168.*)</small>
			</td>
		</tr>
		$L[TR1]>
			$L[TD1c]><b>Ban reason:</b></td>
			$L[TD2]>$L[INPt]='reason' style='width: 500px' value=\"".htmlspecialchars($_GET['newreason'])."\"></td>
		</tr>
		$L[TR1]>
			$L[TD1c]><b>Expires:</b></td>
			$L[TD2]>".bantimeselect('expire', 0)."</td>
		</tr>
		$L[TR1]>
			$L[TD1c]><b>Options:</b></td>
			$L[TD2]>$L[INPc]='hard' id='hard' value=1><label for='hard'>Hard ban</label></td>
		</tr>
		$L[TR2]>$L[TD2] colspan='2'>$L[INPs]='ipban' value='IP Ban'>{$auth_tag}</td></tr>
	</table>
	</form>
";
	pagefooter();
	

function ipfmt($a) {
	$a = str_replace("%", "*", $a);
	if (strpos($a, ':') === false) {
		$expl = explode(".", $a);
		$dot = "<font~color=#808080>.</font>";
		return str_replace("~", " ", str_replace(" ", "&nbsp;", sprintf("%3s%s%3s%s%3s%s%3s", $expl[0], $dot, $expl[1], $dot, $expl[2], $dot, $expl[3])));
	} else { // lol ipv6
		return str_replace(":", "<font color=#808080>:</font>", $a);
	}
}