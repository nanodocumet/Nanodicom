<?php
/**
 * Nanodicom_Dictionary class.
 *
 * @package    Nanodicom
 * @category   Base
 * @author     Nano Documet
 * @copyright  (c) 2010
 * @license    MIT-license http://www.opensource.org/licenses/mit-license.php
 */

class Nanodicom_Dictionary
{
	public static $dict;
	public static $dict_by_name;
	
	function __construct() 
	{
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
	}
}

new Nanodicom_Dictionary;
