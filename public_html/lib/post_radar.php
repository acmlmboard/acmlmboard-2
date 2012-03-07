<?php
// knuck

/*	modified dbs:
 *		perm,
 *		x_perm,
 *		post_radar (added),
 */

function retrieve_post_radar_alpha($u) {
	global $sql;
	$res=$sql->query("SELECT u.posts num_posts, u.name, post_radar.user2_id AS uid
						 FROM post_radar
						 LEFT JOIN users u ON u.id = post_radar.user2_id
						 WHERE post_radar.user_id =$u
						 AND dtime IS NULL ORDER BY name ASC"
						);
	return $res;
}

function retrieve_post_radar($u) {
	global $sql;
	$res=$sql->query("SELECT u.posts num_posts, u.name, post_radar.user2_id AS uid
						 FROM post_radar
						 LEFT JOIN users u ON u.id = post_radar.user2_id
						 WHERE post_radar.user_id =$u
						 AND dtime IS NULL ORDER BY num_posts DESC"
						);
	return $res;
}
function list_post_radar($rlist) {
	global $sql;
	$res = array();
	while ($r=$sql->fetch($rlist)) {
		$res[] = $r;
	}
	return $res;
}

function build_postradar($u) {
	global $sql;
	$res=retrieve_post_radar($u);
	$rcnt=$sql->numrows($res);
	$radar_res = NULL;
	if ($rcnt > 0) {
		$your_count = $sql->resultq("SELECT posts FROM users WHERE id = $u");
		$radar_res = "You are ";
		
		for ($i = 0; $i < $rcnt;$i++) {
			$cur_radar = $sql->fetch($res);
			$rdif = $your_count-$cur_radar["num_posts"];
			$con_str = ($i >= ($rcnt-2)?' and ':', ');
			if ($rdif > 0) {
				$radar_res .= $rdif." ahead of ".userlink_by_id($cur_radar["uid"]).' ('.$cur_radar['num_posts'].')';
			} else if ($rdif < 0) {
				$radar_res .= abs($rdif)." behind ".userlink_by_id($cur_radar["uid"]).' ('.$cur_radar['num_posts'].')';
			} else {
				$radar_res .= " tied with ".userlink_by_id($cur_radar["uid"]).' ('.$cur_radar['num_posts'].')';
			}
			if ($i != $rcnt-1) {
				$radar_res .= $con_str;
			} else {
				$radar_res .= '.';
			}
		}
	}
	return $radar_res;
}

?>