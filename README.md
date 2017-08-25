<p align="center">
    <a href="https://www.boomcms.net" target="_blank">
        <img src="https://www.boomcms.net/vendor/boomcms/boom-core/img/logo.png" alt="BoomCMS logo">
    </a>
</p>

<p align="center">
    <a href="https://travis-ci.org/boomcms/boom-core"><img src="https://travis-ci.org/boomcms/boom-core.svg?branch=master" alt="Build Status"></a>
    <a href="https://styleci.io/repos/25917795"><img src="https://styleci.io/repos/25917795/shield" alt="StyleCI"></a>
</p>

## BoomCMS

BoomCMS is a content management system which is designed to be easy for content editors and developers alike.

This is the core code for BoomCMS which is designed to be integrated into a laravel application.

To create a new BoomCMS app follow the installation instructions in the [BoomCMS app](https://github.com/boomcms/boomcms).

---

## Development

### Running tests

First install PHP dependencies with composer:

```
    composer install
```

You can then run tests with `phpunit`. However, some tests require access to a database. For these tests to pass you may need to specify database credentials:

```
    DB_PASSWORD=<password> DB_USERNAME=<username> DB_DATABASE=<database> DB_HOST=<host> DB_DRIVER=<driver> phpunit
```

The default values for these variables are:

 * DB_PASSWORD: empty
 * DB_USERNAME: root
 * DB_DATABASE: boomcms_tests
 * DB_HOST: 127.0.0.1
 * DB_DRIVER: mysql
 
 You won't need to specify these options on the command line if the default values work for you.
 

### Building JavaScript and CSS files

First install dependencies via NPM and bower:

```
    npm install
    bower install
```

Then create minified JavaScript and CSS files with grunt:

```
    grunt dist
```

The default grunt task is `grunt watch` which will build the JavaScript and CSS files when changes are made.
