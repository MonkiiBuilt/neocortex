#!/usr/bin/env bash

log() {
	MSG=$1
	LOGFILE="/var/log/bootstrap.log"
	TIMESTAMP=`date "+%Y-%m-%d %H:%M:%S"`
	sudo bash -c "echo $TIMESTAMP' '$MSG >> $LOGFILE"
}

cl() {
	MSG=$1
	BLUE='\033[1;34m'
	RED='\033[0;31m'
	GREEN='\033[0;32m'
	if [[ $* == *-e* ]]; then # e is for error
		COLOUR=$RED
	elif [[ $* == *-s* ]]; then # s is for sucsess
		COLOUR=$GREEN
	else
		COLOUR=$BLUE
	fi
	NC='\033[0m' # No Color
	echo -e "${COLOUR}${MSG}${NC}"

	if [[ $* == *-l* ]]; then # l is for log
		log $MSG
	fi
}

php-setting-update() {
	KEY=$1
	VAL=$2
	if grep -q "$KEY" /etc/php/7.0/apache2/php.ini; then
        sed -i "s/$KEY = .*/$KEY = $VAL/" /etc/php/7.0/apache2/php.ini
    else
        sudo bash -c "echo $KEY' = '$VAL >> /etc/php/7.0/apache2/php.ini"
    fi
}
