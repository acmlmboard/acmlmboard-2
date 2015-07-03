<?php

function loadsmilies() {
	global $sql,$smilies;
	$i=0;
	$s=$sql->query("SELECT * FROM smilies");
	while($smilies[$i++] = $sql->fetch($s));
		$smilies['num'] = $i;
}

?>