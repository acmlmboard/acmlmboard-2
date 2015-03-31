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