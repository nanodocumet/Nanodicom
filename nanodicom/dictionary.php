<?php
/**
 * nanodicom/dictionary.php file
 *
 * @package    Nanodicom
 * @category   Base
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   1.1
 * @copyright  (c) 2010
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */

 /**
 * Nanodicom_Dictionary class.
 * @package    Nanodicom
 * @category   Base
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   1.1
 * @copyright  (c) 2010
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */
class Nanodicom_Dictionary
{
	public static $dict;
	public static $dict_by_name;
	
	// Array that holds which dictionaries have been loaded
	protected static $_loaded_dictionaries = array();

	// Make sure it only loads 1 time
	protected static $_loaded = FALSE;
	
	/**
	 * Loads the dictionary of the group into memory. Dictionaries are load by group
	 * to allow easy extension for private groups but primarily for performance issues.
	 * Many groups are rarely used, thus loading them wastes resources.
	 *
	 * @param	integer	 the group of the dictionary to load
	 * @param	mixed	 the vr_mode or to force to load the dictionary
	 * @return	void	 other times
	 */
	public static function load_dictionary($group, $force = FALSE)
	{
		// Only continue if we are forced to load the dictionary (when a string was passed to
		// the $this->_vr_reading_list) or when is has been explicitly said so (setting second
		// argument to TRUE)
		// Thus, it returns right away if neither of those are set
		if ( ! $force) return;

		// Let's load dictionary if it was not loaded yet
		if (! isset(self::$_loaded_dictionaries[$group])  
			AND file_exists(NANODICOMROOT.DIRECTORY_SEPARATOR.'nanodicom'.DIRECTORY_SEPARATOR.'dict'.DIRECTORY_SEPARATOR.sprintf('0x%04X',$group).'.php'))
		{
			// Load the dictionary
			require_once(NANODICOMROOT.DIRECTORY_SEPARATOR.'nanodicom'.DIRECTORY_SEPARATOR.'dict'.DIRECTORY_SEPARATOR.sprintf('0x%04X',$group).'.php');
			
			// Some dictionaries could be empty
			if (isset(Nanodicom_Dictionary::$dict[$group]) AND count(Nanodicom_Dictionary::$dict[$group]) > 0)
			{
				// Load the corresponding lookup table for names
				foreach (Nanodicom_Dictionary::$dict[$group] as $dict_element => $dict_data)
				{
					Nanodicom_Dictionary::$dict_by_name[strtolower($dict_data[2])] = array($group, $dict_element);
					unset($dict_element, $dict_data);
				}
			}
			
			// Dictionary was loaded
			self::$_loaded_dictionaries[$group] = TRUE;
		}
	}

	/**
	 * Create a new Nanodicom_Dictionary instance. There should be only 1 instance running at all times
	 *
	 * @return  void
	 */
	function __construct() 
	{
		if (self::$_loaded) return;
		// Load this class only once
		self::$_loaded = TRUE;
		
		// Group 0x0002		
		Nanodicom_Dictionary::$dict[0x0002][0x0000] = array('UL', '1', 'MetaElementGroupLength');
		Nanodicom_Dictionary::$dict[0x0002][0x0001] = array('OB', '1', 'FileMetaInformationVersion');
		Nanodicom_Dictionary::$dict[0x0002][0x0002] = array('UI', '1', 'MediaStorageSOPClassUID');
		Nanodicom_Dictionary::$dict[0x0002][0x0003] = array('UI', '1', 'MediaStorageSOPInstanceUID');
		Nanodicom_Dictionary::$dict[0x0002][0x0010] = array('UI', '1', 'TransferSyntaxUID');
		Nanodicom_Dictionary::$dict[0x0002][0x0012] = array('UI', '1', 'ImplementationClassUID');
		Nanodicom_Dictionary::$dict[0x0002][0x0013] = array('SH', '1', 'ImplementationVersionName');
		Nanodicom_Dictionary::$dict[0x0002][0x0016] = array('AE', '1', 'SourceApplicationEntityTitle');
		Nanodicom_Dictionary::$dict[0x0002][0x0100] = array('UI', '1', 'PrivateInformationCreatorUID');
		Nanodicom_Dictionary::$dict[0x0002][0x0102] = array('OB', '1', 'PrivateInformation');
		// Group 0xFFFE
		// IT = Item
		// DI = Delimitation Item
		Nanodicom_Dictionary::$dict[0xFFFE][0xE000] = array('IT', '1', 'Item');
		Nanodicom_Dictionary::$dict[0xFFFE][0xE00D] = array('DI', '1', 'ItemDelimitationItem');
		Nanodicom_Dictionary::$dict[0xFFFE][0xE0DD] = array('DI', '1', 'SequenceDelimitationItem');

		// Minimum set of groups loaded
		Nanodicom_Dictionary::$_loaded_dictionaries[0x0002] = TRUE;
		Nanodicom_Dictionary::$_loaded_dictionaries[0xFFFE] = TRUE;
	}

}

new Nanodicom_Dictionary;
