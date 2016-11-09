<p align="center">
    <a href="http://www.boomcms.net" target="_blank">
        <img src="http://www.boomcms.net/img/logo.png" alt="BoomCMS logo">
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

To integrate BoomCMS with an existing Laravel application add it to your composer.json file:

```json
    "boomcms/boom-core": ">=3.0"
```

Then add the service provider to the provider's section in your application's config/app.php file:

```
'BoomCMS\ServiceProviders\BoomCMSServiceProvider'
```
