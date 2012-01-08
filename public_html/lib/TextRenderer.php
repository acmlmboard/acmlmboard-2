<?php

// Text Renderer.

/*
SPECIAL CHARACTER LIST:
==============================================
\x01 - \x03	: Character Sheet selector
\x18 [X]	: Set line spacing to [X] pixels  - byte

\x0A		: Line Break & Carriage Return
\x0D		: Carriage Return

\x0B		: Shift Full Character Right
\x0C		: Shift Full Character Left
\x0E		: Shift 1 Pixel Right
\x0F		: Shift 1 Pixel Left
\x10		: Shift 1 Pixel Down
\x11		: Shift 1 Pixel Up
\x12		: Shift 1 Line Down (Pixel amount determined by current character set and line-spacing setting)
\x13		: Shift 1 Line Up   (Pixel amount determined by current character set and line-spacing setting)

\x14		: Store current cursor position
\x15		: Recall current cursor position (defaults to 0,0)

\x16		: Set cursor to 0,0
\x17 [X] [Y]: Set cursor to X,Y in pixels     - 16-bit words
\x19 [X] [Y]: Set cursor to X,Y in characters - 16-bit words
*/

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

	$ImageWidth = min($WidthPixels, 512);
	$ImageHeight = 1;
	$Image = Image::Create($ImageWidth, $ImageHeight);

	$Words = array();
	$WordIndex = 0;

	for($StringIndex = 0; $StringIndex < strlen($Text); $StringIndex++) {
		switch(ord($Text{$StringIndex})) {
			case 0x20:
				$WordIndex++;
				continue;
			case 0x17:
			case 0x19:
				$Words[$WordIndex].=$Text{$StringIndex};
				$Words[$WordIndex].=$Text{$StringIndex + 1};
				$Words[$WordIndex].=$Text{$StringIndex + 2};
				$Words[$WordIndex].=$Text{$StringIndex + 3};
				$Words[$WordIndex].=$Text{$StringIndex + 4};

				$StringIndex += 4;
				continue;
			case 0x18:
				$Words[$WordIndex].=$Text{$StringIndex};
				$Words[$WordIndex].=$Text{$StringIndex + 1};
				$StringIndex ++;
				continue;
			default:
				$Words[$WordIndex].=$Text{$StringIndex};
		}
	}

	$CurrentSheet = 0;
	$LineSpacing = 1;
	
	$DrawnHeight = 0;
	$DrawnWidth = 0;
	$DrawHeightAdd = 0;

	$DrawY = 0;
	$DrawX = 0;
	$StoredX = 0;
	$StoredY = 0;

	for ($WordIndex = 0; $WordIndex < count($Words); $WordIndex++) {
		$Word = $Words[$WordIndex];

		$WordWidth = 0;
		for ($StringIndex = 0; $StringIndex < strlen($Word); $StringIndex++) {
			$WordWidth += $CharacterWidths[$CurrentSheet][ord($Word{$StringIndex})];
		}

		if ($DrawX + $WordWidth >= $WidthPixels && $DrawX > 0) {
			$DrawY += $DrawnHeight + $LineSpacing;
			$DrawnHeight = 0;
			$DrawHeightAdd = 0;
			$DrawX = 0;
		}

		for ($StringIndex = 0; $StringIndex < strlen($Word); $StringIndex++) {	
			$CharacterCode = ord($Word{$StringIndex});

			if (in_array($Word{$StringIndex}, $SheetCodes)) {
				$CurrentSheet = $CharacterCode - ord($SheetCodes[0]);
				continue;
			}	

			if ($CharacterCode == 0x0A || $DrawX >= $WidthPixels) { //Special Character and extra-long-word support
				$DrawX = 0;
				$DrawY = $DrawnHeight + $LineSpacing;
				$DrawnHeight = 0;
				$DrawHeightAdd = 0;
				continue;
			}

			switch($CharacterCode) { //Special Character support
				case 0x0B:
					$DrawX += $CharacterSheets[$CurrentSheet]->Size[0] / 16;
					continue;
				case 0x0C:
					$DrawX -= $CharacterSheets[$CurrentSheet]->Size[0] / 16;
					continue;
				case 0x0D:
					$DrawX = 0;
					continue;
				case 0x0E:
					$DrawX++;
					continue;
				case 0x0F:
					$DrawX--;
					continue;
				case 0x10:
					$DrawY++;
					continue;
				case 0x11:
					$DrawY--;
					continue;
				case 0x12:
					$DrawY += ($LineSpacing + $CharacterSheets[$CurrentSheet]->Size[1] / 16);
					continue;
				case 0x13:
					$DrawY -= ($LineSpacing + $CharacterSheets[$CurrentSheet]->Size[1] / 16);
					continue;
				case 0x14:
					$StoredX = $DrawX;
					$StoredY = $DrawY;
					continue;
				case 0x15:
					$DrawX = $StoredX;
					$DrawY = $StoredY;
					continue;
				case 0x16:
					$DrawX = 0;
					$DrawY = 0;
					continue;
				case 0x17:
					$DrawX = (ord($Word{$StringIndex + 1}) << 8) + ord($Word{$StringIndex + 2});
					$DrawY = (ord($Word{$StringIndex + 3}) << 8) + ord($Word{$StringIndex + 4});
					$StringIndex += 4;
					continue;
				case 0x18:
					$LineSpacing = ord($Word{$StringIndex + 1});
					$StringIndex++;
					continue;
				case 0x19:
					$DrawX = (ord($Word{$StringIndex + 1}) << 8) + ord($Word{$StringIndex + 2}) * ($CharacterSheets[$CurrentSheet]->Size[0] / 16);
					$DrawY = (ord($Word{$StringIndex + 3}) << 8) + ord($Word{$StringIndex + 4}) * ($CharacterSheets[$CurrentSheet]->Size[1] / 16);
					$StringIndex += 4;
					continue;
				default:
			}		

			$UseDrawY = $DrawY;

			if (in_array($CharacterCode, $DippedCharacters)) {
				$UseDrawY += 2;
				$DrawHeightAdd = 2;
			}

			$DrawnHeight = max($DrawnHeight, $DrawY + $DrawHeightAdd + $CharacterSheets[$CurrentSheet]->Size[1] / 16);
			$DrawnWidth = max($DrawnWidth, $DrawX + $CharacterWidths[$CurrentSheet][$CharacterCode]);

			if ($DrawnHeight > $ImageHeight || $DrawnWidth > $ImageWidth) {
				$ImageHeight = max($ImageHeight, $DrawnHeight);
				$ImageWidth = max($ImageWidth, $DrawnWidth);
				$Image->ResizeCanvas($ImageWidth, $ImageHeight);
			}
	
			CharacterCodeTo($Image, $CharacterSheets[$CurrentSheet], $CharacterCode, $DrawX, $UseDrawY, $CharacterWidths[$CurrentSheet][$CharacterCode]);
			$DrawX += $CharacterWidths[$CurrentSheet][$CharacterCode];
		}
		if ($DrawX > 0)
			$DrawX += $CharacterWidths[$CurrentSheet][0x20]; //Add Spaces as necessary
	}
	$Image->ResizeCanvas($DrawnWidth, $DrawnHeight);

	foreach($CharacterSheets as $Sheet)
		$Sheet->Dispose();

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