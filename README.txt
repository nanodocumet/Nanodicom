Latest Version
--------------
1.1 - Amazonic Bagua (Stable)
1.2 - Imperial Cusco (Stable)

Download
--------
https://github.com/nanodocumet/Nanodicom

Explanation
-----------
There seems to be only one available PHP DICOM parser: php-dicom. However, after 
using it for a while (I took the lead on that project and added some new 
features), I felt that it needed a big overhaul.
The rationale for this new toolkit is to provide the following features:
- Robustness. Should not abruptly die. Exceptions are now triggered properly.
- No dependencies. Self-contained, period.
- Performance improvements. 
  - Memory is kept as low as possible, expect to have at least 10-30% extra 
    of the size of the DICOM object to be read.
  - Speed. Dictionaries are avoided at parsing time. Only a small subset is 
    loaded. If only certain tags are requested, the parsing will stop there.
  - Handling exceptions have been added.
- Up to date. DICOM dictionary has been updated to 2009. Except for few cases 
  (multiplicity and conditional VR) almost 99% of tags are present. The new way
  to load dictionary allows for easy extensibility to include own private
  dictionaries.
- Documentation. Great tools should have great documentation. Users should solve
  most of their inquiries by reading documentation. More to come. For now, inline
  documentation is extensively provided.
- Community-based. Using github to host the source code, hopefully others can
  contribute.
- Extensibility. The core code should be kept intact. The toolkit allows the easy
  extension of the main core class. It also allows the dynamic extension to include
  other tools at runtime. Please be aware that this feature is still experimental.
  Read Additional notes at the end of the file for more information.
  
Requirements
------------
- PHP 5.2.6+ (tested, possibly older versions)

License
-------
The MIT License

Copyright (c) 2010-2011 Jorge "Nano" Documet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

Tested environments
-------------------
1)
32-bits Windows7
PHP 5.3.0 (cli) (built: Jun 29 2009 21:25:23)
Copyright (c) 1997-2009 The PHP Group
Zend Engine v2.3.0, Copyright (c) 1998-2009 Zend Technologies
    with Xdebug v2.1.0, Copyright (c) 2002-2010, by Derick Rethans
	
2)
64-bits "Linux 2.6.32-4-pve #1 SMP Mon Sep 20 11:36:51 CEST 2010 x86_64 
 GNU/Linux" - Debian Lenny
PHP 5.2.6-1+lenny9 with Suhosin-Patch 0.9.6.2 (cli) (built: Aug  4 2010 
06:06:53)
Copyright (c) 1997-2008 The PHP Group
Zend Engine v2.2.0, Copyright (c) 1998-2008 Zend Technologies

3)
32-bits "Linux 2.6.32-24-generic-pae #42-Ubuntu SMP Fri Aug 20 15:37:22 UTC
 2010 i686 GNU/Linux" - Ubuntu 10.04 (Virtual Machine)
PHP 5.3.2-1ubuntu4.2 with Suhosin-Patch (cli) (built: May 13 2010 20:01:00)
Copyright (c) 1997-2009 The PHP Group
Zend Engine v2.3.0, Copyright (c) 1998-2010 Zend Technologies

4)
64 bits "Linux 2.6.33.7-vs2.3.0.36.30.4 #23 SMP Tue Sep 28 05:47:35 PDT 2010 
 x86_64 GNU/Linux" - Debian Lenny (Virtual Machine)
PHP 5.2.14 (cli) (built: Oct  4 2010 16:17:01)
Copyright (c) 1997-2010 The PHP Group
Zend Engine v2.2.0, Copyright (c) 1998-2010 Zend Technologies
	with Zend Extension Manager v1.2.2, Copyright (c) 2003-2007, by Zend 
	 Technologies
	with XCache v1.2.2, Copyright (c) 2005-2007, by mOo
	with Zend Optimizer v3.3.9, Copyright (c) 1998-2009, by Zend Technologies

5)
32 bits "Linux 2.6.32-24-generic-pae #39-Ubuntu SMP Wed Jul 28 07:39:26 UTC
 2010 i686 GNU/Linux" - Ubuntu 10.04 (Virtual Machine)
PHP 5.3.2-1ubuntu4.5 with Suhosin-Patch (cli) (built: Sep 17 2010 13:41:55)
Copyright (c) 1997-2009 The PHP Group
Zend Engine v2.3.0, Copyright (c) 1998-2010 Zend Technologies

6)
64 bits Sun Solaris Ultra-Sparc
More details to come

Steps to use the module
-----------------------
1) Place the nanodicom and dicom folders anywhere (but they have to be in the 
 same parent folder)
