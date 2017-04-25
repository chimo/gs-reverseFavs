ReverseFavs
================

Tiny plugin to display a user's notices that were favored by others

## Installation

Make sure the files are in a folder called `ReverseFavs` if they're not already  
Put the folder in your `/local/plugins/` directory (create it if it doesn't exist)  
Tell `/config.php` to use it with: `addPlugin('ReverseFavs');`  
A `Reverse Favs` link should appear in the left-nav of your profile page.

Assuming you're in the root directory of your GNU social installation, commands for
the above instructions may look like:

    mkdir -p local/plugins
    git clone https://github.com/chimo/gs-reverseFavs.git local/plugins/ReverseFavs
    echo "addPlugin('ReverseFavs');" >> config.php

Note: depending on the permissions on your GNU social directories, you might
need to prefix the commands above with `sudo`. For example:
`sudo -u www-data mkdir -p local/plugins`, etc.

## Update

To update the plugin, run `git pull` in the `local/plugins/ReverseFavs` directory.

