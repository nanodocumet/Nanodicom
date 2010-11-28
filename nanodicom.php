<?php
/**
 * Nanodicom class. Simple wrapper for Core class.
 *
 * @package    Nanodicom
 * @category   Base
 * @author     Nano Documet
 * @copyright  (c) 2010
 * @license    MIT-license http://www.opensource.org/licenses/mit-license.php
 */

 if (PHP_INT_SIZE > 4)
{
	define('NANODICOM_READ_INT', '_read_int_64');
	define('NANODICOM_WRITE_INT', '_write_int_64');
}
else
{
	define('NANODICOM_READ_INT', '_read_int_32');
	define('NANODICOM_WRITE_INT', '_write_int_32');
}

// Set the full path to the current folder
define('NANODICOMROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

require_once NANODICOMROOT.'nanodicom'.DIRECTORY_SEPARATOR.'core.php';

// Abstract class.
abstract class Nanodicom extends Nanodicom_Core {}
