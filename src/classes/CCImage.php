<?php namespace Core;
/**
 * CCF Image manipulation with less pain
 * I know the class is still pretty much a mess, i going to change this soon.
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCImage {

	/**
	 * creates an new empty image
	 */
	public static function create( $width, $height, $type = 'jpg' ) 
	{
		return new static( imagecreatetruecolor( $width, $height ), $type );
	}

	/**
	 * Create a new CCImage from upload
	 *
	 * @param string 		$key
	 * @param string 		$type
	 * @return CCImage|null
	 */
	public static function upload( $key, $type = null )
	{
		static::load( CCFile::upload_path( $key ), $type );
	}

	/**
	 * create a image from file
	 *
	 * @param string 	$type | if the type is null the file extention gets used!
	 * @return CCImage
	 */
	public static function load( $file, $type = null ) 
	{
		if ( is_null( $type ) ) {
			$type = CCStr::extension( $file );
		}

		$image_data = getimagesize( $file );

		if( $image_data === false ) {
			return false;
		}

		$image = null;

		switch( $image_data['mime'] ) {
			case 'image/gif':
				$image = imagecreatefromgif( $file );
			break;
			case 'image/jpeg';
				$image = imagecreatefromjpeg( $file );
			break;
			case 'image/png':
				$image = imagecreatefrompng( $file );
			break;
			case 'image/x-icon':
			case 'image/bmp':
			case 'image/vnd.microsoft.icon':
				$image = imagecreatefrombmp( $file );
			break;
			default:
				// we dont support other image types
				return false;
			break;
		}

		return new static( $image, $type );
	}

	/**
	 * create an image from string
	 */
	public static function from_string( $string, $type = null ) {

		$image = imagecreatefromstring( $string );

		if ( $image !== false ) {
			return new static( $image, $type );
		}

		return false;
	}

	/**
	 * calculate the aspect ratio
	 *
	 * @param int	$width
	 * @param int 	$height
	 * @param bool	$proper
	 *
	 * thanks to: http://jonisalonen.com/2012/converting-decimal-numbers-to-ratios/
	 */
	public static function aspect_ratio( $width, $height, $proper = false ) {

		$ratio = $width / $height;

		if ( !$proper ) {
			return $ratio;
		}

		$tolerance = 1.e-6;
		$h1=1; $h2=0;
		$k1=0; $k2=1;
		$b = 1/$ratio;

		do {
			$b = 1/$b;
			$a = floor($b);
			$aux = $h1; $h1 = $a*$h1+$h2; $h2 = $aux;
			$aux = $k1; $k1 = $a*$k1+$k2; $k2 = $aux;
			$b = $b-$a;

		} while ( abs( $ratio-$h1 / $k1 ) > $ratio * $tolerance );

		return $h1.":".$k1;
	}

	/*
	 * image type default is png
	 */
	public $type = 'jpg';

	/*
	 * dimensions
	 */
	public $height	= 0;
	public $width	= 0;

	/*
	 * the actual image
	 */
	protected $image_context = null;

	/**
	 * CCImage constructor
	 */
	public function __construct( $image_context, $type = null ) {

		if ( is_resource( $image_context ) !== true ) {
			throw new CCException( "CCImage - Invalid image context given." );
		}

		$this->image_context = $image_context;

		// get dimensions
		$this->width  = imagesx( $this->image_context );  
		$this->height = imagesy( $this->image_context ); 

		// type
		if ( !is_null( $type ) ) {
			$this->type = $type;
		}
	}

	/**
	 * save our image to a file
	 *
	 * @param string		$file
	 * @param int		$quality between 1-100
	 * @param string		$type
	 */
	public function save( $file, $quality = null, $type = null ) {

		if ( !is_null( $type ) ) {
			$this->type = $type;
		}

		switch( $this->type ) {
			case 'png':
				if ( is_null( $quality ) ) {
					$quality = -1;
				}
				else {
					$quality = ( $quality / 100 ) * 9;
				}
				imagepng( $this->image_context, $file, $quality );
			break;

			case 'jpeg':
			case 'jpg':
				if ( is_null( $quality ) ) {
					$quality = 90;
				}
				imagejpeg( $this->image_context, $file, $quality );
			break;

			case 'gif':
				imagegif( $this->image_context, $file );
			break;
		}
	}

	/**
	 * output our image
	 *
	 * @param string		$type
	 * @param int		$quality
	 */
	public function stream( $quality = null, $type = null ) {
		return $this->save( null, $quality, $type );
	}

	/**
	 * return the image data
	 *
	 * @param string	$type
	 * @param int		$quality
	 * @return string
	 */
	public function stringify( $quality = null, $type = null ) {
		ob_start();
		$this->stream( $quality, $type );
		return ob_get_clean();
	}

	/**
	 * get the image as CCResponse
	 *
	 * @param string 	$quality / The image quality
	 * @return CCresponse
	 */
	public function response( $quality = null, $type = null ) {
		return CCResponse::create( $this->stingify( $quality, $type ) );
	}

	/**
	 * resize the current image
	 *
	 * alternative syntax $image->resize( 100x200, 'fit' )
	 * 
	 * @param int 		$width
	 * @param int		$height
	 * @param string		$mode
	 */
	public function resize( $width, $height, $mode = null ) {

		// check for alternative syntax 
		if ( strpos( $width, 'x' ) !== false ) {
			// mode is the secound param
			$mode = $height;

			$dimensions = explode( 'x', $width );
			$width = $dimensions[0];
			$height = $dimensions[1];
		} 

		// default mode
		if ( is_null( $mode ) ) {
			$mode = 'strict';
		}

		// auto width
		if ( $width == 'auto' ) {
			$mode = 'portrait';
			// in this case the $height is the first param
			$width = $height;
		} 
		// auto height
		elseif ( $height == 'auto' ) {
			$mode = 'landscape';
		} 

		$method = 'resize_'.$mode;

		if ( !method_exists( $this, $method ) ) {
			throw new CCException( "CCImage::resize - Invalid resize method ".$mode."." );
		}

		return call_user_func_array( array( $this, $method ), array( $width, $height ) );
	}

	/**
	 * resize the current image from width and keep aspect ratio
	 * 
	 * @param int 		$width
	 */
	public function resize_landscape( $width, $ignore_me ) {

		// calculate height
		$height = $width * ( $this->height / $this->width );

		return $this->resize_strict( $width, $height );
	}

	/**
	 * resize the current image from height and keep aspect ratio
	 * 
	 * @param int 		$height
	 */
	public function resize_portrait( $height, $ignore_me ) {

		// calculate width
		$width = $height * ( $this->width / $this->height );

		return $this->resize_strict( $width, $height );
	}

	/**
	 * resize the image that it fits into a size doesn't crop
	 * 
	 * @param int 		$height
	 */
	public function resize_max( $width, $height ) {

		$new_width = $this->width;
		$new_height = $this->height;

		if ( $new_width > $width ) {
			// set new with
			$new_width = $width;
			// calculate height
			$new_height = $new_width * ( $this->height / $this->width );
		}

		if ( $new_height > $height ) {
			// set new height
			$new_height = $height;
			// calculate width
			$new_width = $new_height * ( $this->width / $this->height );
		}

		return $this->resize_strict( $new_width, $new_height );
	}

	/**
	 * resize the image that it fits into a size doesn't crop
	 * 
	 * @param int 		$height
	 */
	public function resize_fit( $width, $height ) {

		$background = static::create( $width, $height );

		// make out actual image max size
		static::resize_max( $width, $height );

		// make background white
		$background->fill_color( '#fff' );

		// add the layer
		$background->add_layer( $this, 'center' );

		// overwrite the image context 
		$this->image_context = $background->image_context;

		// update properties
		$this->width = $width; 
		$this->height = $height;

		// return self
		return $this;
	}


	/**
	 * resize the image that it fits into a size doesn't crop
	 * 
	 * @param int 		$height
	 */
	public function resize_fill( $width, $height ) {

		$new_width = $this->width;
		$new_height = $this->height;

		if ( $new_width > $width ) {
			// set new with
			$new_width = $width;
			// calculate height
			$new_height = $new_width * ( $this->height / $this->width );
		}

		if ( $new_height > $height ) {
			// set new height
			$new_height = $height;
			// calculate width
			$new_width = $new_height * ( $this->width / $this->height );
		}

		return $this->resize_strict( $new_width, $new_height );
	}

	/**
	 * resize the current image
	 * strict means the ratio get broken
	 * 
	 * @param int 		$width
	 * @param int		$height
	 * @param string		$mode
	 */
	public function resize_strict( $width, $height ) {

		// check dimensions
		if ( !( $width > 0 ) || !( $height > 0 ) ) {
			throw new CCException( "CCImage::resize_strict - width and height can't be smaller then 1" );
		}

		$result = imagecreatetruecolor( $width, $height );  
		imagecopyresampled( 
			$result, 
			$this->image_context, 
			0, 0, 0, 0, 
			$width, 
			$height, 
			$this->width, 
			$this->height
		); 

		// overwrite the image context 
		$this->image_context = $result;

		// update properties
		$this->width = $width; 
		$this->height = $height;

		// return self
		return $this;
	}

	/**
	 * add an layer to the current image
	 *
	 * @param CCImage 		$image
	 * @param int			$x
	 * @param int			$y
	 */
	public function add_layer( CCImage $image, $x = 0, $y = 0 ) {

		// alternative syntax
		if ( is_string( $x ) && is_numeric( $x ) === false ) {

			if ( $x == 'center' ) {
				$x = ( $this->width / 2 ) - ( $image->width / 2 );
				$y = ( $this->height / 2 ) - ( $image->height / 2 );
			}
		}

		// run image copy
		imagecopy( 
			$this->image_context, 
			$image->image_context, 
			$x,//x 
			$y,//y
			0, 
			0, 
			$image->width, 
			$image->height
		);
	}

	/**
	 * fill the current image with an color
	 * you can pass an array with rgb or hex string
	 *
	 * @param mixed 		$color
	 */
	public function fill_color( $color ) {

		// parse the color
		$color = CCColor::parse( $color );
		$color = imagecolorallocate( $this->image_context, $color->RGB[0], $color->RGB[1], $color->RGB[2] );

		// run image fill
		imagefill( $this->image_context, 0, 0, $color );
	}

	/**
	 * Blur our image
	 *
	 * @param int 	$ratio
	 */
	public function blur( $ratio ) {
		for ($x=0; $x<$ratio; $x++) {
			imagefilter($this->image_context, IMG_FILTER_GAUSSIAN_BLUR);
			//$gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 1.0, 2.0), array(1.0, 2.0, 1.0));
			//imageconvolution($this->image_context, $gaussian, 16, 0);
		}
	}

	/**
	 * crop the image 
	 * 
	 * @param $crop
	 * @param $size
	 */
	public function resize2( $size = null, $crop = null ) {

		$x = 0;
		$y = 0;
		$width = imagesx( $this->image_context );
		$height = imagesy( $this->image_context );

		// size param
		if ( is_null( $size ) ) {
			$size = array( $width, $height );
		}
		else {
			$size = explode( 'x', $size );
		}

		// crop param
		if ( is_null( $crop ) ) {
			$crop = array( $width, $height );
		}
		elseif ( $crop == 'auto' ) {
			$crop = $size;
		}
		else {
			$crop = explode( ':', $crop );
		}


		/*
		 * CROP (Aspect Ratio) Section
		 */
		if ((empty($crop[0]) === true) || (is_numeric($crop[0]) === false)) {
			$crop[0] = $crop[1];
		} 
		elseif ((empty($crop[1]) === true) || (is_numeric($crop[1]) === false)) {
			$crop[1] = $crop[0];
		}

		$ratio = array(0 => $width / $height, 1 => $crop[0] / $crop[1]);

		if ($ratio[0] > $ratio[1]) {
			$width = $height * $ratio[1];
			$x = (imagesx($this->image_context) - $width) / 2;
		}
		elseif ($ratio[0] < $ratio[1]) {
			$height = $width / $ratio[1];
			$y = (imagesy($this->image_context) - $height) / 2;
		}

		/*
		 * Resize Section
		 */
		if ((empty($size[0]) === true) || (is_numeric($size[0]) === false)) {
			$size[0] = round($size[1] * $width / $height);
		} else if ((empty($size[1]) === true) || (is_numeric($size[1]) === false)) {
			$size[1] = round($size[0] * $height / $width);
		}

		$result = ImageCreateTrueColor($size[0], $size[1]);

		if ( is_resource( $result ) ) {

			ImageSaveAlpha( $result, true );
			ImageAlphaBlending( $result, true );
			ImageFill( $result, 0, 0, ImageColorAllocate( $result, 255, 255, 255 ) );
			ImageCopyResampled( $result, $this->image_context, 0, 0, $x, $y, $size[0], $size[1], $width, $height );

			ImageInterlace( $result, true );

			// set the image 
			$this->image_context = $result;

			$this->width = $size[0];
			$this->height = $size[1];

			// return self
			return $this;
		}
		else {
			throw new CCException( "CCImage - Faild at croping the image." );
		}
	}


	/**
	 * get the average lumincance from the picture 
	 * 
	 * @param int 	$num_samples
	 */
	public function luminance( $num_samples = 10 ) {

		$img = $this->image_context;

		$width = imagesx($img);
		$height = imagesy($img);

		$x_step = intval($width/$num_samples);
		$y_step = intval($height/$num_samples);

		$total_lum = 0;
		$sample_no = 1;

		for ($x=0; $x<$width; $x+=$x_step) {
			for ($y=0; $y<$height; $y+=$y_step) {

				$rgb = imagecolorat($img, $x, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;

				// choose a simple luminance formula from here
				// http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
				$lum = ($r+$r+$b+$g+$g+$g)/6;

				$total_lum += $lum;
				$sample_no++;
			}
		}
		$avg_lum  = $total_lum/$sample_no;
		return (int) $avg_lum;
	}
}

