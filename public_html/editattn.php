<?php

	$nourltracker = 1;
	include "lib/common.php";

	if (!has_perm('edit-attentions-box')) {
		error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
	}
	
	if (isset($_POST['action'])) {
		check_token($_POST['auth']);
		
		$_POST['attention']      = stripslashes($_POST['attention']);
		$_POST['attentiontitle'] = stripslashes($_POST['attentiontitle']);
		
		if ($_POST['action'] == "Submit") {
			$sql->prepare("UPDATE misc SET attentiontitle = ?, attention = ?", array($_POST['attentiontitle'],$_POST['attention']));
			redirect("?", "The attention box settings have been saved.", "Message", "attention box editor");
		} else if ($_POST['action'] == "Preview") {
			$mockboardlogo = "
			$L[TBL] width=100%>
				$L[TRc]>
					$L[TD] style='border:none!important' valign=\"center\"></td>
					$L[TD] style='border:none!important' valign=\"center\" width=\"300\">
						<!-- start of attention box preview -->
						$L[TBL1] width=\"100%\" align=\"center\">
							$L[TRh]>$L[TDh]><font color='red'><i>Preview </i></font>{$_POST['attentiontitle']}</td></tr>
							$L[TR2] align=\"center\">
								$L[TDs]>{$_POST['attention']}</td>
							</tr>
						$L[TBLend]
						<!-- end of attention box preview -->
					</td>
				</tr>
			$L[TBLend]
			<br/>";
		}
	} else {
		$_POST['attention']      = $misc['attention'];
		$_POST['attentiontitle'] = $misc['attentiontitle']; 
	}
	
    pageheader("Edit attention box");
	
    print "
{$cookiemsg}".checkvar('mockboardlogo')."
<form method='POST' action='editattn.php'>
$L[TBL1]>
	$L[TRh]>$L[TDh] colspan='2'>Edit Attention Box</td></tr>
	$L[TR]>
		$L[TD1c]><b>Title:</b></td>
		$L[TD2]>$L[INPt]='attentiontitle' style='width: 300px' value=\"".htmlval($_POST['attentiontitle'])."\"></textarea></td>
	</tr>
	$L[TR]>
		$L[TD1c]><b>Message:</b></td>
		$L[TD2]>$L[TXTa]='attention' rows=8 cols=120>".htmlval($_POST['attention'])."</textarea></td>
	</tr>
	$L[TR]>
		$L[TD1c]></td>
		$L[TD2]>
			$L[INPs]='action' value='Preview'>
			$L[INPs]='action' value='Submit'>
			".auth_tag()."
		</td>
	</tr>
$L[TBLend]
</form>";


  pagefooter();
