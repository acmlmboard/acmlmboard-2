<?php

include('lib/common.php');

if (!$log) {
	needs_login(1);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
if ($action == "update") {
	$err = "";
	if (!preg_match("/^([0-9|.|,|\*]*)$/", $_POST['ranges']))
		$err = "      Range string contains illegal characters.
" . "      <a href=''>Go back</a> or <a href='index.php'>give up</a>.";

	if ($err) {
		print "<a href=./>Main</a> - Error";
		noticemsg("Error", $err);
		pagefooter();
		die();
	} else {
		$_COOKIE['pass'] = packlcookie(unpacklcookie($_COOKIE['pass']), $_POST['ranges']);
		setcookie('pass', $_COOKIE['pass'], 2147483647);
	}
}

pageheader('Advanced login cookie setup');

$dsegments = explode(",", decryptpwd($_COOKIE['pass']));

$data="<table cellspacing=\"0\" class=\"c1\" style='width:200px!important'>
" . "  <tr class=\"h\">
" . "    <td class=\"b h\" colspan=2>Current data
" . "  <tr class=\"h\">
" . "    <td class=\"b h\">Field
" . "    <td class=\"b h\">Value
" . "  <tr class=\"n1\">
" . "    <td class=\"b n1\" align=\"center\">generating IP
" . "    <td class=\"b n2\" align=\"center\">$dsegments[0]
" . "  <tr class=\"n1\">
" . "    <td class=\"b n1\" align=\"center\">password hash
" . "    <td class=\"b n2\" align=\"center\"><i>******</i>";
for ($i = 2; $i < count($dsegments);  $i++) {
	$data.="  <tr class=\"n1\">
" . "    <td class=\"b n1\" align=\"center\">allowed range
" . "    <td class=\"b n2\" align=\"center\">" . $dsegments[$i];
}
$data.="</table><br>";

print "$data
" . "<form action='lcookie.php' method='post'><input type=\"hidden\" name='action' value='update'>
" . "<table cellspacing=\"0\" class=\"c1\">
" . "  <tr class=\"h\">
" . "    <td class=\"b h\">Modify allowed ranges
" . "  <tr class=\"n1\">
" . "    <td class=\"b n2\"><input type=\"text\" name='ranges' value='" . implode(",", array_slice($dsegments, 2)) . "' style='width:80%'><input type=\"submit\" class=\"submit\" name value='Update'>
" . "            <br><font class='sfont'>Data must be provided as comma-separated IPs without spaces,
" . "            each potentially ending in a single * wildcard. (e.g. <font color='#C0C020'>127.*,10.0.*,1.2.3.4</font>)
" . "            Faulty data might result in instant self-destruction of your login cookie.</font>
" . "</table></form>";

pagefooter();
?>
