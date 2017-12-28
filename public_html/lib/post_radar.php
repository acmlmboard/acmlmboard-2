<?php
// knuck

/*	modified dbs:
 *		perm,
 *		x_perm,
 *		post_radar (added),
 */

function retrieve_post_radar($u, $sort='num_posts') {
	global $sql;
	$res=$sql->query("SELECT ".userfields('u','u').",u.minipic AS uminipic,u.posts num_posts
						 FROM post_radar
						 LEFT JOIN users u ON u.id = post_radar.user2_id
						 WHERE post_radar.user_id =$u
						 AND dtime IS NULL ORDER BY $sort DESC"
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

// [Mega-Mario] removing $u parameter -- would this ever be used for anybody else than $loguser?
function build_postradar() {
	global $sql, $loguser;
	$res=retrieve_post_radar($loguser['id']);
	$rcnt=$sql->numrows($res);
	$radar_res = NULL;
	if ($rcnt > 0) {
		$your_count = $loguser['posts'];
		$radar_res = "You are ";
		
		for ($i = 0; $i < $rcnt;$i++) {
			$cur_radar = $sql->fetch($res);
			$rdif = $your_count-$cur_radar["num_posts"];
			$con_str = ($i >= ($rcnt-2)?' and ':', ');
			if ($rdif > 0) {
				$radar_res .= $rdif." ahead of ".userlink($cur_radar, 'u',1).' ('.$cur_radar['num_posts'].')';
			} else if ($rdif < 0) {
				$radar_res .= abs($rdif)." behind ".userlink($cur_radar, 'u',1).' ('.$cur_radar['num_posts'].')';
			} else {
				$radar_res .= " tied with ".userlink($cur_radar, 'u',1).' ('.$cur_radar['num_posts'].')';
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