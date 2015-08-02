<?php
require 'gfxlib.php';

// cast everything to ints
$t = isset($_GET['t']) ? (int)$_GET['t'] : 0;
$u = isset($_GET['u']) ? (int)$_GET['u'] : 0;
$n = isset($_GET['n']) ? (int)$_GET['n'] : 50;
$s = isset($_GET['s']) ? (int)$_GET['s'] : 1;

$val = 'posts';
if ($t == 'lv')
	$val = 'pow(' . sqlexpval() . ',2/7)*100';
if ($t == 'ppd')
	$val = 'posts/(' . ctime() . '-regdate)*8640000';

if ($s < 0) {
	$u = -$s;
	$uval = $sql->resultq("SELECT $val val FROM users WHERE id=$u");
	$rank = $sql->resultq("SELECT count(*) FROM users WHERE $val>$uval AND id!=$u") + 1;
	$s = floor($rank - ($n - 1) / 2);
	if ($s < 1)
		$s = 1;
}

$users = $sql->query("SELECT id, name, $val val FROM users ORDER BY val DESC, (id='$u') DESC LIMIT " . ($s - 1) . ",$n");

header('Content-type:image/png');
$img = ImageCreate(512, ($n + 2) * 8);
$c['bg'] = ImageColorAllocate($img, 40, 40, 90);
$c['bxb0'] = ImageColorAllocate($img, 0, 0, 0);
$c['bxb1'] = ImageColorAllocate($img, 200, 170, 140);
$c['bxb2'] = ImageColorAllocate($img, 155, 130, 105);
$c['bxb3'] = ImageColorAllocate($img, 110, 90, 70);
for ($i = 0; $i < 100; $i++)
	$c[$i] = ImageColorAllocate($img, 10, 16, 60 + $i / 2);
$c['bar'][1] = ImageColorAllocate($img, 255, 189, 222);
$c['bar'][2] = ImageColorAllocate($img, 231, 0, 90);
$c['bar'][3] = ImageColorAllocate($img, 255, 115, 181);
$c['bar'][4] = ImageColorAllocate($img, 255, 115, 99);
$c['bar'][5] = ImageColorAllocate($img, 255, 156, 57);
$c['bar'][6] = ImageColorAllocate($img, 255, 231, 165);
$c['bar'][7] = ImageColorAllocate($img, 173, 231, 255);
$c['hlit'] = ImageColorAllocate($img, 47, 63, 191);
ImageColorTransparent($img, 0);

box(0, 0, 64, $n + 2);

$fontY = fontc(255, 250, 240, 255, 240, 80, 0, 0, 0);
$fontR = fontc(255, 230, 220, 240, 160, 150, 0, 0, 0);
$fontG = fontc(190, 255, 190, 60, 220, 60, 0, 0, 0);
$fontB = fontc(160, 240, 255, 120, 190, 240, 0, 0, 0);
$fontW = fontc(255, 255, 255, 210, 210, 210, 0, 0, 0);

$sc[1] = 1;
$sc[2] = 3;
$sc[3] = 10;
$sc[4] = 20;
$sc[5] = 40;
$sc[6] = 100;
$sc[7] = 200;
$sc[8] = 99999999;
$rval = '';
for ($i = $s; $user = $sql->fetch($users); $i++) {
	if ($user['val'] != $rval) {
		$rank = $i;
		$rval = $user['val'];
	}
	if ($i == $s) {
		$rank = $sql->resultq("SELECT count(*) FROM users WHERE $val>{$user['val']} AND id!={$user['id']}") + 1;
		for ($sn = 1; ($user['val'] / $sc[$sn]) > 320; $sn++)
			;
		$div = $sc[$sn];
		if (!$div)
			$div = 1;
	}
	$y = $i - $s + 1;
	if ($user['id'] == $u) {
		ImageFilledRectangle($img, 8, $y * 8, 503, $y * 8 + 7, $c['hlit']);
		$fontu = $fontY;
	} else
		$fontu = $fontB;
	twrite($fontW, 0, $y, 4, $rank);
	twrite($fontu, 5, $y, 0, substr($user['name'], 0, 12));
	twrite($fontY, 16, $y, 6, floor($user['val']));
	if (($sx = $user['val'] / $div) >= 1) {
		ImageFilledRectangle($img, 185, $y * 8 + 1, 184 + $sx, $y * 8 + 7, $c['bxb0']);
		ImageFilledRectangle($img, 184, $y * 8, 183 + $sx, $y * 8 + 6, $c['bar'][$sn]);
	}
}

ImagePNG($img);
ImageDestroy($img);

function twrite($font, $x, $y, $l, $text) {
	global $img;
	$x*=8;
	$y*=8;
	$text.='';
	if (strlen($text) < $l)
		$x+=($l - strlen($text)) * 8;
	for ($i = 0; $i < strlen($text); $i++)
		ImageCopy($img, $font, $i * 8 + $x, $y, (ord($text[$i]) % 16) * 8, floor(ord($text[$i]) / 16) * 8, 8, 8);
}

function fontc($r1, $g1, $b1, $r2, $g2, $b2, $r3, $g3, $b3) {
	$font = ImageCreateFromPNG('font.png');
	ImageColorTransparent($font, 1);
	ImageColorSet($font, 6, $r1, $g1, $b1);
	ImageColorSet($font, 5, ($r1 * 2 + $r2) / 3, ($g1 * 2 + $g2) / 3, ($b1 * 2 + $b2) / 3);
	ImageColorSet($font, 4, ($r1 + $r2 * 2) / 3, ($g1 + $g2 * 2) / 3, ($b1 + $b2 * 2) / 3);
	ImageColorSet($font, 3, $r2, $g2, $b2);
	ImageColorSet($font, 0, $r3, $g3, $b3);
	return $font;
}

function box($x, $y, $w, $h) {
	global $img, $c;
	$x*=8;
	$y*=8;
	$w*=8;
	$h*=8;
	ImageRectangle($img, $x + 0, $y + 0, $x + $w - 1, $y + $h - 1, $c['bxb0']);
	ImageRectangle($img, $x + 1, $y + 1, $x + $w - 2, $y + $h - 2, $c['bxb3']);
	ImageRectangle($img, $x + 2, $y + 2, $x + $w - 3, $y + $h - 3, $c['bxb1']);
	ImageRectangle($img, $x + 3, $y + 3, $x + $w - 4, $y + $h - 4, $c['bxb2']);
	ImageRectangle($img, $x + 4, $y + 4, $x + $w - 5, $y + $h - 5, $c['bxb0']);
	for ($i = 5; $i < $h - 5; $i++) {
		$n = (1 - $i / $h) * 100;
		ImageLine($img, $x + 5, $y + $i, $x + $w - 6, $y + $i, $c[$n]);
	}
}

?>