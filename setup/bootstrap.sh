#!/bin/bash

START_SECONDS="$(date +%s)"

. "/vagrant/setup/helper-functions.sh"
. "/vagrant/setup/variables.sh"

log "start of bootstrap"

##################################
#          Provisioning          #
##################################

# Don't ask for anything
export DEBIAN_FRONTEND=noninteractive

# Set MySQL root password
debconf-set-selections <<< "mysql-server-5.5 mysql-server/root_password password $DB_ROOT_PASS"
debconf-set-selections <<< "mysql-server-5.5 mysql-server/root_password_again password $DB_ROOT_PASS"


# Don't update more than one per day
if [ -f /var/log/vagrant_provision_last_update_time.log ]; then
    LAST_UPDATED=`cat /var/log/vagrant_provision_last_update_time.log`
    DAY_AGO=`date +%s --date='-1 day'`
else
    LAST_UPDATED=""
fi

if [ -z "$LAST_UPDATED" ] || [ "$LAST_UPDATED" -lt "$DAY_AGO" ]; then

    # Install packages
    cl "Install / update packages"

    log "updating packages"

    apt-get update -y
    apt-get install -q -f -y software-properties-common
    add-apt-repository -y ppa:ondrej/php
    apt-get update -y

    apt-get install -q -f -y \
      -o Dpkg::Options::='--force-confdef' -o Dpkg::Options::='--force-confold' \
      build-essential git language-pack-en-base unzip \
      apache2 \
      php7.1 php7.1-mcrypt php7.1-curl php-xdebug php7.1-mbstring php7.1-xml \
      php7.1-zip \
      libapache2-mod-php7.1 \
      mysql-server-5.7 php7.1-mysql php-mysql \
      imagemagick php-imagick \
      memcached php-memcached \
      postfix mailutils
#      libsqlite3-dev ruby1.9.1-dev

    apt-get -y remove puppet chef chef-zero puppet-common
    apt-get autoremove

    # Make log file to save last updated time
    date +%s > /var/log/vagrant_provision_last_update_time.log

else
    log "skipping updating packages"
    cl "Updates last ran less than a day ago so skipping"
fi

# Set timezone
echo "Australia/Melbourne" | tee /etc/timezone
dpkg-reconfigure --frontend noninteractive tzdata

# Setup apache
log "set up apache"
echo "ServerName localhost" >> /etc/apache2/apache2.conf
a2enmod php7.1
a2enmod rewrite
a2enmod ssl
a2enmod headers

ln -s /vagrant/setup/files/host.conf /etc/apache2/sites-available/host.conf
#cp /vagrant/setup/files/xdebug.ini /etc/php5/mods-available/xdebug.ini

# Create .my.cnf
cat > "/home/${USERNAME}/.my.cnf" << EOF
[client]
user=root
password=$DB_ROOT_PASS
EOF
cat > /root/.my.cnf << EOF
[client]
user=root
password=$DB_ROOT_PASS
EOF

# Log files should be accessible by all
sed -i 's/create 640 root adm/create 644 root adm/' /etc/logrotate.d/apache2
chmod g+rwx /var/log/apache2

# Configure PHP
log "configure php"
php-setting-update display_errors 'On'
php-setting-update error_reporting 'E_ALL | E_STRICT'
php-setting-update html_errors 'On'
php-setting-update xdebug.max_nesting_level '256'

# Make apache2 log folder readable by vagrant
sudo adduser "$USERNAME" admin

service apache2 restart

# Link repository webroot to server webroot
if [ ! -h "/var/www/$HOST_NAME" ] || [ ! -d "/var/www/$HOST_NAME" ]; then
    log "create symlink to webroot"
    ln -fs "$WEBROOT" "/var/www/$HOST_NAME"
fi


# Make sure symlinks to import and export database scripts exist
if [ ! -h "/usr/local/bin/load-db" ]; then
    log "create symlink for load-db script"
    ln -s /vagrant/setup/scripts/load-db.sh /usr/local/bin/load-db
fi
if [ ! -h "/usr/local/bin/save-db" ]; then
    log "create symlink for save-db script"
    ln -s /vagrant/setup/scripts/save-db.sh /usr/local/bin/save-db
fi


# Setup database
NEW_DB=false
for DB in ""$DATABASES; do
    if ! mysql -u root -p${DB_ROOT_PASS} -e "use ${DB}" >/dev/null 2>&1; then

      cl "setting up database. Name: ""$DB"", User: ""$DB_USER"", Host: $DB_HOST"

      cat | mysql -u root -ppassword << EOF
      DROP DATABASE IF EXISTS test;
      CREATE DATABASE ${DB};
      GRANT ALL ON ${DB}.* TO '${DB_USER}'@'${DB_HOST}' identified by '${DB_PASS}';
      FLUSH PRIVILEGES;
EOF

    fi
done


# Disable xdebug before running composer commands because they will take longer
#rm /etc/php5/cli/conf.d/20-xdebug.ini

# Install composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"


# CMS specific provisioning
for filename in /vagrant/setup/bootstrap.*.sh; do
    if [ "$filename" == "/vagrant/setup/bootstrap.""$CMS_SPECIFIC_BOOTSTRAP"".sh" ]; then
        cl "Running provisioning script for bootstrap.""$CMS_SPECIFIC_BOOTSTRAP"".sh" -l
        . $filename
    fi
done

# Re-enable xdebug
# Leaving this disabled as we aren't likely to make extensive use of xdebug on cli tasks ~lg@monkii.com
#cd /etc/php5/cli/conf.d/ && ln -s ../../mods-available/xdebug.ini 20-xdebug.ini

# Make a symlink from Laravel storage folder in public
rm -r /vagrant/storage/app/public
ln -s /vagrant/public /vagrant/storage/app/public

# Add web user to dialout group
adduser www-data dialout

# Finally, enable the new site
a2ensite host
a2dissite 000-default

# Make sure things are up and running as they should be
log "restart apache"
service apache2 restart

# Add vendor/bin to path for ease of running phpunit
echo 'PATH="/vagrant/vendor/bin:$PATH"' >> "/home/$USERNAME/.profile"

# Make sure composer vendors are installed
if [ ! -h "$WEBROOT/.env" ]; then
    su - vagrant -c "cd $WEBROOT && cp .env.example .env && composer install"
fi

if ! grep 'APP_KEY=\(.\+\)' "$WEBROOT/.env"; then
    su - vagrant -c "cd $WEBROOT && php artisan key:generate"
fi

# Run db migrations
su - vagrant -c "cd $WEBROOT && php artisan migrate"

# Say how long the script took to execute (with the seconds in bold yellow)
END_SECONDS="$(date +%s)"
TIME_ELAPSED=`expr $END_SECONDS - $START_SECONDS`
FORMATTED_TIME="$(date -u -d @${TIME_ELAPSED} +"%M minutes %S seconds")"
YELLOW='\033[0;33m'
BLUE='\033[1;34m'
BOLD=$(tput bold)
NORMAL=$(tput sgr0)
cl "Provisioning complete in $YELLOW$BOLD$FORMATTED_TIME$NORMAL $BLUE\n"

cl 'Access the test environment via http://localhost:9999'
cl ""

log "bootstrap finished"
