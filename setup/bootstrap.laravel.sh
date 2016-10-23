#!/bin/bash

# Look for the existence of app.php in laravel's bootstrap directory.
# If this file does not exist we delete the entire www directory and
# install fresh laravel application.
if [ ! -f "/var/www/$HOST_NAME/bootstrap/app.php" ]; then
    cl "Running Laravel specific provisioning"

    cd /tmp

    # Download the Laravel installer
    composer global require "laravel/installer=~1.1"

    # Create symlink for laravel binary
    ln -s /root/.composer/vendor/bin/laravel /usr/local/bin/laravel

    cd /vagrant

    # Delete existing www directory
    rm -rf www

    # Install new laravel app
    laravel new "www"

fi