<?php
/* META
Daily Cycle (Boom.dk)

*/

date_default_timezone_set("UTC");

$tzoff = isset($_GET['tz']) ? floatval($_GET['tz']) : 0;

function srgb($r, $g, $b) {
	return array(r => $r, g => $g ,b => $b);
}
function fadesch($c, $n, $pct) {
	$pct2 = 1 - $pct;
	$ret = floor($c[$n]['r']*$pct2+$c[$n+1]['r']*$pct)*65536+floor($c[$n]['g']*$pct2+$c[$n+1]['g']*$pct)*256+floor($c[$n]['b']*$pct2+$c[$n+1]['b']*$pct);
	return $ret;
}

$curtime = getdate(time()+$tzoff);
$min = $curtime['hours'] * 60 + $curtime['minutes'];
$tbg1[1] = srgb(10, 10, 33);
$tbg2[1] = srgb(7, 7, 22);
$thb[1] = srgb(23, 23, 80);
$tbd[1] = srgb(60, 65, 166);
$cbg[1] = srgb(17, 17, 57);
$tbg1[2] = srgb(48, 6, 82);
$tbg2[2] = srgb(34, 5, 50);
$thb[2] = srgb(70, 30, 110);
$tbd[2] = srgb(118, 66, 165);
$cbg[2] = srgb(59, 18, 96);
$tbg1[3] = srgb(0, 0, 96);
$tbg2[3] = srgb(0, 0, 54);
$thb[3] = srgb(0, 80, 160);
$tbd[3] = srgb(0, 112, 192);
$cbg[3] = srgb(0, 40, 128);
$tbg1[4] = srgb(50, 10, 9);
$tbg2[4] = srgb(31, 7, 7);
$thb[4] = srgb(96, 24, 13);
$tbd[4] = srgb(190, 106, 32);
$cbg[4] = srgb( 73, 17, 11);
$tbg1[5] = $tbg1[1];
$tbg2[5] = $tbg2[1];
$thb[5] = $thb[1];
$tbd[5] = $tbd[1];
$cbg[5] = $cbg[1];
$n = floor($min/360)+1;
$pct = ($min-floor($min/360)*360)/360;
$pct2 = 1-$pct;
$tblbg1 = fadesch($tbg1, $n, $pct);
$tblbg2 = fadesch($tbg2, $n, $pct);
$tblhb = fadesch($thb, $n, $pct);
$tblbd = fadesch($tbd, $n, $pct);
$catbg = fadesch($cbg, $n, $pct);
$scr1 = floor(192+($tbd[$n]['r']*$pct2+$tbd[$n+1]['r']*$pct)*0.25)*65536+floor(192+($tbd[$n]['g']*$pct2+$tbd[$n+1]['g']*$pct)*0.25)*256+floor(192+($tbd[$n]['b']*$pct2+$tbd[$n+1]['b']*$pct)*0.25);
$scr2 = floor(128+($tbd[$n]['r']*$pct2+$tbd[$n+1]['r']*$pct)*0.50)*65536+floor(128+($tbd[$n]['g']*$pct2+$tbd[$n+1]['g']*$pct)*0.50)*256+floor(128+($tbd[$n]['b']*$pct2+$tbd[$n+1]['b']*$pct)*0.50);
$scr3 = floor(64+($tbd[$n]['r']*$pct2+$tbd[$n+1]['r']*$pct)*0.75)*65536+floor(64+($tbd[$n]['g']*$pct2+$tbd[$n+1]['g']*$pct)*0.75)*256+floor(64+($tbd[$n]['b']*$pct2+$tbd[$n+1]['b']*$pct)*0.75);
$tablebg1 = substr(dechex($tblbg1+16777216), -6);
$tablebg2 = substr(dechex($tblbg2+16777216), -6);
$tableheadbg = substr(dechex($tblhb+16777216), -6);
$tableborder = substr(dechex($tblbd+16777216), -6);
$categorybg = substr(dechex($catbg+16777216), -6);
$sc1 = substr(dechex($scr1+16777216), -6);
$sc2 = substr(dechex($scr2+16777216), -6);
$sc3 = substr(dechex($scr3+16777216), -6);

header("Content-type: text/css");
header("Cache-Control: no-cache");

echo "a:link		{color: #FFC040}
a:visited	{color: #C09030}
a:active	{color: #FFEA60}
a:hover		{color: #FFFFFF}

a:link, a:visited, a:active, a:hover {
	text-decoration: none;
	font-weight: bold;
}
body {
	background: #$tablebg2 url('../img/scan.gif');
	font-family: arial, sans-serif;
	scrollbar-3dlight-color: #$sc1;
	scrollbar-highlight-color: #$sc2;
	scrollbar-face-color: #$sc3;
	scrollbar-shadow-color: #$tableborder;
	scrollbar-darkshadow-color: #$tableheadbg;
	scrollbar-arrow-color: #$tablebg1;
	scrollbar-track-color: #$tablebg2;
}
body, table {
	color: #E0E0E0;
	font-size: 1em;
}
table.c1, table.c2 {
  width: 100%;
  border-collapse: collapse;
}
td.b {
	border: 1px solid #$tableborder;
}
td.n1, tr.n1 td {
	background: #$tablebg1;
}
td.n2, tr.n2 td {
	background: #$tablebg2;
}
td.h, tr.h td {
	background: #$tableheadbg;
}
tr.c td {
	background: #$categorybg;
}
tr.h td, tr.c td {
	text-align: center;
}
textarea, input, select, button {
	border: 1px solid #$tableheadbg;
	background-color: #000000;
	color: #E0E0E0;
	font-family: arial, sans-serif;
	font-size: 1em;
}
.submit {
	border: 2px solid #$tableheadbg;
}
input.radio {
	border: 0;
}
.sfont {
	font-size: 0.8em;
	font-family: verdana;
}

.nc0x{color:#888888} .nc1x{color:#888888} .nc2x{color:#888888}
.nc00{color:#97ACEF} .nc10{color:#F185C9} .nc20{color:#7C60B0}
.nc01{color:#D8E8FE} .nc11{color:#FFB3F3} .nc21{color:#EEB9BA}
.nc02{color:#AFFABE} .nc12{color:#C762F2} .nc22{color:#47B53C}
.nc03{color:#FFEA95} .nc13{color:#C53A9E} .nc23{color:#F0C413}";
?>