/*********************************************/
/* Fonction: ImageCreateFromBMP              */
/* Author:   DHKold                          */
/* Contact:  admin@dhkold.com                */
/* Date:     The 15th of June 2005           */
/* Version:  2.0B                            */
/*********************************************/
function imagecreatefrombmp($filename)
{
 //Ouverture du fichier en mode binaire
   if (! $f1 = fopen($filename,"rb")) return FALSE;

 //1 : Chargement des ent�tes FICHIER
   $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
   if ($FILE['file_type'] != 19778) return FALSE;

 //2 : Chargement des ent�tes BMP
   $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
				 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
				 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
   $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
   if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
   $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
   $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
   $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
   $BMP['decal'] = 4-(4*$BMP['decal']);
   if ($BMP['decal'] == 4) $BMP['decal'] = 0;

 //3 : Chargement des couleurs de la palette
   $PALETTE = array();
   if ($BMP['colors'] < 16777216)
   {
	$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
   }

 //4 : Cr�ation de l'image
   $IMG = fread($f1,$BMP['size_bitmap']);
   $VIDE = chr(0);

   $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
   $P = 0;
   $Y = $BMP['height']-1;
   while ($Y >= 0)
   {
	$X=0;
	while ($X < $BMP['width'])
	{
	 if ($BMP['bits_per_pixel'] == 24)
		$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
	 elseif ($BMP['bits_per_pixel'] == 16)
	 { 
		$COLOR = unpack("n",substr($IMG,$P,2));
		$COLOR[1] = $PALETTE[$COLOR[1]+1];
	 }
	 elseif ($BMP['bits_per_pixel'] == 8)
	 { 
		$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
		$COLOR[1] = $PALETTE[$COLOR[1]+1];
	 }
	 elseif ($BMP['bits_per_pixel'] == 4)
	 {
		$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
		if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
		$COLOR[1] = $PALETTE[$COLOR[1]+1];
	 }
	 elseif ($BMP['bits_per_pixel'] == 1)
	 {
		$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
		if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
		elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
		elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
		elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
		elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
		elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
		elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
		elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
		$COLOR[1] = $PALETTE[$COLOR[1]+1];
	 }
	 else
		return FALSE;
	 imagesetpixel($res,$X,$Y,$COLOR[1]);
	 $X++;
	 $P += $BMP['bytes_per_pixel'];
	}
	$Y--;
	$P+=$BMP['decal'];
   }

 //Fermeture du fichier
   fclose($f1);

 return $res;
}
