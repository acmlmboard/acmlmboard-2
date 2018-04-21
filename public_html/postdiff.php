<?php
require 'lib/common.php';

$pid  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$r1   = isset($_GET['o'])  ? (int)$_GET['o']  : 0;
$r2   = isset($_GET['n'])  ? (int)$_GET['n']  : 0;
$mode = isset($_GET['m'])  ? (int)$_GET['m'] : 0;

$t = $sql->resultq("SELECT thread FROM posts WHERE id=$pid");
if (!$t) 
	error("Error", "This post does not exist.<br> <a href=./>Back to main</a>");
$f = $sql->resultq("SELECT forum FROM threads WHERE id=$t");
if (!can_view_forum_post_history($f) || !can_view_forum($f))
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");


// Get the post revisions we need
if (!$r1 || !$r2) $r1 = $r2 = 1;
$text = $sql->getresultsbykey("SELECT revision, text FROM poststext WHERE id = {$pid} AND revision IN ({$r1}, {$r2})", 'revision', 'text');
if (!isset($text[$r1]) || !isset($text[$r2]))
	error("Error", "One of the revisions you selected does not exist. <br> <a href=./>Back to main</a>"); 

// This diff library provides a few different modes
require 'lib/diff/autoload.php';
switch ($mode) {
	case 0: $granularity = new cogpowered\FineDiff\Granularity\Character; break;
	case 1: $granularity = new cogpowered\FineDiff\Granularity\Word;      break;
	case 2: $granularity = new cogpowered\FineDiff\Granularity\Sentence;  break;
	case 3: $granularity = new cogpowered\FineDiff\Granularity\Paragraph; break;
	default: error("Error", "No.");
}
$diff = new cogpowered\FineDiff\Diff($granularity);


//What is this? I don't even…
/*if($act=="hs") {
	$ip=$sql->fetchq("SELECT ip FROM users WHERE id=$pid");
	$ip=$ip[0];
	echo $ip." = ".gethostbyaddr($ip);
}*/

// Revision jump
$revs = $sql->query("SELECT revision FROM poststext WHERE id = {$pid}");
$oldrev = "";
$newrev = "";
while ($x = $sql->fetch($revs)) {
	$w1 = ($x['revision'] == $r1) ? "b" : "a";
	$w2 = ($x['revision'] == $r2) ? "b" : "a";
	$oldrev .= "<{$w1} href='?id={$pid}&o={$x['revision']}&n={$r2}&m={$mode}'>{$x['revision']}</{$w1}> ";
	$newrev .= "<{$w2} href='?id={$pid}&o={$r1}&n={$x['revision']}&m={$mode}'>{$x['revision']}</{$w2}> ";
}

pageheader("Post revision differences");

?>
<style type="text/css">
	#compare {
		font-family: "Courier New", Courier, monospace;
	}
</style>

<!-- dumb trick with displaying / hiding elements -->
<style type="text/css" id="diffmode"></style>
<script type="text/javascript">
	var diffcss = document.getElementById('diffmode');
	function showdiff(mode) {
		switch (mode) {
			case 0: diffcss.innerHTML = ""; break;
			case 1: diffcss.innerHTML = "ins {display: none}"; break;
			case 2: diffcss.innerHTML = "del {display: none}"; break;
		}
	}
</script>
<?php

print "
$L[TBL1]>
	$L[TRh]>$L[TDh] colspan=2>Revision diff for post #{$pid}</td></tr>
	$L[TR1]>
		$L[TD1c] style='width: 150px'><b>Source revision:</b></td>
		$L[TD2]>{$oldrev}</td>
	</tr>
	$L[TR1]>
		$L[TD1c]><b>Compare to:</b></td>
		$L[TD2]>{$newrev}</td>
	</tr>
	$L[TR1]>
		$L[TD1c]><b>Diff mode:</b></td>
		$L[TD2]>
			<a href='?id={$pid}&o={$r1}&n={$r2}&m=0'>Character</a> - 
			<a href='?id={$pid}&o={$r1}&n={$r2}&m=1'>Word</a> - 
			<a href='?id={$pid}&o={$r1}&n={$r2}&m=2'>Sentence</a> - 
			<a href='?id={$pid}&o={$r1}&n={$r2}&m=3'>Paragraph</a>
		</td>
	</tr>
	$L[TR1]>
		$L[TD1c]><b>Show:</b></td>
		$L[TD2]>
			<label>$L[INPr]=diffmode onclick='showdiff(0)' id='default' checked> Diff</label>
			<label>$L[INPr]=diffmode onclick='showdiff(1)'> Source revision</label>
			<label>$L[INPr]=diffmode onclick='showdiff(2)'> Dest revision</label>
		</td>
	</tr>
	$L[TRh]>$L[TDh] colspan=2 style='height: 5px'></td></tr>
	$L[TR2]>
		$L[TD1c]><b>Result:</b></td>
		$L[TD2] id='compare'>".nl2br($diff->render($text[$r1], $text[$r2]))."</td>
	</tr>
</table>
";

// Default to first element because browsers preserve choices on soft refresh, even when it doesn't make sense
?>
<script type="text/javascript">
	document.getElementById('default').checked=true;
</script>
<?php

pagefooter();