<?php


/**
* Route for RSS feeds.
*/
Route::set('feeds', '<action>/<uri>',
	array(
		'action' => 'rss'
	))
   ->defaults(array(
     'controller' => 'feeds',
   ));

/**
* Defines a shortcut for /cms/account pages (login, logout, etc.) so that account doesn't have to be used in the URL.
*
*/
Route::set('auth', 'cms/<action>',
	array(
		'action' => '(login|logout)'
	))
	->defaults(array(
		'controller' => 'cms_account'
	));
	

/**
* Route for displaying / saving assets
*
*/
Route::set('asset', 'asset/(<action>/)<id>(/<with>(/<height>(/<quailty>(/<notsure>))))')
	->defaults(array(
		'controller' => 'asset',
		'action'	 => 'index'
	));

/**
* Defines the route for /cms page settings pages..
*
*/
Route::set('page_settings', 'cms/page/settings/<tab>/<id>' )
	->defaults(array(
		'controller' => 'cms_page',
		'action'     => 'settings',
	));	
	
/**
* Tag manager route.
*
*/
Route::set('tags', 'cms/tags(/<id>)',
	array(
		'id'	=>	'\d+',
	))
	->defaults(array(
		'action'	=> 'index',
		'directory'	=> 'cms',
		'controller'=> 'tags',
	));
	
/**
* Defines the route for /cms pages.
*
*/
Route::set('cms', '<directory>/<controller>/<action>(/<id>)',
	array(
		'directory'	=> 'cms'
	))
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));

/**
* Defines the route for /sledge pages.
*
*/
Route::set('sledge', '<directory>/(<controller>(/<action>(/<id>)))',
	array(
		'directory'	=> 'sledge'
	))
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));
	
?>
