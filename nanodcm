#!/usr/bin/env php
<?php
/**
 * nanodcm file
 *
 * @package    Nanodicom
 * @category   Base
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   1.3.1
 * @copyright  (c) 2010-2011
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 */

/**
 * The Command Line interface for Nanodicom.
 *
 * This cli script helps you run Nanodicom from the command line without the need to create php files.
 * WARNING: Tested in Linux only.
 *
 * Heavily inspired by Goyote's Kohana Installer
 * [https://github.com/goyote/kohana-installer]
 *
 * @package    Nanodicom
 * @category   Cli
 * @author     Nano Documet <nanodocumet@gmail.com>
 * @version	   1.3.1
 * @copyright  (c) 2010-2011
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-license
 * @license    http://kohanaframework.org/license
 */

class Nanodicom_CLI
{
	/**
	 * Raw CLI arguments and options.
	 *
	 * @var  array
	 */
	protected $argv = array();

	/**
	 * Parsed CLI options.
	 *
	 * @var  array
	 */
	protected $passed_options = array();

	/**
	 * Found options to be used.
	 *
	 * @var  array
	 */
	protected $options = array();

	/**
	 * Class constructor.
	 *
	 * @param  array  $argv
	 */
	public function __construct(array $argv)
	{
		$this->argv = $argv;
		$this->parse_options();
		$this->root_dir = getcwd();
	}

	/**
	 * Parses and stores all the valid options from the raw $argv array.
	 */
	public function parse_options()
	{
		foreach ($this->argv as $option)
		{
			if (substr($option, 0, 2) !== '--')
			{
				continue;
			}

			$option = ltrim($option, '--');

			if (strpos($option, '=') !== FALSE)
			{
				list($option, $value) = explode('=', $option);

				if (strtolower($value) === 'false')
				{
					$value = FALSE;
				}
			}
			else
			{
				$value = TRUE;
			}

			$this->passed_options[strtolower($option)] = $value;
		}
	}

	/**
	 * Retrieves the value of a passed in CLI option.
	 *
	 * e.g. If --name=value was passed in it returns "value".
	 *
	 * @param   string  $name
	 * @param   string  $default
	 * @return  string
	 */
	public function get_option($name, $default = NULL)
	{
		if ($this->has_option($name))
		{
			return $this->passed_options[strtolower($name)];
		}

		return $default;
	}

	/**
	 * Checks to see if a option was passed in.
	 *
	 * Both --name and --name=value are valid formats, and will return true.
	 *
	 * @param   string  $name
	 * @return  boolean
	 */
	public function has_option($name)
	{
		return array_key_exists(strtolower($name), $this->passed_options);
	}

	/**
	 * Gets the common options shared by all tools
	 */
	public function common_options()
	{
		// Get the path, default to current dir
		$path = $this->get_option('path', $this->root_dir);

		// Only remove the trailing slash
		$path = rtrim($path, '/').'/';

		if (strlen($path) AND substr($path, 0, 1) != '/')
		{
			// Append "root_dir" only when is not an absolute path
			$path = $this->root_dir.'/'.$path;
		}

		// The mask to match
		$mask = $this->get_option('mask', '*');


		// Do it recursively
		$recursive = (boolean) $this->get_option('recursive', TRUE);

		// Show errors?
		$errors = $this->get_option('errors', TRUE);

		// Show warnings?
		$warnings = $this->get_option('warnings', TRUE);

		// Show warnings?
		$debug    = $this->get_option('debug', TRUE);

		return array($path, $mask, $recursive, $errors, $warnings, $debug);
	}

	/**
	 * Outputs the summary of the matched dicom files
	 */
	public function execute_summary()
	{
		// Grab the common options
		list($path, $mask, $recursive, $errors, $warnings, $debug) = $this->common_options();

		// Get the list of files (with full path)
		$files = $this->sdir($path, $mask, $recursive, TRUE);

		$return = '';

		foreach ($files as $file)
		{
			$return .= $this->colorize_output("Summary of file: ~\"".$file."\"~\n\n");

			// Create a dumper
			$dicom = Nanodicom::factory($file);

			// Get the summary
			$return .= $dicom->summary();

			if ($errors)
			{
				// Record any error if present
				$return .= $this->output_messages('errors', $dicom);
			}

			if ($warnings)
			{
				// Record any warning if present
				$return .= $this->output_messages('warnings', $dicom);
			}

			// Is a valid DICOM?
			$is_dicom = $dicom->is_dicom();

			if ($debug)
			{
				// Show the parsed time
				$return .= $this->colorize_output('File parsed in '.$dicom->profiler_diff('parse')." ms\n");
			}

			// Release memory
			unset($dicom);

			if ( ! $is_dicom )
			{
				continue;
			}


			$return .= $this->colorize_output('File ~'.$file."~ was successfully parsed\n\n");
		}

		exit($return);
	}

