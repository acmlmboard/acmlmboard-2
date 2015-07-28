<?php
/* META
  Theme of the Day
  Rotates through every valid theme. Changes theme daily.
 */
require "../lib/classes/mysql.php";
require "../lib/datetime.php";
require "../lib/config.php";
require "../lib/database.php";

$themes = array();
$result = $sql->query("SELECT * FROM `themes` WHERE `disabled` = 0 AND `filename` != 'totd.php';");
while($row = $sql->fetch($result)) {
	$themes[] = $row;
}

$ts = time();
$theme_count = count($themes) - 1;
$theme_id = floor($ts / 86400) % $theme_count;

// verify the themes array is populated, the theme selected exists, and that the file itself exists.
// otherwise fallback to default.
if(count($themes) > 0 && isset($themes[$theme_id]['filename']) && file_exists($themes[$theme_id]['filename'])) {
	header("Location: {$themes[$theme_id]['filename']}");
} else {
	header("Location: 0.css");
}
?>