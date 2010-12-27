<?php
/**
 * tools/pixeler.php file
 *
 * @package    Nanodicom
 * @category   Tools
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   2.0
 * @copyright  (c) 2010
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */

/**
 * Dicom_Pixeler class.
 *
 * Extends Nanodicom. Pixel data reader. 
 * Currently support:
 * - Only uncompressed pixel data.
 * - Photometric Representations of: Monochrome1, Monochrome2 and RGB (color-by-plane and color-by-pixel)
 * - Big endian and little endian, explicit and implicit
 * - Pixel Representation: Unsigned Integer and 2's complement
 *
 * @package    Nanodicom
 * @category   Tools
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   2.0
 * @copyright  (c) 2010
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */
class Dicom_Pixeler extends Nanodicom {

	protected $_rows;
	protected $_cols;
	protected $_endian;
	protected $_vr_mode;
	protected $_dose_scaling;
	protected $_rescale_slope;
	protected $_rescale_intercept;
	protected $_samples_per_pixel;
	protected $_pixel_representation;
	
	/**
	 * Public method to get the images from the dicom object
	 *
	 * @param integer  a default window width
	 * @param integer  a default window center
	 * @return mixed false if something is missing or not image data found, otherwise an
	 * array of GD objects
	 */
	function get_image($width = NULL, $center = NULL) 
	{
		// Parse the object if not parsed yet
		$this->parse();

		// Set the profiler
		$this->profiler['pixel']['start'] = microtime(TRUE);

		// We need to have GD
		if ( ! function_exists('imagecreatetruecolor')) {
			return FALSE;
		}

		// Supported transfer syntaxes
		if ( ! in_array(trim($this->_transfer_syntax), array(Nanodicom::IMPLICIT_VR_LITTLE_ENDIAN,
													   Nanodicom::EXPLICIT_VR_LITTLE_ENDIAN,
													   Nanodicom::EXPLICIT_VR_BIG_ENDIAN,
													   '1.2.840.10008.1.2.1.99'))) 
		{
			return FALSE;
		}

		// Let's read some values from DICOM file
		$samples_per_pixel = $this->value(0x0028,0x0002);
		$rows              = $this->value(0x0028,0x0010);

		if ($rows == FALSE OR $samples_per_pixel === FALSE)
		{
			// There is no rows, no samples per pixel, no pixel data or malformed dicom file
			return FALSE;
		}
		$cols              = $this->value(0x0028,0x0011);
		$bits              = $this->value(0x0028,0x0100);
		$high_bit          = $this->value(0x0028,0x0102);
		$dose_scaling      = ($this->value(0x3004,0x000E) === FALSE) ? 1 : $this->value(0x3004,0x000E);
		$window_width      = ($width == NULL) ? (($this->value(0x0028,0x1051) === FALSE) ? 0 : $this->value(0x0028,0x1051)) : $width;
		$window_center     = ($center == NULL) ? (($this->value(0x0028,0x1050) === FALSE) ? 0 : $this->value(0x0028,0x1050)) : $center;
		$rescale_intercept = ($this->value(0x0028,0x1052) === FALSE) ? 0 : $this->value(0x0028,0x1052);
		$rescale_slope     = ($this->value(0x0028,0x1053) === FALSE) ? 1 : $this->value(0x0028,0x1053);
		$number_of_frames  = ($this->value(0x0028,0x0008) === FALSE) ? 1 : (int) $this->value(0x0028,0x0008);
		$pixel_representation       = $this->value(0x0028,0x0103);
		$photometric_interpretation = ($this->value(0x0028,0x0004) === FALSE) ? 'NONE' : trim($this->value(0x0028,0x0004));
		$planar_configuration 		= ($this->value(0x0028,0x0006) === FALSE) ? 0 : $this->value(0x0028,0x0006);
		$transfer_syntax_uid        = $this->value(0x0002,0x0010);
		$blob			            = $this->value(0x7FE0,0x0010);

		// Save some values for internal use
		// TODO: improve this, probably using $this->pixeler[]?
		$this->_rows				 = $rows;
		$this->_cols				 = $cols;
		$this->_dose_scaling		 = $dose_scaling;
		$this->_rescale_slope		 = $rescale_slope;
		$this->_rescale_intercept	 = $rescale_intercept;
		$this->_samples_per_pixel	 = $samples_per_pixel;
        $this->_pixel_representation = $pixel_representation;
		
		// Window Center and Width can have multiple values. By now, just reading the first one.
		// It assumes the delimiter is the "\" 
		if ( ! (strpos($window_center,"\\") === FALSE)) 
		{
			$temp          = explode("\\",$window_center);
			$window_center = (int) $temp[0];
		}
		if ( ! (strpos($window_width,"\\") === FALSE)) {
			$temp          = explode("\\",$window_width);
			$window_width = (int) $temp[0];
		}

		// Setting some values
		$images  = array();
		$max     = array();
		$min     = array();
		$current_position = $starting_position = 0;
		$length			  = strlen($blob);
		$current_image    = 0;
		$bytes_to_read    = (int) $bits/8;
		$size_image       = $cols*$rows*$samples_per_pixel*$bytes_to_read; 
		list($vr_mode, $endian) = Nanodicom::decode_transfer_syntax($transfer_syntax_uid);
		$this->_vr_mode = $vr_mode;
		$this->_endian  = $endian;

		// Only if no window center and width are set and the samples per pixel is 1. This could be costly!
		if (($window_center == 0 OR $window_width == 0) AND $samples_per_pixel == 1)
		{
			while ($current_position < $starting_position + $length)
			{
				if ($current_position == $starting_position + $current_image*$size_image) 
				{
					// A new image have been found
					$x   = 0;
					$y   = 0;
					for ($sample = 0; $sample < $samples_per_pixel; $sample++)
					{
						$max[$current_image][$sample] = -200000; // Small enough so it will be properly calculated
						$min[$current_image][$sample] = 200000;  // Large enough so it wil be properly calculated
					}
					$current_image++;
				}

				// This for should not be here, because samples per pixel is 1
				for ($sample = 0; $sample < $samples_per_pixel; $sample++)
				{
					$gray = $this->_read_gray($blob, $current_position, $bytes_to_read);
					$current_position += $bytes_to_read;
					
					// Getting the max
					if ($gray > $max[$current_image - 1][$sample]) 
					{
						// max
						$max[$current_image - 1][$sample] = $gray;
					}

					// Getting the min
					if ($gray < $min[$current_image - 1][$sample]) 
					{
						// min
						$min[$current_image - 1][$sample] = $gray;
					}
				}
				$y++;

				if ($y == $cols)  
				{ 
					// Next row
					$x++;
					$y = 0;
				}
			}
		}

		$current_position = $starting_position = 0;

		// Now let's create the right values for the images
		for ($index = 0; $index < $number_of_frames; $index++) 
		{
			if ($samples_per_pixel == 1)
			{
				// Real max and min according to window center & width (if set)
				$maximum = ($window_center != 0 && $window_width != 0)? round($window_center + $window_width/2) : $max[$index][0];
				$minimum = ($window_center != 0 && $window_width != 0)? round($window_center - $window_width/2) : $min[$index][0];

				// Check if window and level are sent
				$maximum = (!empty($window) && !empty($level))? round($level + $window/2) : $maximum;
				$minimum = (!empty($window) && !empty($level))? round($level - $window/2) : $minimum;

				if ($maximum == $minimum) 
				{ 
					// Something wrong. Avoid having a zero division
					return FALSE;	
				}
			}

			// Create the GD object
			$img = imagecreatetruecolor($cols, $rows);
			for($x = 0; $x < $rows; $x++) 
			{
				for ($y = 0; $y < $cols; $y++)
				{
					if ($samples_per_pixel == 1)
					{
						$gray = $this->_read_gray($blob, $current_position, $bytes_to_read);
						$current_position += $bytes_to_read;

						// truncating pixel values over max and below min
						$gray = ($gray > $maximum)? $maximum : $gray;
						$gray = ($gray < $minimum)? $minimum : $gray;

						// Converting to gray value
						$gray = ($gray - $minimum)/($maximum - $minimum)*255;

						// For MONOCHROME1 we have to invert the pixel values.
						if ($photometric_interpretation == "MONOCHROME1") 
						{
							$gray = 255 - $gray;
						}
						// Set the color
						$color = imagecolorallocate($img, $gray, $gray, $gray);
						$gray = NULL;
					}
					else
					{
						// It is RGB
						$gray = array();
						for ($sample = 0; $sample < $samples_per_pixel; $sample++)
						{
							$current_position = ($planar_configuration == 0)
											  ? $current_position
											  : (($x*$cols + $y) % $samples_per_pixel) * ($rows * $cols);
							$gray[$sample] = $this->_read_gray($blob, $current_position, $bytes_to_read);
							$current_position += $bytes_to_read;
						}
						// Set the color
						$color = imagecolorallocate($img, $gray[0], $gray[1], $gray[2]);
						$gray = NULL;
					}
					// Set the pixel value
					imagesetpixel($img, $y, $x, $color);
					$color = NULL;
				}
			}
			// Append the current image
			$images[] = $img;
			$img = NULL;
		}

		// Collect the ending time for the profiler
		$this->profiler['pixel']['end'] = microtime(TRUE);
        return $images;
    }

