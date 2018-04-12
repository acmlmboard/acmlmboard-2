<?php
require 'lib/common.php';

if (!has_perm('ban-users')) {
	error("Error", "You have no permissions to do this!<br> <a href='index.php'>Back to main</a>");
}

//Alternative to editing users' profiles. - SquidEmpress
//Based off of banhammer.php from Blargboard by StapleButter.

$_GET['ìd']    = isset($_GET['id']) ? ((int) $_GET['id']) : 0;
if (!$_GET['id']) {
	error("Error", "No user selected");
}

$user = $sql->fetchq("SELECT id, group_id, tempbanned FROM `users` WHERE `id` = '{$_GET['id']}'");
if (!$user) {
	error("Error", "Invalid user ID.");
} else if ((is_root_gid($user['group_id']) || (!has_perm_with_bindvalue('can-edit-group', $user['group_id']) && $_GET['id'] != $loguser['id'])) && !has_perm('no-restrictions')) {
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
} 

// Get list of banned groups
$bannedgroups = $sql->getresultsbykey("SELECT id, title FROM `group` WHERE banned = 1", 'id', 'title'); // Can be more than one apparently

if (isset($_GET['unban'])) {
	if (!isset($bannedgroups[$user['group_id']])) {
		error("Error", "This user is not banned.<br> <a href='index.php'>Back to main</a>"); 
	}
	
	if (isset($_POST['unbanuser'])) {
		check_token($_POST['auth']);
		$sql->query("UPDATE users SET group_id = '{$defaultgroup}', title = '', tempbanned = 0 WHERE id = '{$user['id']}'");
		redirect("profile.php?id={$_GET['id']}", -1); // "User has been unbanned.", 'the user'
	}
	
	pageheader('Unban User');
	$pagebar = array(
		'breadcrumb' => array(array('href'=>'index.php', 'title'=>'Index')),
		'title'      => 'Unban user',
		'actions'    => array(),
		'message'    => ''
	);
	RenderPageBar($pagebar);
	print "<form action='?unban&id={$_GET['id']}' method='post'> 
".    "$L[TBL1]>
".    "  $L[TRh]>$L[TD]>Unban User
".    "  $L[TR]>$L[TD1c]>
".    "    Are you sure?
".        "  $L[TR1]>
".        "    $L[TD1c]>
".        "      $L[INPs]='unbanuser' value='Unban User'> - <a href='profile.php?id={$_GET['id']}'>Cancel</a>".auth_tag()."
".    "$L[TBLend]
";
} else {
	$message = isset($bannedgroups[$user['group_id']]) ? "This user is already banned. Continuing will replace the ban information." : "";
	
	if (isset($_POST['banuser'])) {
		check_token($_POST['auth']);
		
		$_POST['tempbanned'] = isset($_POST['tempbanned']) ? ((int) $_POST['tempbanned']) : 0;
		$_POST['destgroup']  = isset($_POST['destgroup']) ? ((int) $_POST['destgroup']) : 0;
		$_POST['title']      = isset($_POST['title']) ? $_POST['title'] : "";
		
		// Sanity check. Do not allow tampering.
		if (!isset($bannedgroups[$_POST['destgroup']])) {
			error("Nice try", "This is not a banned group.");
		}
		// This should ideally be *LIVE* but whatever
		if ($_POST['tempbanned'] > 0) {
			$banreason = "Banned until ".date("m-d-y h:i A" ,ctime()+$_POST['tempbanned']);
		} else {
			$banreason = "Banned permanently";
		}
		// Include the ban reason, if present
		if ($_POST['title']) {
			$banreason .= ': '.htmlspecialchars($_POST['title']);
		}
		
		$sql->prepare("UPDATE users SET group_id = ?, title = ?, tempbanned = ? WHERE id = '{$user['id']}'",
			array($_POST['destgroup'], $banreason, $_POST['tempbanned'] ? $_POST['tempbanned'] + ctime() : 0)
		);
		redirect("profile.php?id={$_GET['id']}",-1); // "User has been banned.", 'the user'
	}
	
	pageheader('Ban User');
	$pagebar = array(
		'breadcrumb' => array(array('href'=>'index.php', 'title'=>'Index')),
		'title'      => 'Ban user',
		'actions'    => array(),
		'message'    => $message,
	);
	RenderPageBar($pagebar);
	
	print "<form action='?id={$_GET['id']}' method='post'> 
	".    "$L[TBL1]>
	".
			catheader('Ban User')."
	".        "  $L[TR]>
	".        "    $L[TD1c]>Reason:</td>
	".        "      $L[TD2]>$L[INPt]='title' class='right'></td>
	".        "  $L[TR]>
	".        "    $L[TD1c]>Expires?</td>
	".        "      $L[TD2]>".bantimeselect("tempbanned", $user['tempbanned'])."</td>
	".        "  $L[TR]>
	".        "  $L[TR1]>
	".        "    $L[TD1c]>Destination group:</td>
	".        "      $L[TD2]>".fieldselect("destgroup", $user['group_id'], $bannedgroups)."</td>
	".        "  $L[TR]>
	".        "  $L[TR1]>
	".        "    $L[TD]>&nbsp;</td>
	".        "    $L[TD]>
	".        "      $L[INPs]='banuser' value='Ban User'>".auth_tag()."
	".    "$L[TBLend]
	";
}

pagefooter();