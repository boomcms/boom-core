<?php

// Route for displaying assets
Route::set('asset', 'asset/<action>/<id>(/<width>(/<height>(/<quality>(/<crop>))))')
	->defaults(array(
		'action'	=> 'view',
		'quality'	=>	85,
	))
	->filter(function(Route $route, $params, Request $request)
		{
			// Try and get the asset from the database.
			$asset = new Model_Asset($params['id']);

			// Does the asset exist?
			if ( ! ($asset->loaded() AND $asset->exists()))
			{
				return FALSE;
			}

			// Put the asset in the request params.
			$params['asset'] = $asset;

			// Set the controller depending on the asset type.
			$params['controller'] = 'Asset_'.ucfirst($asset->type());

			// Return the new request params.
			return $params;
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
			$redirect_to = ORM::factory('Page', $page_id)->url();

			header('Location: '.$redirect_to, NULL, 302);
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
		'location'	=>	'.*?',
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
 * Route for uploading assets.
 *
 * Each stage of the asset upload process is done via the URL /cms/assets/upload
 * But the method varies depending on which stage of the upload process we're at (selecting files, recieving files, etc.)
 *
 * In future we may also use different classes to get files from different sources (such as Dropbox) in addition to the user's file system.
 */
Route::set('asset_upload', 'cms/assets/upload')
	->defaults(array(
		'controller'	=>	'cms_assets_upload'
	))
	->filter(function(Route $route, $params, Request $request)
		{
			if ($request->method() === Request::GET)
			{
				// For a GET request we display a form to select which files to upload.
				$params['action'] = 'begin';
			}
			else
			{
				if ($request->post('asset_id'))
				{
					$params['action'] = 'replace';
				}
				else
				{
					// A POST request with files - process the upload.
					$params['action'] = 'process';
				}
			}

			return $params;
		}
	);

// Route for downloading assets.
Route::set('asset_download', 'cms/assets/download')
	->defaults(array(
		'controller'	=>	'cms_assets_download'
	))
	->filter(function(Route $route, $params, Request $request)
		{
			$params['asset_ids'] = array_unique(explode(",", $request->query('assets')));
			$params['action'] = (count($params['asset_ids']) == 1) ? 'single' : 'multiple';

			return $params;
		}
	);

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

// Route for editing page settings which are handled by a single class (e.g. tags and urls)
Route::set('page_settings1', 'cms/page/<controller>/<action>/<id>', array(
		'controller'	=>	'urls',
	))
	->defaults(array(
		'directory' => 'cms_page',
	));

/**
 * Route for editing page settings where there are different classes depending on request method
 *
 * The request is handled by different controllers depending on the request method.
 * For POST requests we use a controller which saves the page settings.
 * For all other requests we use a controller which shows the relevant settings.
 */
Route::set('page_settings2', 'cms/page/<directory>/<action>/<id>')
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
