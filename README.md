# Server Info API Documentation

## Installation Instructions
* On centos make sure php-posix is installed
    - sudo yum install php-posix
    - If running apache webserver:
        - sudo service httpd restart
* Make sure **./logs**, **./storage/sqlite** & **./tmp/session** are writable by your webserver
* Run composer install
* Run composer run-script generate-config-files
    - Edit the config files
* Run ./vendor/bin/phinx --verbose migrate -e production
* Test by running the dev server
    - php -S 0.0.0.0:8888 -t public

# API Documentation

* Documentation for the API end points can be found [here](./docs/index.md)