	/**
	 * Dumps the matched dicom files
	 */
	public function execute_dump()
	{
		// Grab the common options
		list($path, $mask, $recursive, $errors, $warnings, $debug) = $this->common_options();

		// The mode of output
		$output = $this->get_option('out', 'echo');

		// Get the list of files (with full path)
		$files = $this->sdir($path, $mask, $recursive, TRUE);

		$return = '';

		foreach ($files as $file)
		{
			$return .= $this->colorize_output("Dumping file: ~\"".$file."\"~\n\n");

			// Create a dumper
			$dicom = Nanodicom::factory($file, 'dumper');

			// Perform the dump
			$return .= $dicom->dump($output);

			if ($errors)
			{
				// Record any error if present
				$return .= $this->output_messages('errors', $dicom);
			}

			if ($warnings)
			{
				// Record any warning if present
				$return .= $this->output_messages('warnings', $dicom);
			}

			// Is a valid DICOM?
			$is_dicom = $dicom->is_dicom();

			if ($debug)
			{
				// Show the parsed time
				$return .= $this->colorize_output('File parsed in '.$dicom->profiler_diff('parse')." ms\n");

				// Show the dumping time
				$return .= $this->colorize_output('File dumped in '.$dicom->profiler_diff('dump')." ms\n");
			}

			// Release memory
			unset($dicom);

			if ( ! $is_dicom )
			{
				continue;
			}

			$return .= $this->colorize_output('File ~'.$file."~ was successfully dumped\n\n");
		}

		exit($return);
	}

	/**
	 * Anonymizes the matched dicom files
	 */
	public function execute_anonymize()
	{
		// Grab the common options
		list($path, $mask, $recursive, $errors, $warnings, $debug) = $this->common_options();

		// Overwrite?
		$overwride = (boolean) $this->get_option('overwrite', FALSE);

		// Any tags?
		$tags = $this->get_option('tags', NULL);

		$tags = ($tags !== NULL) ? json_decode($tags) : $tags;
		$tags = $this->convert_hex_strings_in_array($tags);

		// Any mapping?
		$map = $this->get_option('map', NULL);

		$map = ($map !== NULL) ? json_decode($map) : $map;
		$map = $this->convert_hex_strings_in_array($map);

		// Get the list of files (with full path)
		$files = $this->sdir($path, $mask, $recursive, TRUE);

		$return = '';

		foreach ($files as $file)
		{
			$return .= $this->colorize_output("Anonymizing file: ~\"".$file."\"~\n\n");

			// Create an anonymizer
			$dicom = Nanodicom::factory($file, 'anonymizer');

			// Perform the dump
			$blob = $dicom->anonymize($tags, $map);

			if ($errors)
			{
				// Record any error if present
				$return .= $this->output_messages('errors', $dicom);
			}

			if ($warnings)
			{
				// Record any warning if present
				$return .= $this->output_messages('warnings', $dicom);
			}

			// Is a valid DICOM?
			$is_dicom = $dicom->is_dicom();

			if ($debug)
			{
				// Show the parsed time
				$return .= $this->colorize_output('File parsed in '.$dicom->profiler_diff('parse')." ms\n");

				// Show the anonymizing time
				$return .= $this->colorize_output('File dumped in '.$dicom->profiler_diff('anonymize')." ms\n");
			}

			// Release memory
			$dicom = NULL;
			unset($dicom);

			if ( ! $is_dicom )
			{
				continue;
			}

			if ( ! $overwride)
			{
				// Get new backup file name
				$new_file = $this->get_backup_file($file);
				// Move original file to backup file
				rename($file, $new_file);
			}

			// Save anonymized file into original name
			file_put_contents($file, $blob);

			$blob = NULL;
			unset($blob);

			$return .= $this->colorize_output('File ~'.$file."~ was successfully anonymized\n\n");
		}

		exit($return);
	}

