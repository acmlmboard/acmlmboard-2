<?php

header('Content-type: application/xhtml+xml');

/*echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN\" \"http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd\">";
*/
?><html xmlns="http://www.w3.org/1999/xhtml" xmlns:svg="http://www.w3.org/2000/svg" xml:lang="en">
<head><title><?php print "$boardtitle"; ?></title></head>
<body bgcolor="#101020">

<table border="0" cellpadding="10" style="position:fixed;top:40%;left:35%;width:30%;font-family:Verdana,Arial;color:#FF6060;font-size:12px" bgcolor="#C02020">
<tr>
<td style="vertical-align:middle;height:100px;width:50px">
<svg:svg width="50" height="51" style="vertical-align:middle">
  <svg:circle cx="25" cy="26" r="25" fill="#FF6060" />
  <svg:line x1="13" y1="14" x2="37" y2="38" style="stroke:#C02020;stroke-width:5;" />
  <svg:line x1="37" y1="14" x2="13" y2="38" style="stroke:#C02020;stroke-width:5;" />
</svg:svg>
</td>

<td>
<table style="width:100%;height:100%" border="0"><tr style="height:10px"><td><font face="Courier New,Courier,Mono" size="4" color="#FF8080"><b>I AM<br />ERROR.</b></font></td></tr>
<tr><td><?php if($a['txtval'] != '') print $a['txtval']; else print "Access to the board has been restricted by the administration.<br />Please forgive any inconvenience caused and stand by until the underlying issues have been resolved.";?></td></tr>
</table>
</td>
</tr>

</table>

</body>
</html>