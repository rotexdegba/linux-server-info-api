# Server Info API Documentation

## Installation Instructions
* `cp phinx.yml.dist phinx.yml`
* Make sure **./logs**, **./storage/sqlite** & **./tmp/session** are writable by your webserver
* Run composer install
* Run ./vendor/bin/phinx --verbose migrate -e production

