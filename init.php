<?php

Boom::instance()
    ->setCacheDir(APPPATH . 'cache');

Boom\Exception\Handler\Handler::setExceptionHandler();

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

// Route for displaying assets
Route::set('asset', 'asset/<action>/<id>(.<extension>)(/<width>(/<height>(/<quality>(/<crop>))))', array(
            'id' => '\d+',
            'width' => '\d+',
            'height' => '\d+',
            'quality' => '\d+',
            'crop' => '\d',
            'action' => 'view|thumb|download|embed|crop'
        ))
	->defaults(array(
                'controller' => 'asset',
		'action'	=> 'view',
		'quality'	=>	85,
	));

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
			$params['asset_ids'] = array_unique($request->query('asset'));
			$params['action'] = (count($params['asset_ids']) == 1) ? 'single' : 'multiple';

			return $params;
		}
	);

/**
* Defines a shortcut for /cms/account pages (login, logout, etc.) so that account doesn't have to be used in the URL.
*
*/
Route::set('auth', 'cms/logout')
	->defaults(array(
		'controller' => 'Cms_Auth_Logout',
		'action' => 'index',
	));

Route::set('recover', 'cms/recover')
	->defaults(array(
		'controller' => 'Cms_Auth_Recover',
		'action' => 'show_form',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			if ($request->method() === Request::POST && ! $request->query('token'))
			{
				$params['action'] = 'create_token';
				return $params;
			}
			else if ($request->query('token'))
			{
				$params['action'] = 'set_password';
				return $params;
			}
		}
	);

Route::set('profile', 'cms/profile')
	->defaults(array(
		'controller' => 'Cms_Profile',
		'action' => 'view',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			if ($request->method() == Request::POST)
			{
				$params['action'] = 'save';
				return $params;
			}
		}
	);

Route::set('people-manager', 'cms/people')
	->defaults(array(
		'controller' => 'Cms_PeopleManager',
		'action' => 'index',
	));

Route::set('people-edit', 'cms/<controller>(/<action>(/<id>))', array(
		'controller' => 'person|group',
		'id' => '\d+'
	))
	->defaults(array(
		'action' => 'index',
	))
	->filter(function(Route $route, $params, Request $request) {
		$params['controller'] = 'Cms_'.ucfirst($params['controller'])."_";
		$params['controller'] .= ($request->method() == Request::GET)? 'View' : 'Save';

		return $params;
	});

Route::set('login', 'cms/login')
	->defaults(array(
		'controller' => 'Cms_Auth_Login',
		'action' => 'begin',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			if ($request->method() == Request::POST) {
				$params['action'] = 'process';
			}

			return $params;
		});

Route::set('cms', '<directory>(/<controller>(/<action>(/<id>)))',
	array(
		'directory'	=> 'cms',
		'id' => '\d+',
	))
	->defaults(array(
		'controller' => 'default',
		'action'     => 'index',
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