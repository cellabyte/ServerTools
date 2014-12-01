#!/bin/sh

DIR=$(dirname $(readlink -f $0))
SERVERTOOLS_PATH=/opt/ServerTools
CELLABYTE_HOME=$HOME/.cellabyte/ServerTools
PKGS='apache2 apache2-suexec-custom libapache2-mod-fcgid php5-cgi yui-compressor'

ABOUT() {
	clear
	cat <<'EOF'

CELLABYTE ServerTools is a software program
for Apache2 PHP Fastcgi Suexec Server.

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

EOF
}

HELP_INSTALL() {
	clear
	cat <<'EOF'
------------------------------------------------------------
Install help
------------------------------------------------------------

This install.sh file were tested only in Ubuntu 12.04 server.

EOF
	read -p '[q] Quit ' answer
	case $answer in
		q)
			echo ''
			echo 'Exiting...'
			busybox sleep 1
			exit
			;;
		*)
			HELP_INSTALL
	esac
}

SHOW_MENU() {
	clear
	cat <<'EOF'

=============== Install CELLABYTE ServerTools ================

Choose:

[1] Help
[2] Checking dependencies
[3] Install ServerTools
[4] Uninstall ServerTools

[q] Quit

EOF
}

CHECK_PACKAGES() {
	for pkg in $PKGS
	do
		dpkg-query -W -f='${Package} ${Status} ${Version}\n' $pkg
	done
}

SHOW_MENU
read Choose
case $Choose in
	1)
		HELP_INSTALL
		;;

	2)
		clear
		echo ''
		echo 'Checking dependencies...'
		echo ''
		CHECK_PACKAGES
		echo ''
		busybox sleep 1
		;;

	3)
		clear
		echo ''
		if test -d $SERVERTOOLS_PATH; then
			read -p 'ServerTools already installed. Update it? (Yes/No): ' answer
			case $answer in
				y|Y|Yes|yes|YES)
					echo 'Yes'
					echo ''
					sudo rm -rf $SERVERTOOLS_PATH
					sudo mkdir $SERVERTOOLS_PATH
#					sudo cp -rf $DIR/translations $SERVERTOOLS_PATH/translations
#					sudo cp -rf $DIR/help $SERVERTOOLS_PATH/help
					sudo cp $DIR/A2userAdd.php $SERVERTOOLS_PATH/a2useradd
					sudo cp $DIR/A2userDel.php $SERVERTOOLS_PATH/a2userdel
					sudo cp $DIR/ListingCatalog.php $SERVERTOOLS_PATH/listingcatalog
					sudo cp $DIR/StripComments.php $SERVERTOOLS_PATH/stripcomments
					sudo chmod 0755 $SERVERTOOLS_PATH/a2useradd
					sudo chmod 0755 $SERVERTOOLS_PATH/a2userdel
					sudo chmod 0755 $SERVERTOOLS_PATH/listingcatalog
					sudo chmod 0755 $SERVERTOOLS_PATH/stripcomments
					sudo cp $DIR/LICENSE $SERVERTOOLS_PATH/LICENSE
					echo ''
					echo 'ServerTools updated.'
					busybox sleep 1
					;;
				*)
					echo 'No'
					echo ''
					echo 'Exiting...'
					busybox sleep 1
					;;
			esac
		else
			if test ! -d $CELLABYTE_HOME; then
				mkdir -p $CELLABYTE_HOME
			fi
			sudo mkdir $SERVERTOOLS_PATH
#			sudo cp -rf $DIR/translations $SERVERTOOLS_PATH/translations
#			sudo cp -rf $DIR/help $SERVERTOOLS_PATH/help
			sudo cp $DIR/A2userAdd.php $SERVERTOOLS_PATH/a2useradd
			sudo cp $DIR/A2userDel.php $SERVERTOOLS_PATH/a2userdel
			sudo cp $DIR/ListingCatalog.php $SERVERTOOLS_PATH/listingcatalog
			sudo cp $DIR/StripComments.php $SERVERTOOLS_PATH/stripcomments
			sudo chmod 0755 $SERVERTOOLS_PATH/a2useradd
			sudo chmod 0755 $SERVERTOOLS_PATH/a2userdel
			sudo chmod 0755 $SERVERTOOLS_PATH/listingcatalog
			sudo chmod 0755 $SERVERTOOLS_PATH/stripcomments
			sudo cp $DIR/LICENSE $SERVERTOOLS_PATH/LICENSE
			echo ''
			echo 'ServerTools installed.'
			busybox sleep 1
		fi
		echo ''
		;;

	4)
		clear
		echo ''
		if test -d $SERVERTOOLS_PATH; then
			read -p 'Do you really want to uninstall the ServerTools? (Yes/No): ' answer
			case $answer in
				y|Y|Yes|yes|YES)
					echo 'Yes'
					echo ''
					sudo rm -rf $SERVERTOOLS_PATH
					echo ''
					echo 'ServerTools uninstalled.'
					busybox sleep 1
					;;
				*)
					echo 'No'
					echo ''
					echo 'Exiting...'
					busybox sleep 1
					;;
			esac
		else
			echo ''
			echo 'ServerTools is not installed.'
			busybox sleep 1
		fi
		echo ''
		;;

	*)
		echo 'Exiting...'
		busybox sleep 1
		;;
esac
