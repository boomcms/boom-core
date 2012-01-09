<?php

/**
* Check that other modules required by Sledge (auth, database etc.) are loaded.
*
*/
$dependencies = array( 'auth', 'cache', 'database', 'postgresql', 'orm', 'pagination' );

foreach( $dependencies as $dep )
{
	if (!array_key_exists( $dep, Kohana::modules() ))
	{
		throw new Sledge_Exception( "Required module '" . $dep . "' not loaded" );
	}
}

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
* Route for displaying assets
*
*/
Route::set('asset', 'asset/<id>(/<with>(/<height>(/<quailty>(/<notsure>))))')
	->defaults(array(
		'controller' => 'asset',
		'action'	 => 'index'
	));
	
/**
* Defines the route for /cms pages.
*
*/
Route::set('cms', '<directory>/(<controller>(/<action>(/<id>)))',
	array(
		'directory'	=> 'cms'
	))
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));
	
/**
* Route for RSS feeds.
*/
Route::set('feeds', '(<uri>)\.<action>',
	array(
		'action' => 'rss'
	))
  ->defaults(array(
    'controller' => 'feeds',
  ));
		
Route::set('home', 'home/')
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));
?>
