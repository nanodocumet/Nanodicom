<?php
/**
 * tools/anonymizer.php file
 *
 * @package    Nanodicom
 * @category   Tools
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   1.1
 * @copyright  (c) 2010
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */

/**
 * Dicom_Anonymizer class.
 *
 * Extends Nanodicom. It overwrites certain file tags. Fully extensible.
 * @package    Nanodicom
 * @category   Tools
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   1.1
 * @copyright  (c) 2010
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */
class Dicom_Anonymizer extends Nanodicom {

	const RETURN_BLOB	= 0;
	//const CREATE_BACKUP	= 1;
	//const UPDATE 		= 3;

	// Very basic tag elements to anonymize
	protected static $_basic = array(
		array(0x0008, 0x0020, '{date|Ymd}'),			// Study Date
		array(0x0008, 0x0021, '{date|Ymd}'),			// Series Date
		array(0x0008, 0x0090, 'physician{random}'), 	// Referring Physician
		array(0x0010, 0x0010, 'patient{consecutive}'),  // Patient Name
		array(0x0010, 0x0020, 'id{consecutive}'), 		// Patient ID
	);
	
	// The mapped values
	public static $map;
	
	/**
	 * Anonymizes the dataset
	 *
	 * @param	mixed	 NULL or an array to overwrite defaults
	 * @param	integer	 the mode
	 * @return	string	 the anonymized dataset
	 */
	public function anonymize($tags = NULL, $mode = self::RETURN_BLOB)
	{
		$tags = ($tags == NULL) ? self::$_basic : $tags;
		$this->parse();
		$this->profiler['anonymize']['start'] = microtime(TRUE);
		
		// Update the tag elements to anonymized values
		foreach ($tags as $entries)
		{
			list($group, $element, $replacement) = $entries;
			$new_value = $this->_replace($group, $element, $replacement);
			$this->value($group, $element, $new_value);
		}
		
		// Return the new blob
		switch ($mode)
		{
			case self::RETURN_BLOB:
				// Return the blob
				$blob = $this->write();
			break;
			default:
				$blob = $this->write();
			break;
		}
		
		$this->profiler['anonymize']['end'] = microtime(TRUE);
		return $blob;
	}

	/**
	 * Replaces the values
	 *
	 * @param	integer	 the group
	 * @param	integer	 the element
	 * @param	string	 the replacement regex
	 * @return	string	 the new value
	 */
	protected function _replace($group, $element, $replacement)
	{
		$value = $this->value($group, $element);

		$name  = sprintf('0x%04X',$group).'.'.sprintf('0x%04X',$element);
		if (isset(self::$map[$name][$value])) 
			return self::$map[$name][$value];

		// Search for regex expressions
		if (preg_match('/{([a-z0-9]+)(\|([a-z0-9]+))?}$/i', $replacement, $matches))
		{
			switch ($matches[1])
			{
				// Set to date
				case 'date':
					self::$map[$name][$value] = str_replace('{date|'.$matches[3].'}', date($matches[3]), $replacement);
				break;
				// Consecutive
				case 'consecutive':
					$count = (isset(self::$map[$name])) ? count(self::$map[$name]) : 0;
					self::$map[$name][$value] = str_replace('{consecutive}', $count, $replacement);
				break;
				// Random, do not store it
				case 'random':
					return str_replace('{random}', sprintf('%04d',rand()), $replacement);
				break;
			}
		}
		else
		{
			self::$map[$name][$value] = $replacement;
		}
		return self::$map[$name][$value];
	}
	
} // End Dicom_Anonymizer
