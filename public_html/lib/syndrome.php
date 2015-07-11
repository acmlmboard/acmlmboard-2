<?php

function syndrome($num) {
	$a = '>Affected by';
	$syn = '';
	if ($num >= 75)
		$syn = "83F3A3$a 'Reinfors Syndrome'";
	if ($num >= 100)
		$syn = "FFE323$a 'Reinfors Syndrome' +";
	if ($num >= 150)
		$syn = "FF5353$a 'Reinfors Syndrome' ++";
	if ($num >= 200)
		$syn = "CE53CE$a 'Reinfors Syndrome' +++";
	if ($num >= 250)
		$syn = "8E83EE$a 'Reinfors Syndrome' ++++";
	if ($num >= 300)
		$syn = "BBAAFF$a 'Wooster Syndrome'!!";
	if ($num >= 350)
		$syn = "FFB0FF$a 'Wooster Syndrome' +!!";
	if ($num >= 400)
		$syn = "FFB070$a 'Wooster Syndrome' ++!!";
	if ($num >= 450)
		$syn = "C8C0B8$a 'Wooster Syndrome' +++!!";
	if ($num >= 500)
		$syn = "A0A0A0$a 'Wooster Syndrome' ++++!!";
	if ($num >= 500)
		$syn = "A0A0A0$a 'Wooster Syndrome' ++++!!";
	if ($num >= 600)
		$syn = "C762F2$a 'Anya Syndrome'!!!";
	if ($num >= 800)
		$syn = "D06030$a 'Something higher than Anya Syndrome' +++++!!";
	if (!empty($syn))
		$syn = "<i><font color=$syn</font></i>";
	return $syn;
}