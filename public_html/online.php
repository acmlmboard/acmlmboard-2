<?php
	require 'lib/common.php';
	
	$_GET['time'] = isset($_GET['time']) ? (int)$_GET['time'] : 0;
	if (!$_GET['time']) { // 5 minutes default
		$_GET['time'] = 300;
	}
	
	$hiddencheck  = "AND u.hidden = 0";
	if (has_perm('view-hidden-users')) {
		$hiddencheck = "";
	}
	
	$showurl = has_perm('view-user-urls');
	$showip  = has_perm('view-post-ips');

	// Since the IP flags need to be fetched anyway (if we're admins), do it here rather than HITTING THE DB FOR EACH IP
	$users = $sql->query("
		SELECT u.*".($showip ? ", f.cc2 ipflag " : "")."
		FROM users u
		".($showip ? "LEFT JOIN ip2c f ON (f.ip_from <= inet_aton(u.ip) AND f.ip_to >= inet_aton(u.ip))" : "")."
		WHERE u.lastview > ".(ctime()-$_GET['time'])." $hiddencheck
		ORDER BY u.lastview DESC
	");
	$guests = $sql->query("
		SELECT g.*".($showip ? ", f.cc2 ipflag " : "")."
		FROM guests g
		".($showip ? "LEFT JOIN ip2c f ON (f.ip_from <= inet_aton(g.ip) AND f.ip_to >= inet_aton(g.ip))" : "")."
		WHERE g.date > ".(ctime()-$_GET['time'])." 
		ORDER BY g.bot ASC, g.date DESC
	");
	
	pageheader('Online users');
	
	print "
Online users during the last ".timeunits2($_GET['time']).":<br>
<div style='margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block'>
	".timelink(60).'|'.timelink(300).'|'.timelink(900).'|'.timelink(3600).'|'.timelink(86400)."
</div>

$L[TBL1]>
	$L[TRh]>$L[TDh] colspan=7>Online users</td></tr>
	$L[TRh]>
		$L[TDh] style='width: 30px'>#</td>
		$L[TDh]>Name</td>
		$L[TDh] style='width: 90px'>Last view</td>
		$L[TDh] style='width: 140px'>Last post</td>
".($showurl ? "$L[TDh]>URL</td>":'')."
".($showip  ? "$L[TDh] style='width: 170px'>IP</td>":'')."
		$L[TDh] style='width: 50px'>Posts</td>
	</tr>";

// Process users
	for ($i = 1; $user = $sql->fetch($users); $i++) {
		// HTTPS urls are marked by !
		$user['ssl'] = ($user['url'][0] == '!');
		if ($user['ssl']) {
			$user['url'] = substr($user['url'], 1);
		}
		$user['url'] = urlformat($user['url']);
		$tr = ($i % 2 ? 'TR2' : 'TR3').'c';
		
		print "
	$L[$tr]>
		$L[TD1]>$i.</td>
		$L[TDl]>".($user['hidden'] ? '('.userlink($user).')' : userlink($user))."</td>
		$L[TD]>".cdate($loguser['timeformat'], $user['lastview'])."</td>
		$L[TD]>".($user['lastpost'] ? cdate($dateformat, $user['lastpost']) : '-')."</td>
".($showurl ? "  
		$L[TDl]>
			<span style='float:right'>".sslicon($user['ssl'])."</span>
			".($user['url'] ? "<a href=\"{$user['url']}\">{$user['url']}</a>" : '-')."
		</td>" : '')."
".($showip ? "
		$L[TD]>
			".flagip($user['ip'], $user['ipflag'])."<br>
			<small>".($user['ipbanned'] ? "(<a href='ipbans.php?ip={$user['ip']}'>IP banned</a>)" : "<a href='ipbans.php?newip={$user['ip']}&newreason=online.php%20ban#addban'>IP Ban</a>")."</small>
		</td>" : '')."
		$L[TD]>{$user['posts']}</td>
	</tr>";
	}
	
	print "
$L[TBLend]
<br>
$L[TBL1]>
	$L[TRh]>$L[TDh] colspan=5>Guests</td></tr>
	$L[TRh]>
		$L[TDh] style='width: 30px'>#</td>
		$L[TDh] style='width: 70px; min-width: 150px'>User agent (Browser)</td>
		$L[TDh] style='width: 70px'>Last view</td>
		$L[TDh]>URL</td>
".($showip ? "$L[TDh] style='width: 170px'>IP</td>":'')."
	</tr>
";

// Process guests and bots
	$onbot = false;
	for ($i = 1; $guest = $sql->fetch($guests); $i++) {
		// Guests come first, then all bots
		if (!$onbot && $guest['bot']) {
			$onbot = true;
			print "$L[TRg]>$L[TDg] colspan=5>Bots</td></tr>";
		}
		
		// HTTPS urls are marked by !
		$guest['ssl'] = ($guest['url'][0] == '!');
		if ($guest['ssl']) {
			$guest['url'] = substr($guest['url'], 1);
		}
		$guest['url'] = urlformat($guest['url']);
		
		// Only display title effect on shortened UA
		if (strlen($guest['useragent']) > 65) {
			$useragent = "<span title=\"". htmlspecialchars($guest['useragent']) ."\" style='white-space: nowrap; border-bottom: 1px dotted #fff;'>
				". htmlspecialchars(substr($guest['useragent'], 0, 65)) ."...
			</span>";
		} else {
			$useragent = htmlspecialchars($guest['useragent']);
		}
		
		$tr = ($i % 2 ? 'TR2' : 'TR3').'c';
		
		print "
	$L[$tr]>
		$L[TD1]>{$i}.</td>
		$L[TDl]>{$useragent}</td>
		$L[TD]>".cdate($loguser['timeformat'], $guest['date'])."</td>
		$L[TDl]>
			<span style='float:right'>".sslicon($guest['ssl'])."</span>
			".($guest['url'] ? "<a href=\"{$guest['url']}\">{$guest['url']}</a>" : '-')."
		</td>
".($showip ? "
		$L[TD]>".
			flagip($guest['ip'], $guest['ipflag'])."<br>
			<small>".($guest['ipbanned'] ? "(<a href='ipbans.php?ip={$guest['ip']}'>IP banned</a>)" : "<a href='ipbans.php?newip={$guest['ip']}&newreason=online.php%20ban#addban'>IP Ban</a>")."</small>
		</td>" : '')."
	</tr>";
	}
	print "$L[TBLend]";

	pagefooter();

function urlformat($url) {
	return str_replace(array("%20", "_"), " ", htmlspecialchars($url, ENT_QUOTES));
}	
function sslicon($ssl) {
	if (has_perm('view-post-ips') && $ssl) {
		return "<img src='img/ssloff.gif'>";
	}
	return "";
}
function timelink($time){
	return ($_GET['time'] == $time ? " ".timeunits2($time)." " : " <a href='online.php?time=$time'>".timeunits2($time)."</a> ");
}
  