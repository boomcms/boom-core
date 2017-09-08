# BoomCMS Themes

This directory contains the classes for processing BoomCMS themes.

A theme is a package which may contain views, migrations, and public files.
To understand themes it is therefore important to understand
[Laravel package development](https://laravel.com/docs/5.4/packages).

In practice a theme is simply a sub-directory within the application's `storage/boomcms/themes` directory.
An instance of a [BoomCMS application](https://www.github.com/boomcms/boomcms) may contain any number of themes.

It is important that themes are not installed in the vendor directory as packages would usually be.
This prevents every installed composer package being searched for BoomCMS templates and public files.
The storage directory is used to permit the eventual development of a theme store
with an admin interface to search and install themes, should that be required.

As a theme merely needs to exist as a subdirectory in `storage/boomcms/themes`;
BoomCMS is agnostic to how a theme is installed.
While themes will most commonly be installed via composer packages
(e.g. defined in the `storage/boomcms/addons.json` file),
they could also be installed by extracting a .zip into the `storage/boomcms/themes` directory, or copying the directory into place.

Installation via composer is described in more detail below.

## Directory structure

The themes directory may look something like this:
 
- storage/boomcms/themes/
  - theme1/
    - migrations/
    - public/
    - src/
      - config/
        - boomcms.php
      - views/
        - boomcms/
        - chunks/
        - templates/
          - template1.php
          - template2.php
    - init.php

No directories or files are required,
although a theme which does not include anything may be a little unhelpful.

By following a common directory structure themes do not need their own service provider
to load views or publish public files and migrations.
This makes theme development quicker and simpler than normal Laravel Package development.

## ServiceProvider

The theme service provider is responsible for registering the directories of installed themes as sources of views.

This makes theme views available within the application.

  - The main views directory (`src/views` within the theme's directory) is registered with the theme name as the namespace.
This ensures that multiple themes can define views with the same filename without causing conflicts.

  - Views in the `src/views/boomcms` are registered to the boomcms namespace
This allows themes to override boomcms views.
One use-case for this would be to replace the default BoomCMS login page with a branded login page.

  - Views in the `src/views/chunks` directory are registered to the boomcms.chunks namespace.
This namespace is used by the chunk provider to find chunk views.

### Templates
Templates are views which are located in the `src/views/templates` directory of a theme.
Following installation templates become available for use with a page via the BoomCMS page settings interface.

## Console commands
BoomCMS defines two artisan commands for working with themes.

- `installTemplates`. This will search all theme directories for files in `src/views/templates` and add database records in the templates table for any which haven't been seen before.
- `publish`. This will copy the public directory from all themes to the application's public directory.

## Installing themes via composer

The [Composer merge plugin](https://github.com/wikimedia/composer-merge-plugin) is used
 to permit the installation of themes via composer.

Themes may be added to the application's `storage/boomcms/addons.json` file.
This file will be merged with the main `composer.json` file when `composer update` is run from the root diretory of the application.

This allows website administrators to define themes
while still being able to keep a [BoomCMS application](https://www.github.com/boomcms/boomcms) up to date with upstream changes to the composer.json file.

## init.php

Themes may contain an `init.php` file which will be included by the Theme Service Provider.

Since a theme may register PHP classes to be autoloaded via its `composer.json` file,
an `init.php` may considerablly extend the functionality of a BoomCMS application.

Controllers, event listeners, Mailables, and other classes may be distributed as part of the theme.
Routes and event listeners can then be defined in the `init.php` file to integrate these classes with the application.
