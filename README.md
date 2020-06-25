# Note

I'm done with this. If someone wants to maintain it send me a note and I'll transfer the repo. Or just fork it, or whatever.

ReverseFavs
================

Tiny plugin to display a user's notices that were favored by others

## Instructions

Make sure the files are in a folder called `ReverseFavs` if they're not already  
Put the folder in your `/local/plugins/` directory (create it if it doesn't exist)  
Tell `/config.php` to use it with: `addPlugin('ReverseFavs');`  
A `Reverse Favs` link should appear in the left-nav of your profile page.

Assuming you're in the root directory of your GNU social installation, commands for
the above instructions may look like:

    mkdir -p local/plugins
    cd local/plugins
    git clone https://github.com/chimo/gs-reverseFavs.git ReverseFavs
    echo "addPlugin('ReverseFavs');" >> ../../config.php

