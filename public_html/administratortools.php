<?php

	$nourltracker = 1;
	require "lib/common.php";
	
	// message of the day:
	// txtval and intval were a terrible idea which complicated everything
	
	if (!has_perm('admin-tools-access')) 
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

	if (isset($_POST['submit'])) {
		check_token($_POST['auth']);
		
		$values = array(
			(int)$_POST['regdisable'],
			stripslashes($_POST['regdisabletext']),
			(int)$_POST['lockdown'],
			stripslashes($_POST['lockdowntext']),
			stripslashes($_POST['boardemail']),
		);
		$sql->prepare("UPDATE misc SET regdisable = ?, regdisabletext = ?, lockdown = ?, lockdowntext = ?, boardemail = ?", $values);
 
		redirect("?", "The board configuration has been updated!", "Settings saved", "admin page");
	}

	pageheader('Admin Panel');
	
	$misc = $sql->fetchq("SELECT * FROM misc");

	$welp = "";
	if (isset($_GET['test']))
		$welp = infownd("PRO TIP", "<div style='text-align:center'>You can also edit the attention box <a href='editattn.php'>here</a>.</div>");


	print "{$cookiemsg}{$welp}

<form action='administratortools.php' method='POST'>
$L[TBL1]>
	".catheader('General')."
	".fieldrow('Board mode', fieldoption('lockdown', $misc['lockdown'], array('Normal', 'Lockdown' /*, 'Read only [WIP]'*/)))."
	".fieldrow('Registration mode', fieldoption('regdisable', $misc['regdisable'], array('Normal', 'Disabled')))."
	$L[TR]>
		$L[TD1c]>Board email:</td>
		$L[TD2]>$L[INPt]='boardemail' size='40' maxlength='60' value=\"".htmlval($misc['boardemail'])."\"></td>	
	</tr> 	
	
	".catheader('Board messages')."
	$L[TR]>
		$L[TD1c]>Lockdown:</td>
		$L[TD2]>$L[TXTa]='lockdowntext' rows=5 cols=120>".htmlval($misc['lockdowntext'])."</textarea></td>	
	</tr> 
	$L[TR]>
		$L[TD1c]>Registration disabled:</td>
		$L[TD2]>$L[TXTa]='regdisabletext' rows=5 cols=120>".htmlval($misc['regdisabletext'])."</textarea></td>	
	</tr> 
	
	".catheader('Attention box')."
	$L[TR]>
		$L[TD1] colspan='2'>These settings can be changed <a href='editattn.php'>here</a>.</td>	
	</tr>
	
	$L[TR1]>
		$L[TD]>".auth_tag()."</td>
		$L[TD]>$L[INPs]=submit value='Save changes'></td>
	</tr>
</table>
</form>
";

	pagefooter();