2) Add the following statement anywhere in your code 
require_once PATH_TO_NANODICOM.'nanodicom.php';
3) Load files the following way:
$dicom = Nanodicom::factory(LOCATION_OF_FILE);

Supported official extensions (tools)
-------------------------------------
'simple'	 => Simple and dummy wrapper to Core. Nothing extra
'dumper'	 => To dump the contents of a DICOM file. Very helpful for dumping
				 to prompt or creating html output.
'anonymizer' => To anonymize DICOM files. It keeps track of repeated values
				among files for consistency.
				New v1.1!: Searches and replaces inside Sequences. Great for DICOMDIR
				New v1.2!: Allows passing an array to replace values if found. Great
				  when loading anonymized values from DB or other sources.
'pixeler'	 => New v1.2!: Reads pixel data from images and returns image objects.
				Uncompressed images	only for now.
				New v1.2!: Reads RLE, jpeg 8-bit lossy and uncompressed data.
				New v1.2!: Reads Monochrome1 and Monochrome2 or Paletter color.
				New v1.2!: Reads Planar Configuration of 0 (Color-by-pixel RGBRGB...)
				 and 1 (Color-by-plane RRRR...GGGG...BBBB...)

Miscelaneous
------------
- Coding conventions from Kohana: 
 http://kohanaframework.org/guide/about.conventions
- DICOM standard:
 ftp://medical.nema.org/medical/dicom/2009/
- DICOM Dictionary up to 2009 version (self-extracted)
 ftp://medical.nema.org/medical/dicom/2009/09_06pu3.pdf
- Special thanks to the hundreds of people that uploaded their DICOM files to the 
  php-DICOM test page. I have used that repository to test this new toolkit extensively.
- DICOM sample files have been obtained from python package "pydicom" (thanks Darcy)
  http://code.google.com/p/pydicom/
  http://code.google.com/p/pydicom/source/browse/source/dicom/testfiles/README.txt
 
Supported (Tested) Transfer Syntaxes
---------------------------
- For reading (actually, just for parsing, no pixel data is read)
  NONE
  1.2.840.10008.1.2		  Implicit VR Little Endian: Default Transfer Syntax for DICOM
  1.2.840.10008.1.2.1	  Explicit VR Little Endian
  1.2.840.10008.1.2.1.99  Deflated Explicit VR Little Endian
  1.2.840.10008.1.2.2	  Explicit VR Big Endian
  1.2.840.10008.1.2.4.50  JPEG Baseline (Process 1): Default Transfer Syntax 
						  for Lossy JPEG 8 Bit Image Compression
  1.2.840.10008.1.2.4.51  JPEG Extended (Process 2 & 4): Default Transfer Syntax for
						  Lossy JPEG 12 Bit Image Compression (Process 4 only)
  1.2.840.10008.1.2.4.57  JPEG Lossless, Non-Hierarchical (Process 14)
  1.2.840.10008.1.2.4.70  JPEG Lossless, Non-Hierarchical, First-Order Prediction 
						  (Process 14 [Selection Value 1]): Default Transfer Syntax 
						  for Lossless JPEG Image Compression
  1.2.840.10008.1.2.4.90  JPEG 2000 Image Compression (Lossless Only)
  1.2.840.10008.1.2.4.91  JPEG 2000 Image Compression
  1.2.840.10008.1.2.5	  RLE Lossless
  1.2.840.10008.1.2.5.1.1 UNKNOWN
- For writing (please see Additional Notes)
  1.2.840.10008.1.2		  Implicit VR Little Endian: Default Transfer Syntax for DICOM
  1.2.840.10008.1.2.1	  Explicit VR Little Endian
  1.2.840.10008.1.2.1.99  Deflated Explicit VR Little Endian
  1.2.840.10008.1.2.2	  Explicit VR Big Endian

Roadmap
-------
Version 1.3
1) Improve Pixeler performance
2) CLI (Command Line Interface) Tools.
Version 2.0
1) DICOM Network tools (low priority)

Examples
--------
1) Most basic example. Fast!
	$dicom = Nanodicom::factory($filename);
	// Only a small subset of the dictionary entries were loaded
	echo $dicom->parse()->profiler_diff('parse')."\n"; 

2) Load only given tags. It will stop once all given tags are found. Fastest!
	$dicom = Nanodicom::factory($filename, 'simple');
	$dicom->parse(array(array(0x0010, 0x0010)));
	// Only a small subset of the dictionary entries were loaded
	echo $dicom->profiler_diff('parse')."\n"; 
	echo 'Patient name if exists: '.$dicom->value(0x0010, 0x0010)."\n"; // Patient Name if exists
	// This will return nothing because dictionaries were not loaded
	echo 'Patient name should be empty here: '.$dicom->PatientName."\n";

