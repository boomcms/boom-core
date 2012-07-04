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
Route::set('feeds', '<url>/<action>',
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
Route::set('asset', 'asset/<action>/<id>(/<width>(/<height>(/<quality>(/<crop>))))')
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
* Defines the route for /cms pages.
*
*/
Route::set('cms', '<directory>(/<controller>(/<action>(/<id>)))',
	array(
		'directory'	=> 'cms'
	))
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));

/**
* Defines the route for plugin controllers.
*/
Route::set('plugin', '<directory>/<controller>(/<action>)',
	array(
		'directory'	=> 'plugin'
	))
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));
	
/**
* Route for app specific controllers.
*/
Route::set('app', 'app/<controller>(/<action>(/<id>))')
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));
	
if (Kohana::$environment !== Kohana::PRODUCTION)
{
	Route::set('setup', '<controller>/<action>',
		array(
			'controller'	=>	'setup',
			'action'		=>	'import',
		));
}	

/**
* Any URIs not caught by a previous route will be caught by this.
* This route directs all requests to the Controller_Site::action_index() controller.
* The requested page is retrieved from the db and can be accessed from $this->request->param( 'page' ) from within the controller.
* If the URI isn't matched then a page with URI 'error/404' is used.
*/
Route::set('catchall', function($uri)
	{
		$result = Sledge::process_uri( $uri );
		
		if ($result !== NULL)
		{
			return array(
				'controller' 	=> 'site',
				'action'     	=> 'index',
				'page'			=> ORM::factory( 'page', $result['page_id'] ),
				'options'		=> $result['options'],
			);
		}
		else
		{
			return array(
				'controller' 	=> 'site',
				'action'     	=> 'index',
				'page'			=> ORM::factory( 'page' )->join( 'page_uri' )->on( 'page.id', '=', 'page_uri.page_id' )->where( 'uri', '=', 'error/404' )->find(),
			);
		}			
	}
);

/**
* Add core items to the menu.
*/
if (Auth::instance()->logged_in())
{
	Sledge_Menu::add( '/cms/assets', 'Assets', 'manage_assets' );
	Sledge_Menu::add( '/cms/people', 'People', 'manage_people' );
	Sledge_Menu::add( '/cms/templates', 'Templates', 'manage_templates' );
}

/**
* Register the default Sledge plugins.
*/
Plugin::register( array( 
	'archive'		=>	'plugin/archive',
	'child_pages'	=>	'plugin/page/children',
	'createsend'	=>	'plugin/createsend/signup',
 	'twitter'		=>	'plugin/twitter/feed',
	'search'		=>	'plugin/search',
	'leftnav'		=>	'plugin/tree/leftav',
));

?>
