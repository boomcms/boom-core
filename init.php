<?php

Boom_Exception_Handler::set_exception_handler();

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
			$page = \Boom\Page\Factory::byId($page_id);
			$redirect_to = $page->url();

			header('Location: '.$redirect_to, null, 302);
			exit;
		}
	);

// Route for a list of child pages in JSON
Route::set('child_pages', 'page/children(.<action>)', array(
		'action'	=>	'json'
	))
	->defaults(array(
		'controller'	=>	'page_children'
	));

/**
 * Checks for a page with the matching URL in the CMS database.
 *
 */
Route::set('boom', '<location>(.<format>)', array(
		'location'	=>	'^(?!cms).*?',
	))
	->defaults(array(
		'action' => 'show'
	))
	->filter(array('Boom', 'process_uri'));

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
Route::set('cms', '<directory>(/<controller>(/<action>(/<id>)))',
	array(
		'directory'	=> 'cms',
		'id' => '\d+',
	))
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
	));

Route::set('chunks', 'cms/chunk/<controller>/<action>/<page_id>')
	->defaults(array(
		'directory'	=>	'cms_chunk'
	));

// Route for add / removing tags from assets and pages.
Route::set('tags', 'cms/tags/<controller>/<action>/<id>', array(
		'controller'	=>	'asset|page',
	))
	->defaults(array(
		'directory'		=>	'cms_tags',
	));

/**
 * Route for editing page settings where there are different classes depending on request method
 *
 * The request is handled by different controllers depending on the request method.
 * For POST requests we use a controller which saves the page settings.
 * For all other requests we use a controller which shows the relevant settings.
 */
Route::set('page_settings2', 'cms/page/<directory>/<action>(/<id>)')
	->filter(function(Route $route, $params, Request $request)
		{
			// Set the directory correctly
			$params['directory'] = 'Cms_Page_'.$params['directory'];

			// Set the controller based on request method.
			$params['controller'] = ($request->method() === Request::POST)? 'Save' : 'View';

			// Return the request params.
			return $params;
		}
	);