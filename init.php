<?php

/* Include the userguide module if this isn't a live instance.
* @link http://kohanaframework.org/3.2/guide/userguide
*/
if (Kohana::$environment === Kohana::DEVELOPMENT)
{
	Kohana::modules(array_merge(Kohana::modules(), array(MODPATH.'guide')));
}

/**
* Route for displaying assets
*
*/
Route::set('asset', 'asset/<action>/<id>(/<width>(/<height>(/<quality>(/<crop>))))')
	->defaults(array(
		'controller' => 'asset',
		'action'	 => 'view'
	));

/**
* Any URIs not caught by a previous route will be caught by this.
* This route directs all requests to the Controller_Site::action_index() controller.
* The requested page is retrieved from the db and can be accessed from $this->request->param( 'page' ) from within the controller.
* If the URI isn't matched then a page with URI 'error/404' is used.
*
* This is starting to become quite unwieldy, some rewriting may be required.
*/
Route::set('sledge', '<location>(.<action>)', array(
		'location'	=>	'.*?',
	))
	->defaults(array(
		'controller'	=>	'page',
		'action'		=>	'html',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			$page_link = ORM::factory('Page_Link', array('location' => $params['location']));

			if ( ! $page_link->loaded())
			{
				return FALSE;
			}

			$page = $page_link->page;

			if ($page->loaded())
			{
				if ( ! $page_link->is_primary AND $page_link->redirect)
				{
					HTTP::redirect($page->link(), 301);
					return FALSE;
				}

				$params['page'] = $page;
				return $params;
			}

			return FALSE;
		}
	);

/**
 * Route for vanity URIs. Vanity URIs are the page ID base-36 encoded and prefixed with an underscore.
 * Vanity URIs redirect to the page's primary URI
 */
Route::set('vanity', '_<link>', array(
		'link'	=>	'[a-zA-Z0-9]',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			// Turn the vanity URI into a page ID.
			$page_id = base_convert($params['link'], 36, 10);

			HTTP::redirect(ORM::factory('Page', $page_id)->link(), 302);
			return FALSE;
		}
	);



/**
* Defines the route for /cms page settings pages..
*
*/
Route::set('page_settings', 'cms/page/settings/<action>/<id>' )
	->defaults(array(
		'controller' => 'cms_page_settings',
	));

/**
* Defines the route for /cms page links pages..
*
*/
Route::set('page_links', 'cms/page/link/<action>/<id>' )
	->defaults(array(
		'controller' => 'cms_page_link',
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


/**********************************
 *
 * Routes for logged in users only below
 *
 **********************************
 */
/**
* Defines the route for /cms pages.
*
*/
Route::set('cms', '<directory>/<controller>(/<action>(/<id>))',
	array(
		'directory'	=> 'cms'
	))
	->defaults(array(
		'action'     => 'index',
	));

Route::set('child_page_plugin', 'plugin/page/children.<action>')
	->defaults(array(
		'controller'	=>	'plugin_page_children'
	));

Route::set('chunks', 'cms/chunk/<controller>/<action>/<page>')
	->defaults(array(
		'directory'	=>	'cms_chunk'
	));