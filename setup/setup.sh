#!/bin/bash

DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd $DIR

read -p "You appear to be setting up a new Sledge. Would you like some help (y/n)? "

if [ $REPLY != 'y' ] ; then
	echo
	echo "Sorry for bothering you"
	exit 0
fi

echo
echo "Great! Things are always more fun when you've got a friend to help you"

echo
echo "First I'm going to make your cache and log directories writable. This will keep Kohana happy and make our server a better place to be"
sudo chmod 777 ./application/cache
sudo chmod 777 ./application/logs
sudo chmod 777 ./application/config


if [ ! -d "/etc/apache2/vhosts.d/" ]; then
	echo
	echo "Oh dear. I'm very sorry but you don't appear to have a /etc/apache2/vhosts.d directory"
	echo "You probably don't have apache installed or use a different directory for your vhosts files."
	echo "Unfortunately I don't know what to do now. If you want me to setup your Sledge you'll need to create the vhosts directory and re-run this script"
	
	exit 0
fi

echo
echo "We're going to setup your vhost file"
read -p "What is the hostname of your new Sledge site? "

# Substitute the correct locations into the vhosts file.
name=$REPLY
file=/etc/apache2/vhosts.d/$name.conf
docroot=${DIR/"/modules/sledge/setup"/""}

sudo cp ./vhost.conf $file
sudo sed "s|{docroot}|$docroot|" -i $file
sudo sed "s/{hostname}/$name/" -i $file

echo
read -p "I've copied the vhost file into the apache vhost directory. Would you like  me to restart apache for you (y/n)? "

if [ $REPLY == 'y' ]; then
	# Check that restarting apache isn't going to break anything.
	ok=`sudo apache2ctl configtest 2>&1 | grep -c 'Syntax OK'`

	if [ $ok == 1 ]; then
		sudo apache2ctl -k graceful
	else
		echo
		echo "Oh dear. There's a problem with Apache's config files."
		echo "Restarting Apache could break any existing sites."
		echo "Please check the output of Apache configtest and fix any problems before restarting Apache manually"
	fi
fi

echo
echo "Thankyou, come again"