3) Load only given tags by name. Stops once all tags are found. Not so fast.
	$dicom = Nanodicom::factory($filename, 'simple');
	$dicom->parse(array('PatientName'));
	echo $dicom->profiler_diff('parse')."\n";
	echo 'Patient name if exists: '.$dicom->value(0x0010, 0x0010)."\n"; // Patient Name if exists
	// Or
	echo 'Patient name if exists: '.$dicom->PatientName."\n"; // Patient Name if exists

4) Load only given tags. Dump it and print certain tags. Load 'dumper' directly.
	$dicom = Nanodicom::factory($filename, 'dumper');
	$dicom->parse(array(array(0x0010, 0x0010)));
	echo $dicom->dump();
	echo $dicom->profiler_diff('parse')."\n";
	// Patient Name if exists
	echo 'Something should show if element exists.'.$dicom->value(0x0010, 0x0010)."\n";
	// This will return the value because 'dumper' was used and loaded the dictionaries
	echo 'This should be empty, no dictionaries loaded.'.$dicom->PatientName."\n";

5) Load simple and print certain value
	$dicom = Nanodicom::factory($filename);
	$dicom->parse();
	echo $dicom->profiler_diff('parse')."\n";
	echo 'Patient Name: '.$dicom->value(0x0010, 0x0010)."\n"; // Patient Name if exists
	
6) Load simple and extend it to 'dumper'
	$dicom = Nanodicom::factory($filename);
	echo $dicom->parse()->extend('dumper')->dump();
	echo $dicom->profiler_diff('parse').' '.$dicom->profiler_diff('dump')."\n";

7) Load simple and extend it to 'dumper'. No need to parse, dump() does it. Parsing is done only once.
	$dicom = Nanodicom::factory($filename);
	echo $dicom->extend('dumper')->dump();
	echo $dicom->profiler_diff('parse').' '.$dicom->profiler_diff('dump')."\n";

8) Load 'dumper' directly. Dump output is in html format.
	$dicom = Nanodicom::factory($filename, 'dumper');
	echo $dicom->parse()->dump('html');
	echo $dicom->profiler_diff('parse').' '.$dicom->profiler_diff('dump')."\n";
	
9) Load 'anonymizer' directly.
	$dicom = Nanodicom::factory($filename, 'anonymizer');
	file_put_contents($filename.'.ex9', $dicom->anonymize());

10) Extend 'anonymizer'. No need to call parse(), anonymize() will do it.
	$dicom = Nanodicom::factory($filename);
	file_put_contents($filename.'.ex10', $dicom->extend('anonymizer')->anonymize());

11) Double extension (and probably you can go on and on)
	$dicom = Nanodicom::factory($filename);
	echo $dicom->extend('dumper')->dump();
	file_put_contents($filename.'.ex11', $dicom->extend('anonymizer')->anonymize());

12) Save file as Explicit VR Little Endian. Read Additional notes below!
	$dicom = Nanodicom::factory($filename);
	echo $dicom->parse()->profiler_diff('parse')."\n";
	// Setting values takes care of even length
	// If set to '1.2.840.10008.1.2.1.99' it will use deflate
	$dicom->value(0x0002, 0x0010, $dicom::EXPLICIT_VR_LITTLE_ENDIAN);
	echo $dicom->write_file($filename.'.ex12')->profiler_diff('write')."\n";

13) Pass file contents instead of filename.
	$contents = file_get_contents(FILE);
	$dicom = Nanodicom::factory($contents, 'dumper', 'blob');
	echo $dicom->dump();
	
14) Check if file has preamble and DICM.
	$dicom = Nanodicom::factory($filename);
	echo 'Is DICOM? '.$dicom->is_dicom()."\n";

15) Anonymize and save file as Explicit VR Little Endian.  Read Additional notes below!
	$dicom = Nanodicom::factory($filename);
	echo $dicom->parse()->profiler_diff('parse')."\n";
	// Setting values takes care of even length
	// If set to '1.2.840.10008.1.2.1.99' it will use deflate
	$dicom->value(0x0002, 0x0010, $dicom::EXPLICIT_VR_LITTLE_ENDIAN);
	file_put_contents($filename.'.ex15', $dicom->extend('anonymizer')->anonymize());

16) Provide your own dumper formatting
	$formatting = array(
		'dataset_begin'	=> '',
		'dataset_end'	=> "\n",
		'item_begin'	=> '',
		'item_end'		=> '',
		'spacer'		=> ' ',
		'text_begin'	=> '',
		'text_end'		=> '',
		'columns'		=> array(
			'off'		=> array('%04X', ' '),
			'g'			=> array('%04X', ':'),
			'e'			=> array('%04X', ' '),
			'name'		=> array('%-30.30s', ' '),
			'vr'		=> array('%2s', ' '),
			'len'		=> array('%-3d', ' '),
			'val'		=> array('[%s]', ''),
		),
	);
	$dicom = Nanodicom::factory($filename, 'dumper');
	echo $dicom->dump($formatting);

