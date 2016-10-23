#!/bin/bash

. "/vagrant/setup/helper-functions.sh"
. "/vagrant/setup/variables.sh"

mysqldump --add-drop-table "$DB_NAME" | grep "DROP TABLE" | mysql "${DB[name]}"
