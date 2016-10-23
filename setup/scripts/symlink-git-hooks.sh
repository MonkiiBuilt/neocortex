#!/bin/bash

START_FOLDER=`pwd`

cd /vagrant/setup/git-hooks/

for HOOK in *; do
    # Always remove existing hooks and replace them
    if [ -f "/vagrant/.git/hooks/$HOOK" ]; then
        rm "/vagrant/.git/hooks/$HOOK"
    fi

    # Make a symlink to the script stored in the repo
    cd /vagrant/.git/hooks/
    ln -s "../../setup/git-hooks/$HOOK" .
done

cd "$START_FOLDER"