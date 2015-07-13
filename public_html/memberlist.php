<?php

require 'lib/common.php';
pageheader('Memberlist');

$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'posts';
$sex = isset($_REQUEST['sex']) ? $_REQUEST['sex'] : '';
$pow = isset($_REQUEST['pow']) ? $_REQUEST['pow'] : '';
$ppp = isset($_REQUEST['ppp']) ? (int)$_REQUEST['ppp'] : 50;
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
$mini = isset($_REQUEST['mini']) ? $_REQUEST['mini'] : '';
$orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '';
$customnc = isset($_REQUEST['customnc']) ? $_REQUEST['customnc'] : '';
$displayn = isset($_REQUEST['displayn']) ? $_REQUEST['displayn'] : '';


if ($ppp < 1)
	$ppp = 50;
if ($page < 1)
	$page = 1;

if ($orderby == 'a')
	$sortby = " ASC";
else
	$sortby = " DESC";

$order = 'posts' . $sortby;
if ($sort == 'exp')
	$order = 'exp' . $sortby;
if ($sort == 'name')
	$order = 'name' . $sortby;
if ($sort == 'reg')
	$order = 'regdate' . $sortby;

$where = '1';
if ($sex == 'm')
	$where = 'sex=0';
if ($sex == 'f')
	$where = 'sex=1';
if ($sex == 'n')
	$where = 'sex=2';

if ($pow != '' && is_numeric($pow)) {
	if ($pow == '-1')
		$where.=" AND `group_id` =  ANY (SELECT `x_id` FROM `x_perm` WHERE `x_id`= ANY (SELECT `id` FROM `group` WHERE `perm_id` = \"show-as-staff\") AND `x_type` =\"group\")";
	else
		$where.=" AND group_id=$pow";
}
if (!$config['perusercolor'])
	$customnc = '0';
if ($customnc == '1')
	$where.=" AND `nick_color` !='' AND `enablecolor` > 0";

if (!$config['displayname'])
	$displayn = '0';
if ($displayn == '1')
	$where.=" AND `displayname` !=''";

$users = $sql->query("SELECT *," . sqlexp() . " FROM users "
		. "WHERE $where "
		. "ORDER BY $order "
		. "LIMIT " . ($page - 1) * $ppp . ",$ppp");
$num = $sql->resultq("SELECT COUNT(*) FROM users "
		. "WHERE $where");

if ($num <= $ppp)
	$pagelist = '';
else {
	$pagelist = 'Pages:';
	for ($p = 1; $p <= 1 + floor(($num - 1) / $ppp); $p++)
		if ($p == $page)
			$pagelist.=" $p";
		else
			$pagelist.=' ' . mlink($sort, $sex, $pow, $ppp, $p, $mini, $orderby, $customnc, $displayn) . "$p</a>";
}

$activegroups = $sql->query("SELECT * FROM `group` WHERE id IN (SELECT `group_id` FROM users GROUP BY `group_id`) ORDER BY `sortorder` ASC ");

$groups = array();
$gc = 0;
$unclass = '';
if ($config['useshadownccss'])
	$unclass = "class='needsshadow'";
while ($group = $sql->fetch($activegroups)) {
	if ($config['memberlistcolorlinks']) {
		if ($sex == 'f')
			$sexcolor = $group['nc1'];
		elseif ($sex == 'n')
			$sexcolor = $group['nc2'];
		else
			$sexcolor = $group['nc0'];
		$grouptitle = "<span $unclass style=\"color:#" . $sexcolor . ";\">" . $group['title'] . "</span>";
	} else
		$grouptitle = $group['title'];
	$groups[$gc++] = mlink($sort, $sex, $group['id'], $ppp, $page, $mini, $orderby, $customnc, $displayn) . $grouptitle . "</a>";
}

//If colornames are enabled.. 
if ($config['memberlistcolorlinks']) {
	$malecolor = "<span $unclass style='color:#97ACEF;'>";
	$femalecolor = "<span $unclass style='color:#F185C9;'>";
	$nacolor = "<span $unclass style='color:#7C60B0;'>";
	$spancolor = "</span>";
} else {
	$malecolor = "";
	$femalecolor = "";
	$nacolor = "";
	$spancolor = "";
}

print "<table cellspacing=\"0\" class=\"c1\">
" . "  <tr class=\"h\">
" . "    <td class=\"b h\" colspan=2>$num user" . ($num > 1 ? 's' : '') . " found.</td>
" . "  <tr>
" . "    <td class=\"b n1\" width=60>Sort by:</td>
" . "    <td class=\"b n2\" align=\"center\">
" . "      " . mlink('', $sex, $pow, $ppp, $page, $mini, $orderby, $customnc, $displayn) . "Posts</a> |
" . "      " . mlink('exp', $sex, $pow, $ppp, $page, $mini, $orderby, $customnc, $displayn) . "EXP</a> |
" . "      " . mlink('name', $sex, $pow, $ppp, $page, $mini, $orderby, $customnc, $displayn) . "Username</a> |
" . "      " . mlink('reg', $sex, $pow, $ppp, $page, $mini, $orderby, $customnc, $displayn) . "Registration date</a>
" . "  <tr>
" . "    <td class=\"b n1\" width=60>Order by:</td>
" . "    <td class=\"b n2\" align=\"center\">
" . "      " . mlink($sort, $sex, $pow, $ppp, $page, $mini, 'd', $customnc, $displayn) . "Descending</a> |
" . "      " . mlink($sort, $sex, $pow, $ppp, $page, $mini, 'a', $customnc, $displayn) . "Ascending</a>
" . "  <tr>
" . "    <td class=\"b n1\">Sex:</td>
" . "    <td class=\"b n2\" align=\"center\">
" . "      " . mlink($sort, 'm', $pow, $ppp, $page, $mini, $orderby, $customnc, $displayn) . $malecolor . "Male" . $spancolor . "</a> |
" . "      " . mlink($sort, 'f', $pow, $ppp, $page, $mini, $orderby, $customnc, $displayn) . $femalecolor . "Female" . $spancolor . "</a> |
" . "      " . mlink($sort, 'n', $pow, $ppp, $page, $mini, $orderby, $customnc, $displayn) . $nacolor . "N/A" . $spancolor . "</a> |
";

