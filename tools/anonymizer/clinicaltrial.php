<?php
/**
 * tools/anonymizer/clinicaltrial.php file
 *
 * @package    Nanodicom
 * @category   Tools
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   1.1
 * @copyright  (c) 2010
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */

/**
 * Dicom_Anonymizer_Clinicaltrial class.
 *
 * Extends Dicom_Anonymizer. Use this to extend specific tools. The idea is to have it
 * specific to certain functionality. In this case, this tool it is an Anonymizer
 * specific for Clinical Trials.
 * @package    Nanodicom
 * @category   Tools
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   1.1
 * @copyright  (c) 2010
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */

class Dicom_Anonymizer_Clinicaltrial extends Dicom_Anonymizer {

	/**
	 * Public method test.
	 *
	 * Empty. Just to demo the extensibility.
	 *
	 * @return	void
	 */
	public function test()
	{
		// Nothing here.
	}
	
} // End Dicom_Anonymizer_Clinicaltrial
