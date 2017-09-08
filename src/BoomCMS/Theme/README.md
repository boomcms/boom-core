# BoomCMS Themes

This directory contains the classes for working with BoomCMS themes.

A theme is a package which may contain views, migrations, and public files.
To understand themes it is important therefore to understand [Laravel package development](https://laravel.com/docs/5.4/packages)

In practice a theme is simply a sub-directory within the storage/boomcms/themes directory/

It is important that themes are not installed to the vendor directory as packages would usually be.
This prevents every installed composer package being searched for BoomCMS templates and public files.
The storage directory is used to permit the future development of a theme store with interface to install themes/

BoomCMS is therefore agnostic to how a theme is installed.
While themes will most commonly be installed via composer packages (e.g. defined in the storage/boomcms/addons.json file),
they could also be installed by extracting a .zip into the storage/boomcms/themes directory, or copying the directory into place.

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

No directories are required, although a theme which defines no templates may be a little unhelpful

By following a common directory structure themes do not need their own service provider
to include views or publish public files / migrations.

## ServiceProvider

The theme service provider is responsible for registering the theme directories as a source of views.

This makes theme views available within the application.

The main views directory (src/views within the theme's directory) is registered with the theme name as the namespace.
This ensures that multiple themes can define views with the same filename.

Views in the src/views/boomcms are registered to the boomcms namespace
This allows themes to override boomcms views.
One use-case for this would be to replace the default BoomCMS login page with a branded login page.

Views in the views/chunks directory are registered to the boomcms.chunks namespace.
This namespace is used by the chunk provider to find chunk views.

## Console commands

BoomCMS defines two artisan commands for working with themes.

- installTemplates. This will search all theme directories for files in src/views/templates and add database records in the templates table for any which haven't been seen before.
- publish. This will copy the public directory from all themes to the application's public directory.

## init.php

Themes may contain an init.php file which will be included by the Theme service provider.
This may be used to define routes for additional functionality, or event listeners.
