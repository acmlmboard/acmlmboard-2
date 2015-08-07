<?php

require("lib/common.php");
pageheader();

$inserted_rows = 0;
$files = scandir("css");
sort($files);

print "
	<table cellspacing=\"0\" class=\"c1\">
		<tr class=\"h\">
			<td class=\"b h\" colspan=2>Update Themes</td>
		</tr>
		<tr>
			<td class=\"b n1\">
				<p>Scanning for new themes...</p>
				<ul>
";

foreach ($files as $file_name) {
	if ($file_name[0] == ".") {
		continue;
	}
	
	$file_hash = md5_file("css/$file_name");
	$file_content = file_get_contents("css/$file_name");
	$file_content = str_replace("\r\n", "\n", $file_content);
	
	if (preg_match("~/* META\n(.*?)\n(.*?)\n*/\n~s", $file_content, $matches)) {
		$theme_name = trim($matches[1]);
		$theme_description = trim(substr($matches[2], 0, -2));
		$theme_base_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name); // theme without file extension
		
		if(!empty($theme_description)) {
			$sql->prepare_query("INSERT INTO `themes` (`name`, `description`, `basename`, `filename`, `filehash`) VALUES (?, ?, ?, ?, ?)",
				array($theme_name, $theme_description, $theme_base_name, $file_name, $file_hash));
		} else {
			$sql->prepare_query("INSERT INTO `themes` (`name`, `basename`, `filename`, `filehash`) VALUES (?, ?, ?, ?)",
				array($theme_name, $theme_base_name, $file_name, $file_hash));
		}
		printf("<li>Inserting `%s` into `themes`...</li>\n", htmlentities($theme_name));
		if($sql->affected_rows() > 0) {
			$inserted_rows += 1;
		}
	}
}
print "</ul>\n";

$theme_count = $sql->query_result("SELECT COUNT(*) FROM `themes`;");
print "<p><strong>Currently installed themes: </strong>$theme_count</p>\n";

print "</td>\n";
print "</tr>\n";
print "</table>\n";


pagefooter();
?>