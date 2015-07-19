<?php

/**
 * An object for a Forum.
 *
 * @author Xeon
 */
class Forum {
	static function markRead($forum_id, $user_id) {
		global $sql;
		//delete obsolete threadsread entries
		if($stmt = $sql->db->prepare("DELETE r FROM threadsread r LEFT JOIN threads t ON t.id = r.tid WHERE t.forum = ? AND r.uid = ?;")) {
			$stmt->bind_param("ii", $forum_id, $user_id);
			$stmt->execute();
			$stmt->close();
		}
		//add new forumsread entry
		if($stmt = $sql->db->prepare("REPLACE INTO forumsread VALUES (?, ?, UNIX_TIMESTAMP());")) {
			$stmt->bind_param("ii", $user_id, $forum_id);
			$stmt->execute();
			$stmt->close();
		}
	}
	
	static function markAllRead($user_id) {
		global $sql;
		//clear out all threads read entries for uid.
		if($stmt = $sql->db->prepare("DELETE FROM `threadsread` WHERE `uid` = ?;")) {
			$stmt->bind_param("i", $user_id);
			$stmt->execute();
			$stmt->close();
		}
		//mark all forums read
		if($stmt = $sql->db->prepare("REPLACE INTO `forumsread` (`uid`, `fid`, `time`) SELECT ?, f.id, UNIX_TIMESTAMP() FROM forums f")) {
			$stmt->bind_param("i", $user_id);
			$stmt->execute();
			$stmt->close();
		}
	}
	
	static function getIgnoredForums($user_id) {
		global $sql;
		$return_value = array();
		if($stmt = $sql->db->prepare("SELECT * FROM `ignoredforums` WHERE `uid` = ?;")) {
			$stmt->bind_param("i", $user_id);
			if($stmt->execute()) {
				$result = $stmt->get_result();
				$return_value = $result->fetch_all(MYSQLI_ASSOC);
			}
			$stmt->close();
		}
		return $return_value;
	}
}
