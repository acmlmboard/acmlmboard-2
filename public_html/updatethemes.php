<?php
  $nourltracker=1;
  require("lib/common.php");

  if (!has_perm('manage-board')) error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
  pageheader("Updating Themes.");
print "Scanning for new themes...<br>";

$files = scandir("css");
sort($files);
foreach($files as $f)
{
	if($f[0] == ".")
		continue;
	$snarf = file_get_contents("css/".$f);
	$snarf = str_replace("\r\n", "\n", $snarf);
	if(preg_match("~/* META\n(.*?)\n(.*?)\n*/\n~s", $snarf, $matches))
	{
		$n = $matches[1];
		$d = substr($matches[2], 0, -2);
		//print "Got a hit on ".$f."! Its name is \"$n\", description \"$d\".<br>";
		$f2 = str_replace(".css", "", str_replace(".php", "", $f));
		if($d != "")
			$newlist[] = array($n, $f2, $d);
		else
			$newlist[] = array($n, $f2);
	}
}

file_put_contents("themes_serial.txt", serialize($newlist));

print "We now have ".count($newlist)." themes.";

pagefooter();

?>