if ($config['perusercolor']) {
	if ($customnc == '1')
		print "      " . mlink($sort, $sex, $pow, $ppp, $page, $mini, $orderby, '0', $displayn) . "Regular</a> |
  ";
	else
		print "      " . mlink($sort, $sex, $pow, $ppp, $page, $mini, $orderby, '1', $displayn) . "Custom</a> |
  ";
}

print "      " . mlink($sort, '', $pow, $ppp, $page, $mini, $orderby, $customnc, $displayn) . "All</a>";

//Added the sort by displayname feature - SquidEmpress
if ($config['displayname'])
	print "  <tr>
" . "    <td class=\"b n1\">Displayname:</td>
" . "    <td class=\"b n2\" align=\"center\">
" . "      " . mlink($sort, $sex, $pow, $ppp, $page, $mini, $orderby, $customnc, '0') . "Regular</a> |
" . "      " . mlink($sort, $sex, $pow, $ppp, $page, $mini, $orderby, $customnc, '1') . "Displayname</a>";

print "  <tr>
" . "    <td class=\"b n1\">Image:</td>
" . "    <td class=\"b n2\" align=\"center\">
" . "      " . mlink($sort, $sex, $pow, $ppp, $page, '0', $orderby, $customnc, $displayn) . "Avatars</a> |
" . "      " . mlink($sort, $sex, $pow, $ppp, $page, '1', $orderby, $customnc, $displayn) . "Minipics</a>";

print "  <tr>
" . "    <td class=\"b n1\">Group:</td>
" . "    <td class=\"b n2\" align=\"center\">";
$c = 0;
foreach ($groups as $k => $v) {
	echo $v;
	$c++;
	//if ($c < $gc) 
	echo " | ";
}
echo "      " . mlink($sort, $sex, '-1', $ppp, $page, $mini, $orderby, $customnc, $displayn) . "All Staff</a>
" . " |       " . mlink($sort, $sex, '', $ppp, $page, $mini, $orderby, $customnc, $displayn) . "All</a>
" . "      
" . "</table>
" . "<br>";

//Need to replace a few things if $mini=1 -Emuz
if ($mini == 1) {
	$piccap = "Minipic";
	$picwid = "16px";
} else {
	$piccap = "Picture";
	$picwid = "64px";
}

//[KAWA] Rebuilt this to use my new renderer. Not sure what to do about the part above though X3
$headers = array
	(
	"id" => array("caption" => "#", "width" => "32px", "align" => "center"),
	"pic" => array("caption" => $piccap, "width" => $picwid),
	"name" => array("caption" => "Name"),
	"reg" => array("caption" => "Registered on", "width" => "130px"),
	"posts" => array("caption" => "Posts", "width" => "50px"),
	"lvl" => array("caption" => "Level", "width" => "40px"),
	"exp" => array("caption" => "EXP", "width" => "80px"),
);
$data = array();
for ($i = ($page - 1) * $ppp + 1; $user = $sql->fetch($users); $i++) {
	$user['exp'] = floor($user['exp']);
	$user['level'] = calclvl($user['exp']);
	if ($mini == 1)
		$picture = ($user['minipic'] ? "<center><img style='vertical-align:text-bottom' src='" . $user[$u . 'minipic'] . "' border=0 ></center> " : '<img src=img/_.png width=16 height=16>');
	else
		$picture = ($user['usepic'] ? "<img src=gfx/userpic.php?id=$user[id] width=60 height=60>" : '<img src=img/_.png width=60 height=60>');

	$data[] = array(
		"id" => $user['id'] . '.',
		"pic" => $picture,
		"name" => userlink($user),
		"reg" => cdate($dateformat, $user['regdate']),
		"posts" => $user['posts'],
		"lvl" => $user['level'],
		"exp" => $user['exp'],
	);
}

RenderTable($data, $headers);

print "<br>
" . "$pagelist
" . "<br>
";
pagefooter();

function mlink($sort, $sex, $pow, $ppp, $page = 1, $mini, $orderby, $customnc, $displayn) {
	return '<a href=memberlist.php?'
			. ($sort ? "sort=$sort" : '')
			. ($sex ? "&sex=$sex" : '')
			. ($pow != '' ? "&pow=$pow" : '')
			. ($ppp != 50 ? "&ppp=$ppp" : '')
			. ($page != 1 ? "&page=$page" : '')
			. ($mini != 0 ? "&mini=$mini" : '')
			. ($orderby != '' ? "&orderby=$orderby" : '')
			. ($customnc != '' ? "&customnc=$customnc" : '')
			. ($displayn != '' ? "&displayn=$displayn" : '')
			. '>';
}

?>
