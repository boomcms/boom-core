<?php

// Has the runtime database configuration been created?
// Sledge apps ship with no database configuration.
// If this sledge is newly cloned then we need to set the database connection information.
// This checks for the sledge property in the database config which is set by our config setup script.
if (!Kohana::$config->load( 'database.default.connection.sledge' ))
{
	echo View::factory( 'setup/config/database' );
	exit;			
}

// Has the Sledge environment config file been created?
if (!Kohana::$config->load( 'sledge.environment' ))
{
	echo View::factory( 'setup/config/sledge' );
	exit;			
}		

// Check that the datbase exists, if not we offer to create it.
try{
	$db = Database::instance();
	$db->connect();
}
catch (Database_Exception $e)
{
	// Is it a database not existing error?
	if (preg_match( "/Unknown database '(.*)'/", $e->getMessage(), $matches ))
	{
		$v = View::factory( 'setup/database/create' );
		$v->dbname = $matches[1];
		echo $v;
		exit;
	}
	
	// It's some other error which we don't worry about here.
	throw $e;
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
