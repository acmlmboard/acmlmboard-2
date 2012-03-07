<?php
	require 'lib/common.php';
	needs_login(1);
	$targetuserid = $loguser['id'];

	if (!can_edit_user($targetuserid)) $targetuserid = 0;

	if ($targetuserid == 0 || $act && ($act != 'Save and continue' || $act != 'Save and finish')) {
		pageheader('No permission');
		no_perm();
	}
	
	$act=$_POST[action];
	if ($act == 'Save and continue' || $act == 'Save and finish') {
		if ($_POST['post_radar_add'] != -1) {
			$user_add = $sql->fetchq('SELECT id FROM users WHERE id = '.$_POST['post_radar_add']);
			if ($sql->numrows($sql->query('SELECT user2_id FROM post_radar WHERE user_id = '.$targetuserid.' AND user2_id = '.$_POST['post_radar_add'].' AND dtime IS NULL')) != 0) {
				pageheader('Post Radar');
				print "<a href=./>Main</a> - Profile
	".        "<br><br>
	".        "$L[TBL1]>
	".        "  $L[TD1c]>
	".        "This user is already in your post radar.
	".        "$L[TBLend]
	";
				pagefooter(); die();
			}
			if (!$user_add) {
				pageheader('Post Radar');
				print "<a href=./>Main</a> - Profile
	".        "<br><br>
	".        "$L[TBL1]>
	".        "  $L[TD1c]>
	".        "This user does not exist!
	".        "$L[TBLend]
	";
				pagefooter(); die();
			}
			$qr= 'INSERT INTO `post_radar` ( `user_id` , `user2_id` , `ctime` )
VALUES (
'.addslashes($targetuserid).', '.addslashes($_POST['post_radar_add']).', UNIX_TIMESTAMP( ))';
			$sql->query($qr);
		}
		if ($_POST['post_radar_rem'] != -1) {
			$user_rem = $sql->fetchq('SELECT id FROM users WHERE id = '.$_POST['post_radar_rem']);
			if (!$user_rem) {
				pageheader('Post Radar');
				print "<a href=./>Main</a> - Profile
	".        "<br><br>
	".        "$L[TBL1]>
	".        "  $L[TD1c]>
	".        "This user does not exist!
	".        "$L[TBLend]
	";

				pagefooter(); die();
			}
			$qr = 'SELECT user2_id FROM post_radar WHERE user_id = '.$targetuserid.' AND user2_id = '.$_POST['post_radar_rem'].' AND dtime IS NULL';
			if ($sql->numrows($sql->query($qr)) == 0) {
				pageheader('Post Radar');
				print "<a href=./>Main</a> - Profile
	".        "<br><br>
	".        "$L[TBL1]>
	".        "  $L[TD1c]>
	".        "This user is not in your Post Radar.
	".        "$L[TBLend]
	";
				pagefooter(); die();
			}
			$qr= 'UPDATE `post_radar` SET `dtime` = UNIX_TIMESTAMP( ) WHERE user_id = '.$targetuserid.' AND user2_id = '.$_POST['post_radar_rem'];
			$sql->query($qr);
		}
	}
	pageheader('Post Radar');
	if (!$act || $act == 'Save and continue') {
		print "$L[TBL1]><form action='postradar.php' method='post' enctype='multipart/form-data'>".catheader('Edit Post Radar');
		$radar_users = list_post_radar(retrieve_post_radar_alpha($targetuserid));
		
		$res = $sql->query('select id,name,posts FROM users ORDER BY name');
		while ($r=$sql->fetch($res)) $ulist[$r['name']]= $r;
		
		$uchoices[-1] = 'Do not add anyone';
		foreach ($ulist as $z => $k) $uchoices[$k['id']] = $z.' -- '.$k['posts'].' posts';
		$radar_remlist = array();
		foreach ($radar_users as $k) {
			$radar_remlist[$k['uid']] = $k['name'].' -- '.$k['num_posts'].' posts';
		}
		print fieldrow('Add an user',fieldselect('post_radar_add','-1',array_diff_key($uchoices,$radar_remlist)));
		$radar_remlist = array(-1 => 'Do not remove anyone') + $radar_remlist;
		print fieldrow('Remove an user',fieldselect('post_radar_rem','-1',$radar_remlist));
		print catheader('&nbsp;')."
".        "  $L[TR1]>
".        "    $L[TD]>&nbsp;</td>
".        "    $L[TD]>$L[INPs]=action value='Save and continue'> $L[INPs]=action value='Save and finish'></td>
".        " </form>
".        "$L[TBLend]
";
	} else if ($act == 'Save and finish') {
		print "$L[TBL1]>
".        "  $L[TD1c]>
".        "    <font color='#FF0000' style='font-weight: bold' />$error</font>
".        "    Post Radar saved!<br>
".        "    ".redirect("index.php",'the forum Index')."
".        "$L[TBLend]
";
	}
	pagefooter();
?>