	/**
	 * Internal method to read a 'gray' value.
	 *
	 * @param string the blob that holds the pixel data
	 * @param integer the current position in the string to read
	 * @param integet the number of bytes to read
	 * @return integer the gray or color value at the given location
	 */
	protected function _read_gray($blob, $current_position, $bytes_to_read)
	{
		// For RGB values with planar configuration of 1 (RRRRRGGGGGGBBBBBB)

		if ($this->_samples_per_pixel == 1)
		{
			$chunk = substr($blob, $current_position, $bytes_to_read);
			$gray = $this->{Nanodicom::$_read_int}($bytes_to_read, $this->_endian, $bytes_to_read, Nanodicom::UNSIGNED, $chunk);
			$chunk = NULL;
			
			// Checking if 2's complement
			$gray = ($this->_pixel_representation)? self::complement2($gray, $this->_high_bit) : $gray;
			// Getting the right value according to slope and intercept 
			$gray = $gray*$this->_rescale_slope + $this->_rescale_intercept;
			// Multiplying for dose_grid_scaling
			return $gray*$this->_dose_scaling;
		}
		else
		{
			// Read current position and 
			$chunk = substr($blob, $current_position, $bytes_to_read);
			return $this->{Nanodicom::$_read_int}($bytes_to_read, $this->_endian, $bytes_to_read, Nanodicom::UNSIGNED, $chunk);
		}
	}

	/**
	 * Static method to find the complement of 2, returns an integer.
	 *
     * @param integer  the integer number to convert
     * @param integer  the high bit for the value. By default 15 (assumes 2 bytes)
     * @return integer the number after complement's 2 applied 
	 */
    static function complement2($number, $high_bit = 15) 
    {
        $sign = $number >> $high_bit;
        if ($sign) 
		{ 
			// Negative
            $number = -pow(2, $high_bit + 1) - $number;
        }
        return $number;
    }
}
