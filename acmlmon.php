<?php

if(isset($_GET['catch']))
{
	$monID = (int)$_GET['catch'];
	$userID = (int)$_COOKIE['user'];
	if($userID == 0) die("Not logged in.");
	require("lib/config.php");
	mysql_connect($sqlhost,$sqluser,$sqlpass) or die("Couldn't connect to MySQL server.");
	mysql_select_db($sqldb) or die("Couldn't find MySQL database.");
	mysql_query("INSERT IGNORE INTO acmlmon_captures VALUES(".$userID.", ".$monID.")") or die("Could not register capture.");
	if(mysql_affected_rows() == 1)
	{
		$monName = mysql_result(mysql_query("SELECT name FROM acmlmon WHERE id=".$monID), 0, 0);
		die("Congratulations. You caught ".$monName."!");
	}
	die("OK");
}

require("lib/common.php");
pageheader();
if(!$log)
{
	print "
	$L[TBL1]>
		$L[TD1c]>
			You must be logged in to check your Acmlmon!<br>
			<a href=./>Back to main</a> or <a href=login.php>login</a>
	$L[TBLend]
";
	pagefooter();
	die();
}

$captureReq = $sql->query("SELECT monid FROM acmlmon_captures WHERE userid = ".$loguser['id']);
$captures = array();
while($capt = $sql->fetch($captureReq))
	$captures[$capt['monid']] = true;

$monReq = $sql->query("SELECT * FROM acmlmon ORDER BY id ASC");

$l = "";
while($mon = $sql->fetch($monReq))
{
	if($captures[$mon['id']])
	{
		$l .= "
	$L[TRc]>
		$L[TD1]>$mon[id]</td>
		$L[TD2l]><img src=\"img/acmlmon/$mon[pic]\" title=\"$mon[title]\" /></td>
		$L[TD1]>$mon[name]</td>
		$L[TD2l]>$mon[flavor]</td>
";
	}
	else	
	{
		$l .= "
	$L[TRc]>
		$L[TD1]>$mon[id]</td>
		$L[TD2l]>&nbsp;</td>
		$L[TD1]>???</td>
		$L[TD2l]>&nbsp;</td>
";
	}
}

print "
$L[TBL1]>
	$L[TRh]>
		$L[TDh] width=32>#</td>
		$L[TDh] width=32>Image</td>
		$L[TDh]>Name</td>
		$L[TDh]>Description</td>
$l
$L[TBLend]
";

pagefooter();

?>