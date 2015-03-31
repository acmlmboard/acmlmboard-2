<?php
include('lib/common.php');
include('lib/diff/Diff.php');
include('lib/diff/Diff/Renderer/inline.php');

pageheader();

$pid=$_GET[id];
$r1=$_GET[o];
$r2=$_GET[n];

if(!can_view_forum_post_history(getforumbythread($pid[thread]))) { pagefooter(); die(); }

if(!$r1||!$r2) $r1=$r2=1;

$d1=$sql->fetchq("SELECT text FROM poststext WHERE id=$pid AND revision=$r1");
$d2=$sql->fetchq("SELECT text FROM poststext WHERE id=$pid AND revision=$r2");

echo "$L[TBL1] width=100% height=100>$L[TR1]>$L[TD2]><font face='courier new'>";

$diff = &new Text_Diff("native",array(explode("\n",$d1[text]),explode("\n",$d2[text])));

?>
<style type=text/css>
del {
	text-decoration: none;
	background-color: #800000;
	border: 1px dashed #FF0000;
	color: #cfcfcf;
	margin-left:1px;
	padding-left:1px;
	padding-right:1px;
}
ins {
	text-decoration: none;
	background-color: #008000;
	border: 1px dashed #00FF00;
	color: #ffffff;
	margin-left:1px;
	padding-left:1px;
	padding-right:1px;
}
</style>
<?

$renderer = &new Text_Diff_Renderer_inline();
if($act=="hs") {
	$ip=$sql->fetchq("SELECT ip FROM users WHERE id=$pid");
	$ip=$ip[0];
	echo $ip." = ".gethostbyaddr($ip);
} else echo str_replace("\n","<br>",$renderer->render($diff));

//echo diff(str_replace("\n","<br>\n",$d1[text])."\n",str_replace("\n","<br>\n",$d2[text])."\n");
echo "$L[TBLend]";

pagefooter();

?>