	/**
	 * Function to grab the messages (if exist) from last operation in DICOM object
	 *
	 * @param  string  $type       The type of message to output (errors or warnings)
	 * @param  object  $dicom      Nanodicom object
	 */
	public function output_messages($type = 'errors', $dicom)
	{
		$return = '';

		if ($dicom->status != Nanodicom::SUCCESS)
		{

			foreach ($dicom->{$type} as $message)
			{
				$label = substr(strtoupper($type), 0, -1);
				$return .= $this->colorize_output('!'.$label.': '.$message.'!')."\n";
			}
		}

		return $return;
	}

	/**
	 * Function to get a unique name for the backup file
	 *
	 * @param  string  $file      the file to which we will create the backup
	 */
	public function get_backup_file($file)
	{
		$count = 1;

		while (file_exists($file.'.bak'.$count))
		{
			$count++;
		}

		return $file.'.bak'.$count;
	}

	/**
	 * Function to get the files of a given directory. Based on original
	 * function written by [phpnet at novaclic dot com]
	 * (http://www.php.net/manual/en/function.scandir.php#95588)
	 *
	 *
	 * @param  string  $path      the path to scan
	 * @param  string  $mask      the mask used to match the files
	 * @param  boolean $recursive whether to iterate subfolders or not
	 * @param  boolean $no_cache  whether to cache the results
	 */
	public function sdir($path = '.', $mask = '*', $recursive = FALSE, $no_cache = FALSE)
	{
		static $dir = array(); // cache result in memory

		if ( ! is_dir($path))
		{
			exit($this->colorize_output("Path ~\"{$path}\"~ does not exist!\n\n"));
		}

		if ( ! isset($dir[$path]) OR $no_cache)
		{
			$dir[$path] = scandir($path);
		}

		$sdir = array();

		foreach ($dir[$path] as $i => $entry)
		{
			if ($entry == '.' OR $entry == '..')
				continue;

			if (is_dir($path.$entry) AND $recursive)
			{
				$files = $this->sdir($path.$entry.'/', $mask, $recursive, $no_cache);
				$sdir = array_merge($sdir, $files);
			}

			if (is_file($path.$entry) AND fnmatch($mask, $entry))
			{
				$sdir[] = $path.$entry;
			}
		}

		return $sdir;
	}

	/**
	 * Converts any group and element values from strings to integers
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function convert_hex_strings_in_array($data)
	{
		if ($data !== NULL AND count($data))
		{
			// Keep
			$updated_data = array();

			foreach ($data as $triplets)
			{
				// Get information
				$group = isset($triplets[0]) ? $triplets[0] : NULL;
				$group = (is_string($group)) ? hexdec($group) : $group;

				$element = isset($triplets[1]) ? $triplets[1] : NULL;
				$element = (is_string($element)) ? hexdec($element) : $element;

				$info = isset($triplets[2]) ? $triplets[2] : NULL;

				$updated_data[] = array($group, $element, $info);
			}

			$data = $updated_data;
		}

		return $data;
	}

	/**
	 * Shows the list of available commands.
	 */
	public function execute_help()
	{
		if ($this->has_option('verbose'))
		{
			$this->show_verbose_help_screen();
		}
		else
		{
			$this->show_help_screen();
		}
	}

	/**
	 * Creates a directory or series of directories.
	 *
	 * Any directory that currently doesn't exist will be created. Upon completion it will fix the
	 * permissions so that it's writable by everyone (appropriate for development,) the mode can
	 * be overridden.
	 *
	 * @param  string  $dir
	 * @param  string  $mode
	 */
	public function mkdir($dir, $mode = NULL)
	{
		system(sprintf('mkdir -p %s', escapeshellarg($dir)));
		$this->fix_permissions($dir, $mode);
	}

	/**
	 * Changes the permissions of a directory.
	 *
	 * Note: this function is recursive, so all of the folders and files underneath the target
	 * directory will also receive the same permissions. The default behaviour is to 777 the whole
	 * thing, makes it easier to develop locally.
	 *
	 * @param  string  $dir
	 * @param  string  $mode
	 */
	public function fix_permissions($dir, $mode = NULL)
	{
		if ( ! $mode)
		{
			// Default the mode to rxw by everyone
			$mode = '0777';
		}

		system(sprintf('chmod -R %s %s', escapeshellarg($mode), escapeshellarg($dir)));
	}

