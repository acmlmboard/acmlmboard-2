<?php

// Text Renderer.

function RenderText($Text, $WidthPixels = 1048576) {
	$CharacterSheets = array(Image::LoadPNG("./gfx/fonts/defaultf.png"), Image::LoadPNG("./gfx/fonts/tempredf.png"), 
							 Image::LoadPNG("./gfx/fonts/large.png"));

	$DippedCharacters = array(44, 95, 103, 106, 112, 113); //ASCII values for the characters that 'dip' down slightly.
	$TwoCharCharacters = array("\x92\x93", "\x94\x95", "\x9B\x9C"); //ASCII character pairs for the L/R/Start N64 buttons
	$PreWrapCharacters = array("\x92", "\x94", "\x9B"); //ASCII characters which wrap if another character cannot fit on the same line after them
	$SheetCodes = array("\x01", "\x02", "\x03");

	$Text = str_replace($TwoCharCharacters, $PreWrapCharacters, $Text);
	$Text = str_replace($PreWrapCharacters, $TwoCharCharacters, $Text);

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

	for ($WordIndex = 0; $WordIndex < count($Words); $WordIndex++) {
		$Word = $Words[$WordIndex];

		if (($DrawIndex % $CharsPerLine) + strlen($Word) > $CharsPerLine) {
			$DrawIndex = ceil($DrawIndex / $CharsPerLine) * $CharsPerLine;
		}

		for ($StringIndex = 0; $StringIndex < strlen($Word); $StringIndex++) {
			if (($DrawIndex + 1) % $CharsPerLine == 0 && in_array($Word{$StringIndex}, $PreWrapCharacters))
				$DrawIndex++;
	
			if (in_array($Word{$StringIndex}, $SheetCodes)) {
				$CurrentSheet = ord($Word{$StringIndex}) - ord($SheetCodes[0]);
				continue;
			}
	
			if (ord($Word{$StringIndex}) == 10) {
				$DrawIndex = ceil($DrawIndex / $CharsPerLine) * $CharsPerLine;
				continue;
			}
	
			$DrawX = ($DrawIndex % $CharsPerLine) * $SheetSize[0];
			$DrawY = floor($DrawIndex / $CharsPerLine) * $SheetSize[1];
	
			if (in_array(ord($Word{$StringIndex}), $DippedCharacters)) {
				$DrawY += 2;
			}
	
			if ($DrawY + $SheetSize[1] + 2 >= $ImageHeight) {
				$ImageHeight = $DrawY + $SheetSize[1] + 2;
				$Image->ResizeCanvas($ImageWidth, $ImageHeight);
			}
	
			CharacterCodeTo($Image, $CharacterSheets[$CurrentSheet], ord($Word{$StringIndex}), $DrawX, $DrawY);
	
			$DrawIndex++;
		}
		if (($DrawIndex % $CharsPerLine) != 0)
			$DrawIndex++;
	}

	if ((ceil($DrawIndex / $CharsPerLine) * $SheetSize[1]) + 2 < $ImageHeight) {
		$ImageHeight = (ceil($DrawIndex / $CharsPerLine) * $SheetSize[1]) + 2;
		$Image->ResizeCanvas($ImageWidth, $ImageHeight);
	}
	return $Image;
}

function CharacterCodeTo($TargetImage, $Sheet, $ASCII, $DestX, $DestY) {
	$SheetSize = $Sheet->Size;

	$UseSize = array($SheetSize[0] / 16, $SheetSize[1] / 16);

	$SourceX = ($ASCII % 16) * ($UseSize[0]);
	$SourceY = floor($ASCII / 16) * ($UseSize[1]);

	imagecopy($TargetImage->Image, $Sheet->Image, $DestX, $DestY, $SourceX, $SourceY, $UseSize[0], $UseSize[1]);
}

?>

