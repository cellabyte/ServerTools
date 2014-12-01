#!/usr/bin/php
<?php
/***********************************************************************

A2userDel.php

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
function CliRead($length = '255'){
	if (!isset ($GLOBALS['StdinPointer'])){
		$GLOBALS['StdinPointer'] = fopen('php://stdin', 'r');
	}
	$line = fgets($GLOBALS['StdinPointer'],$length);
	return trim($line);
}
echo 'Enter the login that be removed (for example: mysite): ';
$a2login = CliRead();
if(empty($a2login)){
	exit('Login is not entered.');
}
echo "Are you really want to delete account user $a2login and his home directory? yes/no (no): ";
$yesNo = CliRead();
if(isset($yesNo) && $yesNo == 'yes'){
	exec("a2dissite $a2login"); // turn off the site $a2login
	exec('service apache2 reload', $output); // reload the configuration apache2
	echo $output[0]."\033[0;32m".$output[1]."\033[0m"; // print the result
	if(is_file("/etc/apache2/sites-available/$a2login")){
		unlink("/etc/apache2/sites-available/$a2login"); // remove the settings by site
		exec("pkill -TERM -u $a2login"); // kill user $a2login processes
//		exec("fuser -k -TERM -m /home/$a2login"); // kill processes that have access to the user home directory
		exec("sleep 7 && userdel -r $a2login &"); // delete user $a2login and his home directory
		exec("sleep 9 && groupdel $a2login &"); // delete user group $a2login
		echo "\n * \033[0;34m$a2login\033[0;32m   ...delete\033[0m.\n";
	}else{
		exit("\nThis user has no configured site. \nAction\033[0;31m canceled\033[0m.\n");
	}
}else{
	exit("Action\033[0;31m canceled\033[0m.\n");
}
?>
