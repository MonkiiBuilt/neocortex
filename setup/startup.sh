#!/usr/bin/env bash

. "/vagrant/setup/helper-functions.sh"
. "/vagrant/setup/variables.sh"

# Set up mount on Bubbles
#/vagrant/setup/project-data.sh

# Create symlinks to git hooks
/vagrant/setup/scripts/symlink-git-hooks.sh

su - vagrant -c "/vagrant/setup/scripts/post-project-update.sh"

service apache2 start