#!/usr/bin/php
<?php
/***********************************************************************

ListingCatalog.php

CELLABYTE ServerTools, version: 0.0.2
Email: roman@cellabyte.com

Copyright 2014 Roman Verin

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

***********************************************************************/
$homeDir = getenv('HOME').'/';
include($homeDir.'.cellabyte/ServerTools/listingcatalog.conf'); // insert an element configuration

function CliRead($length = '255'){
	if (!isset ($GLOBALS['StdinPointer'])){
		$GLOBALS['StdinPointer'] = fopen('php://stdin', 'r');
	}
	$line = fgets($GLOBALS['StdinPointer'],$length);
	return trim($line);
}
// ask a directory and filename in which to write list
echo 'Enter the directory (for example: http/): ';
$inputDir[0] = CliRead();
if(!empty($inputDir[0])){
	$srcDir = $homeDir.$inputDir[0];
}
echo 'Enter the exception of 1 in the directory if there is (for example: plugins/): ';
$inputDir[1] = CliRead();
if(!empty($inputDir[1])){
	$exceptionDir[1] = $srcDir.$inputDir[1];
}
echo 'Enter the exception of 2 in the directory if there is: ';
$inputDir[2] = CliRead();
if(!empty($inputDir[2])){
	$exceptionDir[2] = $srcDir.$inputDir[2];
}
echo 'Enter the exception of 3 in the directory if there is: ';
$inputDir[3] = CliRead();
if(!empty($inputDir[3])){
	$exceptionDir[3] = $srcDir.$inputDir[3];
}
echo 'Enter the filename (for example: listing.md): ';
$listFile = CliRead();
if(empty($listFile)){
	$nameDate = date('Y-m-d_H-i');
	$listFile = "listing_$nameDate.md";
}
echo "\nDirectory: $srcDir\n";
echo "Exception directory: $exceptionDir[1]\n";
echo "Exception directory: $exceptionDir[2]\n";
echo "Exception directory: $exceptionDir[3]\n";
echo "Output file: $listFile\n\n";
echo 'Are you sure want Listing this directory? yes/no (yes): ';
$yesNo = CliRead();
if(isset($yesNo) && $yesNo == 'no'){
	exit("Action canceled.\n");
}
function ListDirectory($srcDir, $listFile, $exceptionDir){
	$dirHandle = opendir($srcDir);
	while (false !== ($file = readdir($dirHandle))){
		if (($file != '.')&&($file != '..')){
			if (is_dir($srcDir.$file)){ // if it is a directory
				if(!empty($exceptionDir) && $exceptionDir[1] == $srcDir.$file.'/' || $exceptionDir[2] == $srcDir.$file.'/' || $exceptionDir[3] == $srcDir.$file.'/'){ // if there are exceptions in the directory
					$fileDate = date('d.m.Y-H:i',filectime($srcDir.$file)); // determine the date
					echo "\033[0;32m$fileDate \033[0m\t\t$srcDir$file/ (\033[0;31mexcluded\033[0m)\n"; // print a result in the console with color highlighting
					file_put_contents($listFile, "- **$fileDate**`,\t\t$srcDir$file/ (`**excluded**`)`\n", FILE_APPEND); // and add to the file.
				}else{
					ListDirectory($srcDir.$file.'/', $listFile, $exceptionDir); // call the function again
				}
			}else{
				$fileSize = filesize($srcDir.$file).' byte'; // calculate the size
				$fileDate = date('d.m.Y-H:i',filectime($srcDir.$file)); // determine the date
				echo "\033[0;32m$fileDate \033[0m(\033[0;34m$fileSize\033[0m)\t$srcDir$file\n"; // print a result in the console with color highlighting
				file_put_contents($listFile, "- $fileDate ($fileSize)`,\t$srcDir$file`\n", FILE_APPEND); // and add to the file.
			}
		}
	}
	return false;
}
$listDate = date("d.m.Y, H:i");
$headerFile = "\n# List of files in a directory ($srcDir) - $listDate.\n";
file_put_contents($listFile, "$headerFile\n", FILE_APPEND); // insert title.
ListDirectory($srcDir, $listFile, $exceptionDir); // call the function ListDirectory()
?>
