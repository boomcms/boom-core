<?php

// Sometimes we want the Sledge classes to be available but don't want to load the Sledge.
// e.g. in the sledge3 import app.
// In these cases we define SKIP_SLEDGE_INIT.
// Although this code should probably be moved to a Sledge::init() function which is only called when necessary, but this way will do for now.
if ( ! defined('SKIP_SLEDGE_INIT'))
{
	/* Include the userguide module if this isn't a live instance.
	* @link http://kohanaframework.org/3.2/guide/userguide
	*/
	if (Kohana::$environment === Kohana::DEVELOPMENT)
	{
		Kohana::modules(array_merge(Kohana::modules(), array(MODPATH . 'guide')));
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
			'controller' => 'cms_auth'
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
	Route::set('page_settings', 'cms/page/settings/<action>/<id>' )
		->defaults(array(
			'controller' => 'cms_page_settings',
		));

	/**
	* Defines the route for /cms page uris pages..
	*
	*/
	Route::set('page_uris', 'cms/page/uri/<action>/<id>' )
		->defaults(array(
			'controller' => 'cms_page_uri',
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

	Route::set('child_page_plugin', 'plugin/page/children.<action>')
		->defaults(array(
			'controller'	=>	'plugin_page_children'
		));

	/**
	 * Route for vanity URIs. Vanity URIs are the page ID base-36 encoded and prefixed with an underscore.
	 * Vanity URIs redirect to the page's primary URI
	 */
	Route::set('vanity', '_<uri>', array(
			'uri'	=>	'[a-zA-Z0-9]',
		))
		->filter(function(Route $route, $params, Request $request)
			{
				// Turn the vanity URI into a page ID.
				$page_id = base_convert($params['uri'], 36, 10);

				HTTP::redirect(ORM::factory('Page', $page_id)->url(), 302);
				return FALSE;
			}
		);

	/**
	* Any URIs not caught by a previous route will be caught by this.
	* This route directs all requests to the Controller_Site::action_index() controller.
	* The requested page is retrieved from the db and can be accessed from $this->request->param( 'page' ) from within the controller.
	* If the URI isn't matched then a page with URI 'error/404' is used.
	*
	* This is starting to become quite unwieldy, some rewriting may be required.
	*/
	Route::set('sledge', '<uri>(.<action>)', array(
			'uri'	=>	'.*?',
		))
		->defaults(array(
			'controller'	=>	'page',
			'action'		=>	'html',
		))
		->filter(function(Route $route, $params, Request $request)
			{
				$page_uri = ORM::factory('Page_URI', array('uri' => $params['uri']));

				if ($page_uri->loaded() AND $page_uri->page->loaded())
				{
					if ($page_uri->primary_uri == FALSE AND $page_uri->redirect == TRUE)
					{
						HTTP::redirect($page->url(), 301);
						return FALSE;
					}

					$params['page'] = $page_uri->page;
					return $params;	
				}

				return FALSE;
			}
		);
}