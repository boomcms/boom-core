<?php

// Sometimes we want the Sledge classes to be available but don't want to load the Sledge.
// e.g. in the sledge3 import app.
// In these cases we define SKIP_SLEDGE_INIT.
// Although this code should probably be moved to a Sledge::init() function which is only called when necessary, but this way will do for now.
if ( ! defined('SKIP_SLEDGE_INIT'))
{
	/*
	* Let the Sledge take charge of exceptions.
	*/
	set_exception_handler(array('Sledge', 'exception_handler'));

	/**
	 * Log remotely.
	 */
	Kohana::$log->attach(
		new Log_Remote(
			'https://status.thisishoop.com/api/logs/new', 
			array(
				'hostname'	=>	$_SERVER['SERVER_NAME'],
				'api_key'	=>	'gChlK4F5BwP3NY21IgJc-WlYY3uFwayguKNMI96dJ-pJfNHj6HtaegA7ZRA38E',
				'key'		=>	'nIPOjkcaCGNdml3qjvtE-CQs3IZKffx7DP1JkJYrk-52qSXQYbvuGYjmDT63bn',
			)
		)
	);

	/* Include the userguide module if this isn't a live instance.
	* @link http://kohanaframework.org/3.2/guide/userguide
	*/
	if (Kohana::$environment === Kohana::DEVELOPMENT)
	{
		Kohana::modules(array_merge(Kohana::modules(), array(MODPATH . 'guide')));
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

	/**
	* Any URIs not caught by a previous route will be caught by this.
	* This route directs all requests to the Controller_Site::action_index() controller.
	* The requested page is retrieved from the db and can be accessed from $this->request->param( 'page' ) from within the controller.
	* If the URI isn't matched then a page with URI 'error/404' is used.
	*/
	Route::set('catchall', function($uri)
		{
			$result = Sledge::process_uri($uri);

			if ($result !== NULL)
			{
				// If the URI used to access the page wasn't the primary URI then redirect them, otherwise send them to the site controller
				$page_uri = $result['page_uri'];

				if ($page_uri->primary_uri == FALSE)
				{
					return array(
						'controller' 	=> 'site',
						'action'     	=> 'redirect',
						'page'			=> $page_uri->page,
						'options'		=> $result['options'],
					);
				}
				else
				{
					return array(
						'controller' 	=> 'site',
						'action'     	=> 'index',
						'page'			=> $page_uri->page,
						'options'		=> $result['options'],
					);
				}
			}
		}
	);

	/**
	* Register the default Sledge plugins.
	*/
	Plugin::register(array(
		'archive'		=>	'plugin/archive',
		'child_pages'	=>	'plugin/page/children_html',
		'createsend'	=>	'plugin/createsend/signup',
	 	'twitter'		=>	'plugin/twitter/feed',
		'search'		=>	'plugin/search',
		'leftnav'		=>	'plugin/tree/leftnav',
		'mainnav'		=>	'plugin/tree/mainnav',
	));
}
