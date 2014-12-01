#!/usr/bin/php
<?php
/***********************************************************************

StripComments.php

CELLABYTE ServerTools, version: 0.0.3
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
include($homeDir.'.cellabyte/ServerTools/stripcomments.conf'); // insert an element configuration

if(is_dir($srcDir)){ // if it is a directory
	exec("rm -r $stripDir"); // remove directory $stripDir
	mkdir($stripDir);
}else{
	exit("No Source Directory: $srcDir");
}

function StripDirectory($srcDir, $stripDir, $incDump, $exceptionDir, $exceptionFile){
	$dirHandle = opendir($srcDir);
	while(false !== ($file = readdir($dirHandle))){
		if(($file != '.') && ($file != '..')){
			if(is_dir($srcDir.$file)){ // if it is a directory
				@mkdir($stripDir.$file);
				StripDirectory($srcDir.$file.'/', $stripDir.$file.'/', $incDump, $exceptionDir, $exceptionFile); // call the function again
			}else{
				$ext = pathinfo($srcDir.$file, PATHINFO_EXTENSION);
				if($ext == 'php' && $srcDir === str_replace($exceptionDir, '', $srcDir) && $srcDir.$file === str_replace($exceptionFile, '', $srcDir.$file)){ // if this PHP file, and this don't exception file, or from exception directory (clever trick with str_replace() function to find the match of the array)
					$compressDump = php_strip_whitespace($srcDir.$file);
					$stripDump = substr_replace($compressDump, $incDump, 5, 0); // insert title
					$fileSize = filesize($srcDir.$file).' byte'; // calculate the size
					$fileDate = date('d.m.Y H:i',filectime($srcDir.$file)); // determine the date
					echo " *\033[0;32m processed \033[0;34m\t$ext\033[0m\t($fileSize)\t$srcDir$file\n"; // print a result in the console with color highlighting
					file_put_contents($stripDir.$file, $stripDump, FILE_APPEND); // and add to the file.
				}else{
					$fileSize = filesize($srcDir.$file).' byte'; // calculate the size
					$fileDate = date('d.m.Y H:i',filectime($srcDir.$file)); // determine the date
					echo " *\033[0;33m copied \033[0;34m\t$ext\033[0m\t($fileSize)\t$srcDir$file\n"; // print a result in the console with color highlighting
					copy($srcDir.$file, $stripDir.$file); // and copy.
				}
			}
		}
	}
	return false;
}
function DeleteFiles($deleteFile){
	for($i=1;$i<=50;$i++){
		if(isset($deleteFile[$i]) && file_exists($deleteFile[$i])){
			$ext = pathinfo($deleteFile[$i], PATHINFO_EXTENSION);
			$fileSize = filesize($deleteFile[$i]).' byte'; // calculate the size
			unlink($deleteFile[$i]); // remove
			echo " *\033[1;31m removed \033[1;34m\t$ext\033[0m\t($fileSize)\t".$deleteFile[$i]."\n"; // print a result in the console with color highlighting
		}
	}
	return false;
}
StripDirectory($srcDir, $stripDir, $incDump, $exceptionDir, $exceptionFile); // call the function StripDirectory()
DeleteFiles($deleteFile); // call the function DeleteFiles()
?>