17) Anonymize on the fly and dump the contents, don't save to a file
	$dicom  = Nanodicom::factory($filename, 'anonymizer');
	$dicom1 = Nanodicom::factory($dicom->anonymize(), 'dumper', 'blob');
	echo $dicom1->dump();
	unset($dicom);
	unset($dicom1);
	
18) Pass your own list of elements to anonymizer
	// Own tag elements for anonymizing
	$tags = array(
		array(0x0008, 0x0020, '{date|Ymd}'),			// Study Date
		array(0x0008, 0x0021, '{date|Ymd}'),			// Series Date
		array(0x0008, 0x0090, 'physician{random}'),		// Referring Physician
		array(0x0010, 0x0010, 'patient{consecutive}'),  // Patient Name
		array(0x0010, 0x0020, 'id{consecutive}'), 		// Patient ID
		array(0x0010, 0x0030, '{date|Ymd}'), 			// Patient Date of Birth
	);
	$dicom  = Nanodicom::factory($filename, 'anonymizer');
	$dicom1 = Nanodicom::factory($dicom->anonymize($tags), 'dumper', 'blob');
	echo $dicom1->dump();
	unset($dicom);
	unset($dicom1);

19) Pass your own list of mappings to anonymizer. Patient Name should be replace to
	'Mapped' if 'Anonymized' is found. Case sensitive

	// Own tag elements for anonymizing
	$tags = array(
		array(0x0008, 0x0020, '{date|Ymd}'),			// Study Date
		array(0x0008, 0x0021, '{date|Ymd}'),			// Series Date
		array(0x0008, 0x0090, 'physician{random}'),		// Referring Physician
		array(0x0010, 0x0010, 'patient{consecutive}'),  // Patient Name
		array(0x0010, 0x0020, 'id{consecutive}'), 		// Patient ID
		array(0x0010, 0x0030, '{date|Ymd}'), 			// Patient Date of Birth
	);
	$replacements = array(
		array(0x0010, 0x0010, 'anonymized', 'Mapped'),
	);
	$dicom  = Nanodicom::factory($filename, 'anonymizer');
	$dicom1 = Nanodicom::factory($dicom->anonymize($tags, $replacements), 'dumper', 'blob');
	echo $dicom1->dump();
	file_put_contents($filename.'.ex19', $dicom1->write());
	unset($dicom);
	unset($dicom1);

	
20) Gets the images from the dicom object if they exist. This example is for gd
	$dicom  = Nanodicom::factory($filename, 'pixeler');
	if ( ! file_exists($filename.'.0.jpg'))
	{
		$images = $dicom->get_images();
		// If using another library, for example, imagemagick, the following should be done:
		// $images = $dicom->set_driver('imagick')->get_images();

		if ($images !== FALSE)
		{
			foreach ($images as $index => $image)
			{
				// Defaults to jpg
				$test->write_image($image, $dir.$file.'.'.$index);
				// To write another format, pass the format in second parameter.
				// This will write a png image instead
				// $test->write_image($image, $dir.$file.'.'.$index, 'png');
			}
		}
		else
		{
			echo "There are no DICOM images or transfer syntax not supported yet.\n";
		}
		$images = NULL;
	}
	else
	{
		echo "Image already exists\n";
	}
	unset($dicom);

21) Prints summary report
	$dicom  = Nanodicom::factory($filename);
	echo $dicom->summary();
	unset($dicom);

22) Proper way of handling exceptions
	try
	{
		// All other examples should be placed here
		$dicom = Nanodicom::factory('simple', $filename);
		echo $dicom->parse()->profiler_diff('parse')."\n";
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n";
	}

And more to come...

Additional notes
---------------
1) Attention! No pixel data conversion is provided yet to change transfer syntaxes at will. Even though
   the toolkit DOES change the transfer syntax and the related encoding is verified (Implicit vs Explicit and 
   Big vs Little Endian), encapsulated pixel data IS NOT CONVERTED! Thus, encapsulated Dicom files with a not
   encapsulated Transfer Syntax can lead to problems.
2) Extended tools get a copy of the current Nanodicom object at the moment of extension. Whatever happens in the
   extended tool is not kept in sync with the main (initially loaded) tool. This could lead to unexpected 
   results. Thus, please treat the 'extend' feature still as experimental. Loading the right tool from the 
   beginning (ie dumper or anonymizer) and performing any operations directly there is recommended.
