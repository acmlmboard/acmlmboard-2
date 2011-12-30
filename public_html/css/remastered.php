<?php
/* META
Remastered Cycle (Nikolaj)

*/

//Remastered Cycle theme.
header("Content-type: text/css");
header("Cache-Control: no-cache");

$hue = (date('H', time()) + (date('i', time())+$_GET[minover] / 100)) % 360;
$css = "
a {-o-transition: 0.1s linear;}
A:link {
	color: #FFC040;
}
A:visited {
	color: #C09030;
}
A:active {
	color: #FFEA60;
}
A:hover {
	color: #FFFFFF;
}

A:link,A:visited,A:active,A:hover{
	text-decoration:none;
	font-weight:bold;
}

body{
	background: hsl([h], 50%, 50%) url(../theme/remastered/back.png);
	color: white;
	font-family: 'Segoe UI', Tahoma, Arial;
}

table {
	font-size: 1em;
}

table.c1{
	width: 100%;
	background: hsl([h], 50%, 11%);
	border: hsl([h], 50%, 11%) 1px solid;
	background-image: -o-linear-gradient(hsl([h], 50%, 20%), hsl([h], 50%, 10%));
	box-shadow: 0px 0px 10px hsl([h], 50%, 50%) , inset 0px 0px 5px #000;
	border-radius: 5px;
}

table.c2{
	width: 100%;
	background: hsl([h], 50%, 11%);
	border: hsl([h], 50%, 11%) 1px solid;
	background-image: -o-linear-gradient(hsl([h], 50%, 20%), hsl([h], 50%, 10%));
	font-size: 1em;
	box-shadow: 0px 0px 5px hsl([h], 50%, 50%) , inset 0px 0px 5px #000;
}

tr.h{
	background: -o-linear-gradient(rgba(0, 0, 0, 0.50), rgba(0, 0, 0, 0.25));
	text-align: center;
	font-size: 1em;
	font-weight: bold;
}

tr.c{
	background: -o-linear-gradient(rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.50));
	text-align: center;
	font-size: 1em;
}

td.b{
	border-left: hsl([h], 50%, 10%) 1px solid;
	border-top:	hsl([h], 50%, 10%) 1px solid;
	padding: 1px;
}

td.nb{
	border: 0px none;
	padding: 1px;
}

td.h{
	border-left: 0;
	border-right: 0;
	padding: 1px;
}

tr.n1,td.n1{
	background: rgba(0, 0, 0, 0.30);
}

tr.n2,td.n2{
	background: rgba(0, 0, 0, 0.25);
}

tr.n3,td.n3{
	background: rgba(0, 0, 0, 0.20);
}

.sfont{
	font-size: 0.8em;
}

textarea,input,select,button{
	border:	hsl([h], 50%, 10%) ridge 2px;
	background: #000000;
	color: #FFF;
	font: 1em 'Segoe UI', Tahoma, Arial;
}

.radio{
	border: none;
	background: none;
}

input[type=\"submit\"], button {
	border: hsl([h], 50%, 20%) solid 2px;
	font-weight: bold;
	-o-transition: 0.2s linear;
	background-image: -o-linear-gradient(rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.50));
}
input[type=\"submit\"]:hover, button:hover {
	background-color: hsl([h], 50%, 20%);
}

.nc0x{color:#888888} .nc1x{color:#888888} .nc2x{color:#888888}
.nc00{color:#97ACEF} .nc10{color:#F185C9} .nc20{color:#7C60B0}
.nc01{color:#D8E8FE} .nc11{color:#FFB3F3} .nc21{color:#EEB9BA}
.nc02{color:#AFFABE} .nc12{color:#C762F2} .nc22{color:#47B53C}
.nc03{color:#FFEA95} .nc13{color:#C53A9E} .nc23{color:#F0C413}
";

$css = str_replace("[h]", $hue, $css);
header("Content-Type:text/css");
print $css;

?>
