## BoomCMS

BoomCMS is a content management system which is designed to be easy for content editors and developers alike.

## Installation
BoomCMS can be installed via Composer:

```Shell
composer create-project boomcms/boomcms --prefer-dist
```

You'll then want to setup your Apache virtual host:

```ApacheConf
<VirtualHost *:80>
	ServerName <your server name>
	DocumentRoot "<your install path>/public"
	<Directory "<your install path>/public">
		AllowOverride all
	</Directory>
</VirtualHost>
```

Then open up Boom in your browser to complete the installation process.

## Credits
BoomCMS is built on the [Laravel Framework](https://github.com/laravel/laravel).
WYSYWIG text editor is courtesty of [wysihtml](https://github.com/voog/wysihtml)
