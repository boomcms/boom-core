<?php

/*
* Let the Sledge take charge of exceptions.
*/
set_exception_handler( array( 'Sledge_Exception', 'handler' ) );

/* Include the userguide module if this isn't a live instance.
* @link http://kohanaframework.org/3.2/guide/userguide
*/
if (Kohana::$environment != Kohana::PRODUCTION)
{
	Kohana::modules( array_merge( Kohana::modules(), array(MODPATH . 'guide') ) );
}

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
Route::set('asset', 'asset/(<action>/)<id>(/<width>(/<height>(/<quality>(/<crop>))))')
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
	
/**
 * Default route.
 */
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'site',
		'action'     => 'index',
	));
	
?>