	/**
	 * Builds the desired path directory structure, fixing the permissions on all new directories.
	 *
	 * @param   string  $dir
	 * @param   string  $mode
	 * @return  string
	 */
	public function build_path_dir($dir = NULL, $mode = NULL)
	{
		$dir = trim($dir, '/');
		if ( ! $dir)
		{
			return $this->root_dir;
		}

		$dirs = explode('/', $dir);

		do
		{
			$folder = array_shift($dirs);
			$folder = substr($dir, 0, strpos($dir, $folder) + strlen($folder));

			if ( ! is_dir($this->root_dir.'/'.$folder))
			{
				$this->mkdir($this->root_dir.'/'.$dir, $mode);
				$this->fix_permissions($this->root_dir.'/'.$folder, $mode);
				break;
			}
		}
		while (count($dirs));

		return $this->root_dir.'/'.$dir;
	}

	/**
	 * Displays the full help screen; contains the list of available commands.
	 */
	public function show_verbose_help_screen()
	{
		exit($this->colorize_output(<<<EOF
*********************************************
*       Nanodicom Command Line helper       *
*********************************************

** Specify a command to run **
!All options are optional, if not set, default values will be used!
~$ nanodcm [command] --[option1]=[value] --[option2]=[value] ...~

common options
~$ --path=my/dir~                   <- Path to search. Absolute or relative.
                                    ~Default:~ Current directory
~$ --mask=*.dcm~                    <- Mask to match the filenames
                                    ~Default:~ Any file "*"
~$ --recursive=false~               <- To avoid searching in subfolders.
                                    ~Default:~ Do it recursively
~$ --errors=true~                   <- Output errors found
                                    ~Default:~ true.
~$ --warnings=true~                 <- Output warnings found
                                    ~Default:~ true.
~$ --debug=true~                    <- Shows debug messages. Mostly profiling values, currently
                                    only 'parsing' time, 'dumping' time and 'anonymizing' time.
                                    ~Default:~ true.

> summary:      Outputs the summary of the DICOM files found
~$ nanodcm summary --[option1]=[value] --[option2]=[value] ...~

~no extra options~

									
> dumper:       Dumps the DICOM files found
~$ nanodcm dump --[option1]=[value] --[option2]=[value] ...~

options
~$ --out=html~                      <- Output mode. Available by distribution: html or echo
                                    ~Default:~ "echo" mode.


> anonymizer:   Anonymizes the matched files
~$ nanodcm anonymize --[option1]=[value] --[option2]=[value] ...~

options
~$ --overwrite=true~                <- Anonymizes and overwrites all files found.
                                    ~Default:~ false. Creates a backup.

~$ --map={json_string}~             <- Adds a mapping capability in json format.
                                    ~Default:~ empty. Uses default from anonymizer tool.

~$ --tags={json_string}~             <- Adds a mapping capability in json format.
                                    ~Default:~ empty. Uses default from anonymizer tool.
									
EOF
		));
	}

	/**
	 * Displays the minimal help screen; contains the list of available commands.
	 */
	public function show_help_screen()
	{
		exit($this->colorize_output(<<<EOF
*********************************************
*       Nanodicom Command Line helper       *
*********************************************

[!] For quick snippets, try "nanodcm --verbose"

** Specify a command to run **

~summary~    Outputs the summaries of the DICOM files matched
~dump~       Dumps the files matched with the given pattern
~anonymize~  Anonymizes the files matched
~pixel~      Creates images out of the files matched [SOON!]

EOF
		));
	}

	/**
	 * Colorizes the output so it's more legible.
	 *
	 * @param   string  $output
	 * @return  string
	 */
	public function colorize_output($output)
	{
		// Color green for highlights
		preg_match_all('/~(.*?)~/', $output, $matches, PREG_SET_ORDER);

		foreach ($matches as $match)
		{
			$output = str_replace($match[0], "\033[0;32m".$match[1]."\033[0m", $output);
		}

		// Color red for Errors!
		preg_match_all('/!(.*?)!/', $output, $matches, PREG_SET_ORDER);

		foreach ($matches as $match)
		{
			$output = str_replace($match[0], "\033[0;31m".$match[1]."\033[0m", $output);
		}

		return $output;
	}
}

class Nanodcm extends Nanodicom_Cli
{
}

set_time_limit(0);

// First index is the name of this script
array_shift($argv);

$helper = new Nanodcm($argv);

if (empty($argv) OR $helper->has_option('verbose'))
{
	$helper->execute_help();
}
else if (method_exists($helper, $method = 'execute_'.strtolower(str_replace('-', '_', $argv[0]))))
{
	require_once 'nanodicom.php';
	call_user_func(array($helper, $method));
}
else if ( ! empty($argv[0]))
{
	exit("\"{$argv[0]}\" is not a valid command!\n\n");
}
