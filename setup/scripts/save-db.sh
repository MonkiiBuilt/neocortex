#!/bin/bash

. "/vagrant/setup/helper-functions.sh"
. "/vagrant/setup/variables.sh"

# Loop over all $SITES, but allow for an argument to do only one site
USE_SITES="$DATABASES"

while getopts "d" OPTION
do
     case $OPTION in
         d)
             USE_SITES="$OPTARG"
             ;;
         y)
             FORCE=1
             ;;
     esac
done


for SITE_DB_NAME in ""$USE_SITES; do

    # Make sure DB exists
    if ! mysql -e "use $SITE_DB_NAME" >/dev/null 2>&1; then
        cl "Error: This script expects a database called $SITE_DB_NAME but that does not exist." -e
        log "save-db was called but $SITE_DB_NAME does not exist"
        exit
    fi

    # If Drupal clear cache
    if [ "$FRAMEWORK" == "drupal7" ]; then
        cd "/var/www/$HOST_NAME/sites/$SITE" && drush cc all
    fi

    # Dump db
    DATE=`date +%Y-%m-%d:%H:%M`
    FILE_NAME="${SITE_DB_NAME}_${DATE}_db.sql.gz"
    BACKUP_PATH="${DB_DUMP_DIR}/${FILE_NAME}"

    mysqldump --opt "$SITE_DB_NAME" | gzip > "$BACKUP_PATH"

    if [ -f "$BACKUP_PATH" ]; then
        MSG="Backed up database to $FILE_NAME"
        cl "$MSG" -s
        log "$MSG"
    else
        MSG="There was an error backing up the database."
        cl "$MSG" -e
        log "$MSG"
    fi
done
