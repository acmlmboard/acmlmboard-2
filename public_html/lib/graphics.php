<?PHP

require 'lib/TextRenderer.php';

/*
	Basic Acmlmboard2 graphics library.  Includes functions for image upload and vetting, ASCII 7-bit text rendering, etc.
*/

class Image {
	public $Image;
	public $Size;

	public function SavePNG($Filename) {
		if (file_exists($Filename))
			unlink($Filename);
		imagepng($this->Image, $Filename);
	}

	public static function LoadPNG($Filename) {
		$BasePNGImage = ImageCreateFromPNG($Filename);
		imagealphablending($BasePNGImage, true);
		imagesavealpha($BasePNGImage, true);

		$Size = getimagesize($Filename);
		$Image = Image::Create($Size[0], $Size[1]);
		imagecopy($Image->Image, $BasePNGImage, 0, 0, 0, 0, $Size[0], $Size[1]);
		imagedestroy($BasePNGImage);

		return $Image;
	}

	public function OutputPNG($StopProcessing = false) {
		ob_clean();
		header ('Content-Type: image/png');
		imagepng($this->Image);
		if ($StopProcessing) { 
			$this->Dispose();
			die();
		}
	}

	public function Dispose() {
		imagedestroy($this->Image);
	}

	public function DrawImageDirect($SrcImage, $DestX, $DestY) {
		imagecopy($this->Image, $SrcImage->Image, $DestX, $DestY, 0, 0, $SrcImage->Size[0],  $SrcImage->Size[1]);
	}

	public function DrawImageSection($SrcImage, $DestX, $DestY, $Width, $Height) {
		imagecopy($this->Image, $SrcImage->Image, $DestX, $DestY, 0, 0, $Width, $Height);
	}

	public function DrawImageSubSection($SrcImage, $DestX, $DestY, $SourceX, $SourceY, $Width, $Height) {
		imagecopy($this->Image, $SrcImage->Image, $DestX, $DestY, $SourceX, $SourceY, $Width, $Height);
	}

	public function Colourize($Red, $Blue, $Green, $Amt) {
		imagefilter($this->Image, IMG_FILTER_COLORIZE, $Red, $Blue, $Green, (255 - $Amt) >> 1);
	}

	public function ResizeCanvas($NewWidth, $NewHeight) {
		$OldSize = array($this->Size[0], $this->Size[1]);
		$this->Size = array($NewWidth, $NewHeight);
		$NewImage = Image::_CreateImageResource($NewWidth, $NewHeight);	

		imagecopy($NewImage, $this->Image, 0, 0, 0, 0, $OldSize[0], $OldSize[1]);

		$this->Dispose();
		$this->Image = $NewImage;
	}

	public function CreateBrush($Red, $Green, $Blue, $Alpha = 255) {
		return imagecolorallocatealpha($this->Image, $red, $green, $blue, (255-$alpha) >> 1);
	}

	public static function Create($Width, $Height) {	
		$ImageObject = new Image();
		$ImageObject->Image = Image::_CreateImageResource($Width, $Height);
		$ImageObject->Size = array($Width, $Height);
		
		return $ImageObject;
	}

	private static function _CreateImageResource($Width, $Height) {
		$Image = imagecreatetruecolor($Width, $Height);

		imagealphablending($Image, true);
		imagesavealpha($Image, true);
		imagefill($Image, 0, 0, imagecolorallocatealpha($Image, 0, 0, 0, 127));

		return $Image;
	}

