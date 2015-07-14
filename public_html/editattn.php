<?php

include('lib/common.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$attndata = '';
$mockboardlogo = '';

if (!has_perm('edit-attentions-box')) {
	pageheader('Nothing here.');
	noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
} else {

	if ($action == "Submit") {
		$sql->query("UPDATE misc SET txtval='" . $_POST['txtval'] . "' WHERE field='attention'");
	}

	if ($action == "Preview") {
		$attndata = stripslashes($_POST['txtval']);

		$previewattn = "
                 <table cellspacing=\"0\" class=\"c1\" width=\"100%\" align=\"center\">
                   <tr class=\"h\">
                      <td class=\"b h\"><font color='red'><i>Preview </i></font>$config[atnname] $ae</td>
                    <tr class=\"n2\" align=\"center\">
                      <td class=\"b sfont\">" . $attndata . "
                      </td>
                 </table>";
		$mockboardlogo = "
       <table cellspacing=\"0\" width=100%>
         <tr align=\"center\">
           <td class=\"b\" style=\"border:none!important\" valign=\"center\"></td>
           <td class=\"b\" style=\"border:none!important\" valign=\"center\" width=\"300\">
             $previewattn
           </td>
       </table><br/>";
	} else
		$attndata = $sql->resultq("SELECT txtval FROM misc WHERE field='attention'");

	$pageheadtxt = "Edit " . $config['atnname'];
	pageheader($pageheadtxt);
	//print $previewattn."<br />";
	print $mockboardlogo;

	print "<form action=\"editattn.php\" method=\"post\">
" . "<table cellspacing=\"0\" class=\"c1\">
" . "  <tr class=\"h\">
" . "    <td class=\"b h\">
" . "      Edit $config[atnname]
" . "  <tr class=\"n1\">
" . "    <td class=\"b\">
" . "      <textarea wrap=\"virtual\" name='txtval' rows=8 cols=120>" . $attndata . "</textarea>
" . "  <tr class=\"n1\">
" . "    <td class=\"b n1\" align=\"center\">
" . "      <input type=\"submit\" class=\"submit\" name=action value=Preview>
" . "      <input type=\"submit\" class=\"submit\" name=action value=Submit>
" . "</table> </form>";
}

pagefooter();
?>
