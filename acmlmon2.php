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

$headers = array
(
	"id" => array //Entry key is used in $data to bind fields
	(
		"caption" => "#",
		"width" => "32px",
		"align" => "center",
		"color" => 1
	),
	"img" => array("caption"=>"Image", "width"=>"32px", "color"=>2),
	"name" => array("caption"=>"Name", "align"=>"center", "color"=>1),
	"flavor" => array("caption"=>"Description", "color"=>2),

	//Hidden flag could be used for admin-only columns.
	"secretbuttfun" => array("caption"=>"You can't see this one!", "hidden"=>true),
);

$data = array();
$monReq = $sql->query("SELECT * FROM acmlmon ORDER BY id ASC");
while($mon = $sql->fetch($monReq))
{
	if($captures[$mon['id']])
	{
		$data[] = array
		(
			"id" => $mon['id'],
			"img" => "<img src=\"img/acmlmon/".$mon['pic']."\" title=\"".$mon['title']."\" alt=\"\" />",
			"name" => $mon['name'],
			"flavor" => $mon['flavor'],
		);
	}
	else
	{
		$data[] = array
		(
			"id" => $mon['id'],
			"img" => "&nbsp;",
			"name" => "???",
			"flavor" => "&nbsp;"
		);
	}
}

$data[6]['secretbuttfun'] = "PONIES AND PONIES AND PONIES AND PONIES...";

RenderTable($data, $headers);

pagefooter();


//When comparing overall code size, please imagine this function is in lib/layout.php or summin'.

function RenderTable($data, $headers)
{
	$zebra = 0;
	$cols = count($header);
	
	print "<table cellspacing=\"0\" class=\"c1\">\n";
	print "\t<tr class=\"h\">\n";
	foreach($headers as $headerID => $headerCell)
	{
		if($headerCell['hidden'])
			continue;
	
		if(isset($headerCell['width']))
			$width = " style=\"width: ".$headerCell['width']."\"";
		else
			$width = "";
			
		print "\t\t<td class=\"b h\"".$width.">".$headerCell['caption']."</td>\n";
	}
	print "\t</tr>\n";
	foreach($data as $dataCell)
	{
		print "\t<tr>\n";
		foreach($dataCell as $id => $value)
		{
			if($headers[$id]['hidden'])
				continue;

			$color = $zebra + 1;
			$align = "";
			if(isset($headers[$id]['color']))
				$color = $headers[$id]['color'];
			if(isset($headers[$id]['align']))
				$align = " style=\"text-align: ".$headers[$id]['align']."\"";
			print "\t\t<td class=\"b n".$color."\"".$align.">".$value."</td>\n";
		}
		print "\t</tr>\n";
		$zebra = ($zebra + 1) % 2;
	}
	print "</table>\n"; 
}

?>