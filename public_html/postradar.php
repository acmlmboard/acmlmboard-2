<?php
	require 'lib/common.php';
	needs_login(1);
	
	//Prevent SQLi
	$targetuserid = intval($loguser['id']);

	if (!can_edit_user($targetuserid)) $targetuserid = 0;

	if ($targetuserid == 0 || $act && ($act != 'Save and continue' || $act != 'Save and finish')) {
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
	}
	
	$act=$_POST[action];
	if ($act == 'Save and continue' || $act == 'Save and finish') {
	
	    //Prevent SQLi
		$radar_add = intval($_POST['post_radar_add']);
		$radar_rem = intval($_POST['post_radar_rem']);
		
		if ($radar_add != -1) {
			$user_add = $sql->fetchq('SELECT id FROM users WHERE id = '.$radar_add);
			if (!$user_add) {
                              error("Error", "This user does not exist!");
			}
			if ($sql->numrows($sql->query('SELECT user2_id FROM post_radar WHERE user_id = '.$targetuserid.' AND user2_id = '.$radar_add.' AND dtime IS NULL')) != 0) {
                              error("Error", "This user is already in your post radar.");
			}
			
			$qr= 'INSERT INTO `post_radar` ( `user_id` , `user2_id` , `ctime` )
VALUES (
'.$targetuserid.', '.$radar_add.', UNIX_TIMESTAMP( ))';
			$sql->query($qr);
		}
		if ($radar_rem != -1) {
			$user_rem = $sql->fetchq('SELECT id FROM users WHERE id = '.$radar_rem);
			if (!$user_rem) {
                              error("Error", "This user does not exist!");
			}
			$qr = 'SELECT user2_id FROM post_radar WHERE user_id = '.$targetuserid.' AND user2_id = '.$radar_rem.' AND dtime IS NULL';
			if ($sql->numrows($sql->query($qr)) == 0) {
                              error("Error", "This user is not in your Post Radar.");
			}
			$qr= 'UPDATE `post_radar` SET `dtime` = UNIX_TIMESTAMP( ) WHERE user_id = '.$targetuserid.' AND user2_id = '.$radar_rem;
			$sql->query($qr);
		}
	}
	if (!$act || $act == 'Save and continue') {
        pageheader('Post Radar');
		print "<table cellspacing=\"0\" class=\"c1\"><form action='postradar.php' method='post' enctype='multipart/form-data'>".catheader('Edit Post Radar');
		$radar_users = list_post_radar(retrieve_post_radar($targetuserid, 'name'));
		
		$res = $sql->query('select id,name,posts FROM users ORDER BY name');
		while ($r=$sql->fetch($res)) $ulist[$r['name']]= $r;
		
		$uchoices[-1] = 'Do not add anyone';
		foreach ($ulist as $z => $k) $uchoices[$k['id']] = $z.' -- '.$k['posts'].' posts';
		$radar_remlist = array();
		foreach ($radar_users as $k) {
			$radar_remlist[$k['uid']] = $k['uname'].' -- '.$k['num_posts'].' posts';
		}
		print fieldrow('Add an user',fieldselect('post_radar_add','-1',array_diff_key($uchoices,$radar_remlist)));
		$radar_remlist = array(-1 => 'Do not remove anyone') + $radar_remlist;
		print fieldrow('Remove an user',fieldselect('post_radar_rem','-1',$radar_remlist));
		print catheader('&nbsp;')."
".        "  <tr class=\"n1\">
".        "    <td class=\"b\">&nbsp;</td>
".        "    <td class=\"b\"><input type=\"submit\" class=\"submit\" name=action value='Save and continue'> <input type=\"submit\" class=\"submit\" name=action value='Save and finish'></td>
".        " </form>
".        "</table>
";
	} else if ($act == 'Save and finish') {
        /*if($loguser[redirtype]==0){ //Classical Redirect
       $loguser['blocksprites']=1;
	pageheader('Post Radar');
		print "<table cellspacing=\"0\" class=\"c1\">
".        "  <td class=\"b n1\" align=\"center\">
".        "    <font color='#FF0000' style='font-weight: bold' />$error</font>
".        "    Post Radar saved!<br>
".        "    ".redirect("index.php",'the forum Index')."
".        "</table>
";
         } else { //Modern redirect*/
              redirect("index.php",-1);
         //}
	}
	pagefooter();
?>