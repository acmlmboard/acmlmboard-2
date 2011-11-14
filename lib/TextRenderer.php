<?php

// Text Renderer.

function RenderText($Text, $WidthPixels = 1048576) {
	$CharacterSheets = array(Image::LoadPNG("./gfx/fonts/defaultf.png"), Image::LoadPNG("./gfx/fonts/tempredf.png"), 
							 Image::LoadPNG("./gfx/fonts/large.png"));

	$CharacterWidths = array(loadvwfdata("./gfx/fonts/defaultfw.txt"), loadvwfdata("./gfx/fonts/defaultfw.txt"), loadvwfdata("./gfx/fonts/defaultfw.txt"));

	$DippedCharacters = array(44, 95, 103, 106, 112, 113); //ASCII values for the characters that 'dip' down slightly.
	$SheetCodes = array("\x01", "\x02", "\x03");

	$Sheet = $CharacterSheets[0];

	for ($StringIndex = 0; $StringIndex < strlen($Text); $StringIndex++) {
		if (in_array($Text{$StringIndex}, $SheetCodes)) {
			$Sheet = $CharacterSheets[ord($Text{$StringIndex}) - ord($SheetCodes[0])];
			break;
		}
	}

	$SheetSize = array($Sheet->Size[0], $Sheet->Size[1]);

	$SheetSize[0] /= 16;
	$SheetSize[1] /= 16;

	$CharsPerLine = floor($WidthPixels / $SheetSize[0]);
	$AllocatedLines = ceil(strlen($Text) / $CharsPerLine);

	$ImageWidth = min($WidthPixels, strlen($Text) * $SheetSize[0]);
	$ImageHeight = $AllocatedLines * $SheetSize[1];

	$DrawIndex = 0;

	$Image = Image::Create($ImageWidth, $ImageHeight);

	$CurrentSheet = 0;

	$Words = explode(" ", $Text);

	$DrawY = $DrawX = 0;

	for ($WordIndex = 0; $WordIndex < count($Words); $WordIndex++) {
		$Word = $Words[$WordIndex];

		$WordWidth = 0;

		for ($StringIndex = 0; $StringIndex < strlen($Word); $StringIndex++) {
			$WordWidth += $CharacterWidths[$CurrentSheet][ord($Word{$StringIndex})];
		}

		if ($DrawX + $WordWidth >= $WidthPixels) {
			$DrawY += $SheetSize[1];
			$DrawX = 0;
		}

		if (($DrawIndex % $CharsPerLine) + strlen($Word) > $CharsPerLine) {
			$DrawIndex = ceil($DrawIndex / $CharsPerLine) * $CharsPerLine;
		}

		for ($StringIndex = 0; $StringIndex < strlen($Word); $StringIndex++) {
	
			if (in_array($Word{$StringIndex}, $SheetCodes)) {
				$CurrentSheet = ord($Word{$StringIndex}) - ord($SheetCodes[0]);
				continue;
			}
	
			if (ord($Word{$StringIndex}) == 10 || $DrawX >= $WidthPixels) {
				$DrawX = 0;
				$DrawY += $SheetSize[1];
				continue;
			}	

			$UseDrawY = $DrawY;

			if (in_array(ord($Word{$StringIndex}), $DippedCharacters)) {
				$UseDrawY += 2;
			}
	
			if ($UseDrawY + $SheetSize[1] + 2 >= $ImageHeight) {
				$ImageHeight = $UseDrawY + $SheetSize[1] + 2;
				$Image->ResizeCanvas($ImageWidth, $ImageHeight);
			}
	
			CharacterCodeTo($Image, $CharacterSheets[$CurrentSheet], ord($Word{$StringIndex}), $DrawX, $UseDrawY, $CharacterWidths[$CurrentSheet][ord($Word{$StringIndex})]);
			
			$DrawX += $CharacterWidths[$CurrentSheet][ord($Word{$StringIndex})];

			$DrawIndex++;
		}
		if (($DrawIndex % $CharsPerLine) != 0)
			$DrawIndex++;

		if ($DrawX > 0) $DrawX += $CharacterWidths[$CurrentSheet][0x20]; //Add Space
	}

	if ((ceil($DrawIndex / $CharsPerLine) * $SheetSize[1]) + 2 < $ImageHeight) {
		$ImageHeight = (ceil($DrawIndex / $CharsPerLine) * $SheetSize[1]) + 2;
		$Image->ResizeCanvas($ImageWidth, $ImageHeight);
	}
	return $Image;
}

function CharacterCodeTo($TargetImage, $Sheet, $ASCII, $DestX, $DestY, $CharWidth) {
	$SheetSize = $Sheet->Size;

	$UseSize = array($SheetSize[0] / 16, $SheetSize[1] / 16);

	$SourceX = ($ASCII % 16) * ($UseSize[0]);
	$SourceY = floor($ASCII / 16) * ($UseSize[1]);

	imagecopy($TargetImage->Image, $Sheet->Image, $DestX, $DestY, $SourceX, $SourceY, $CharWidth, $UseSize[1]);
}

function loadvwfdata($Filename) {
	$Data = array();
	$Str = file_get_contents($Filename);
	for ($x = 0; $x < strlen($Str); $x++)
		$Data[$x] = ord($Str{$x}) - ord("A");
	return $Data;
}

?>

