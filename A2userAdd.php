#!/usr/bin/php
<?php
/***********************************************************************

A2userAdd.php

CELLABYTE ServerTools, version: 0.0.1
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
echo 'Enter the new user login (for example: mysite): ';
$a2login = CliRead();
if(empty($a2login)){
	exit('Login is not entered.');
}
echo 'Enter the domain of the new user (for example: example.com): ';
$a2domain = CliRead();
if(empty($a2domain)){
	exit('Domain is not entered.');
}
exec("useradd -s /bin/bash -U -m $a2login"); // add a new user $a2login
exec("passwd $a2login"); // add password new user $a2login
$a2domainAlias = 'www.'.$a2domain;
file_put_contents('/etc/apache2/sites-available/'.$a2login, "# Much of this file is generated automatically by the program CELLABYTE ServerTools,\n# version: 1.0.\n#\n<VirtualHost *:80>\n\tSuexecUserGroup $a2login $a2login\n\tServerAdmin $a2login@localhost\n\tDocumentRoot /home/$a2login/http\n\tServerName $a2domain\n\tServerAlias $a2domainAlias\n<IfModule mod_fcgid.c>\n\t<Directory /home/$a2login/http>\n\t\tOptions -Indexes FollowSymLinks +ExecCGI\n\t\tAllowOverride None\n\t\tAddHandler fcgid-script .php\n\t\tFcgidWrapper  /home/$a2login/fastcgi-bin/php.cgi.wrapper .php\n\t\tOrder allow,deny\n\t\tallow from all\n\t</Directory>\n</IfModule>\n#\tPossible values include: debug, info, notice, warn, error, crit, alert, emerg.\n\tLogLevel warn\n\tErrorLog /home/$a2login/logs/error.log\n\tCustomLog /home/$a2login/logs/access.log combined\n</VirtualHost>", FILE_APPEND); // and add a new file.
exec("a2ensite $a2login"); // includes site $a2login
exec('service apache2 reload', $output); // reload the configuration apache2
echo $output[0]."\033[0;32m".$output[1]."\033[0m"; // print the result
exec('ifconfig | grep inet | grep -v inet6 | grep -v 127.0.0.1 | cut -d: -f2 | awk \'{printf $1}\'', $outputIp); // get the current IP address of the server
file_put_contents('/etc/hosts', $outputIp[0]."\t$a2domain\n", FILE_APPEND); // add the domain in '/etc/hosts' file
echo "\n * \033[0;34m$a2domain\033[0;32m   ...done\033[0m.\n";
?>
