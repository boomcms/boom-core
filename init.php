<?php

// Route for displaying assets
Route::set('asset', 'asset/<action>/<id>(.<extension>)(/<width>(/<height>(/<quality>(/<crop>))))')
	->defaults(array(
		'action'	=> 'view',
		'quality'	=>	85,
	))
	->filter(function(Route $route, $params, Request $request)
		{
			$asset = \Boom\Finder\Asset::byId($params['id']);

			// Does the asset exist?
			if ( ! $asset->loaded() || ( Kohana::$environment != Kohana::DEVELOPMENT && ! $asset->exists()))
			{
				return false;
			}

			if ($params['action'] == 'view' && ! $asset instanceof \Boom\Asset\Type\Image && substr($request->headers('accept'), 0, 5) == 'image')
			{
				// An image response has been requested, but this asset isn't an image.
				// Show the asset thumbnail instead.
				$params['action'] = 'thumb';
			}

			$params['asset'] = $asset;
			$params['controller'] = 'Asset_'. $asset->getType();
			return $params;
		}
	);

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