	public function RendTag($Width, $Col) {
		$col=str_split($Col,2);
		$r=hexdec($col[0]);
		$g=hexdec($col[1]);
		$b=hexdec($col[2]);
		//$bg=imagecolorallocatealpha($this->Image, 255, 0, 0, 127);
		$black = imagecolorallocate($this->Image, 0, 0, 0);
		$main = imagecolorallocate($this->Image, $r, $g, $b);
		$ro=($r+28 <= 255) ? $r+28 : 255;
		$go=($g+28 <= 255) ? $g+28 : 255;
		$bo=($b+28 <= 255) ? $b+28 : 255;
		$light = imagecolorallocate($this->Image,$ro,$go,$bo);
		$ro=($r-23 >= 0) ? $r-23 : 0;
		$go=($g-23 >= 0) ? $g-23 : 0;
		$bo=($b-23 >= 0) ? $b-23 : 0;
		$dark = imagecolorallocate($this->Image,$ro,$go,$bo);
		$ro=($r-74 >= 0) ? $r-74 : 0;
		$go=($g-74 >= 0) ? $g-74 : 0;
		$bo=($b-74 >= 0) ? $b-74 : 0;
		$deep = imagecolorallocate($this->Image,$ro,$go,$bo);
		imagefilledrectangle ($this->Image, 5, 0, ($Width-2), 15, $black);
		imagefilledrectangle ($this->Image, ($Width-1), 1, ($Width-1), 14, $black);
		imagefilledrectangle ($this->Image, 4, 0, 4, 0, $black);
		imagefilledrectangle ($this->Image, 3, 1, 3, 1, $black);
		imagefilledrectangle ($this->Image, 2, 2, 2, 2, $black);
		imagefilledrectangle ($this->Image, 1, 3, 1, 3, $black);
		imagefilledrectangle ($this->Image, 0, 4, 0, 10, $black);
		imagefilledrectangle ($this->Image, 1, 11, 1, 11, $black);
		imagefilledrectangle ($this->Image, 2, 12, 2, 12, $black);
		imagefilledrectangle ($this->Image, 3, 13, 3, 13, $black);
		imagefilledrectangle ($this->Image, 4, 14, 4, 14, $black);
		imagefilledrectangle ($this->Image, 5, 1, ($Width-2), 14, $main);
		imagefilledrectangle ($this->Image, 4, 2, 6, 5, $main);
		imagefilledrectangle ($this->Image, 4, 10, 6, 13, $main);
		imagefilledrectangle ($this->Image, 2, 11, 3, 11, $main);
		imagefilledrectangle ($this->Image, 3, 12, 3, 12, $main);
		imagefilledrectangle ($this->Image, 5, 1, ($Width-3), 2, $light);
		imagefilledrectangle ($this->Image, 1, 4, 1, 5, $light);
		imagefilledrectangle ($this->Image, 2, 3, 2, 4, $light);
		imagefilledrectangle ($this->Image, 3, 2, 3, 3, $light);
		imagefilledrectangle ($this->Image, 4, 1, 4, 2, $light);
		imagefilledrectangle ($this->Image, 1, 9, 1, 9, $light);
		imagefilledrectangle ($this->Image, 1, 10, 4, 10, $light);
		imagefilledrectangle ($this->Image, 5, 9, 5, 9, $light);
		imagefilledrectangle ($this->Image, 6, 6, 6, 8, $light);
		imagefilledrectangle ($this->Image, 6, 13, ($Width-2), 14, $dark);
		imagefilledrectangle ($this->Image, ($Width-3), 3, ($Width-2), 14, $dark);
		imagefilledrectangle ($this->Image, ($Width-2), 1, ($Width-2), 2, $dark);
		imagefilledrectangle ($this->Image, 3, 4, 4, 4, $dark);
		imagefilledrectangle ($this->Image, 5, 5, 5, 5, $dark);
		imagefilledrectangle ($this->Image, 5, 14, 6, 14, $dark);
		imagefilledrectangle ($this->Image, 2, 5, 4, 5,$deep);
		imagefilledrectangle ($this->Image, 2, 9, 4, 9,$deep);
		imagefilledrectangle ($this->Image, 1, 6, 1, 8,$deep);
		imagefilledrectangle ($this->Image, 5, 6, 5, 8,$deep);
	}
}
function img_upload($fname,$img_targ,$img_x,$img_y,$img_size){
  $ftypes=array("png","jpeg","jpg","gif");
  $img_data=getimagesize($fname['tmp_name']);
  $err=0; $oerr="";
  if($img_data[0]>$img_x){ $oerr.="<br>Too wide."; $err=1; }
  if($img_data[1]>$img_y){ $oerr.="<br>Too tall."; $err=1; }
  if($fname['size']>$img_size){ $oerr.="<br>Filesize limit of $img_size bytes exceeded."; $err=1; }
  if(!in_array(str_replace("image/","",$img_data['mime']),$ftypes)){ $oerr="Invalid file type."; $err=1; }
  if($err){ return $oerr; }
  if(move_uploaded_file($fname['tmp_name'],$img_targ)){
    return "OK!";
  } else {
    return "<br>Error creating file.";
  }
}
?>