# PHPCrawler for getting all file size

A project to parse a website and get the total file size, within the site.

## Requirements

- PHP > 5.4
- Mysql

## Setup
- Setup the required dependencies using composer:
```ShellSession
$ composer install
```
- Create a new database, in the MySQL server, and run `crawler.sql` in the newly created database,
 so as to setup the required tables.
- Update the database settings in the `config.ini` file.

- In the `url` section, set the URL in confiq.ini and also pass the URL using CLI
    eg:Goto your project folder and run the :"php crawler.php https://www.costrategix.com/"

## How to run

```ShellSession
$ php crawler.php
```

If you have Any issue, please call me any time midhun : 09846534089.
