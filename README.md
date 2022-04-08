# Semantle - Dutch version

This GitHub repository hosts the code used in the Dutch version of Semantle game at [https://semantle.be](https://semantle.be)

This game is a translation from the [original Semantle game](https://semantle.novalis.org/) by [David Turner](https://gitlab.com/novalis_dt/semantle). 

# PHP server fork

The initial game uses a Flask based Python webserver to host the game (semantle.py) but not many hosting providers allow you to run daemonized Python code.

Therefore I've ported the server part to a simple PHP version.

The PHP version is a drop-in replacement for David's semantle.py, all you need is an Apache webserver with libapache2-mod-php and php-sqlite3 installed.

## Installation

- Copy .htaccess, rest.php and top1k.php to your webroot
- Copy word2vec.db (see David's GitLab page on how to generate) to a folder outside your webroot
- Make sure your webserver user has write access to both the folder as word2vec.db itself
- Edit rest.php and top1k.php with word2vec.db's (relative) location

top1k.php is unfortunately translated to Dutch in this repo but you'll get the idea.
