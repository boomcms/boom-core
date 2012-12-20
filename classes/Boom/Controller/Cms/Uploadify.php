<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Uploadify controller.
* This is used to receive file uploads from uploadify.
* This has to be kept separate as uploadify can't authenticate via cookies.
*
* @package Boom
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Boom_Controller_Cms_Uploadify extends Kohana_Controller
{
	/**
	* Asset upload form.
	*
	*/
	public function action_form()
	{
			// Encypt the session ID and user ID to create an upload token.
			$encrypt = Encrypt::instance();
			$token = $encrypt->encode(microtime( TRUE) . " " . Session::instance()->id() . " " . Auth::instance()->get_user()->pk());

			$v = View::factory('boom/assets/upload_assets');
			$v->token = $token;

			$this->response->body($v);
	}

	/**
	* Asset upload controller.
	* Backend to the uploadify for uploading multiple assets at once.
	*
	*/
	public function action_asset()
	{
		if ( ! empty($_FILES))
		{
			// Normalise the $_FILES array, which varies according to upload method.
			// Result should be an array of files in $newfiles.
			if ( array_key_exists( 's-assets-upload-files', $_FILES ) ) {

				if ( is_array($_FILES['s-assets-upload-files']['tmp_name']) ) {
					// HTTP POST, multiple files
					$newfiles = array();
					    foreach($_FILES as $fieldname => $fieldvalue)
					        foreach($fieldvalue as $paramname => $paramvalue)
					            foreach((array)$paramvalue as $index => $value)
					                $newfiles[$fieldname][$index][$paramname] = $value;
				} else {
					// HTTP POST, single file
					$newfiles = $_FILES;
				}

				$newfiles = $newfiles['s-assets-upload-files'];

			} else {
				// Uploadify POST
				$newfiles = $_FILES;
			}


			/**
			* Validate based on the upload token.
			*/
			// Get the token data.
			$token = $this->request->post('upload_token');

			// Decrypt it.
			$encrypt = Encrypt::instance();
			$data = $encrypt->decode($token);

			// Extract the session ID and person ID.
			list($time, $session_id, $person_id) = explode(" ", $data);

			// No person ID? Fail.
			if ( ! @$person_id)
			{
				throw new HTTP_Exception_401;
			}

			// Process the files.
			foreach ($newfiles as $file)
			{
				// if (in_array($file['type'], Boom_Asset::$allowed_types))
				// {
					// Are we replacing an existing asset?
					$asset_id = $this->request->post('asset_id');

					$asset = new Model_Asset($asset_id);
					$asset->filename = $file['name'];

					if ($asset_id === NULL)
					{
						// Get the file's name without the extension.
						// This is what we'll call the asset.
						preg_match('/(.*)\.(.*)/', $file['name'], $matches);
						$title = $matches[1];

						$asset->title = $title;
					}

					$asset->filesize = $file['size'];
					$asset->uploaded_by = $this->request->post('person');
					$asset->type = Boom_Asset::type_from_mime(File::mime($file['tmp_name']));
					$asset->visible_from = $_SERVER['REQUEST_TIME'];

					if ($asset->type == Boom_Asset::IMAGE)
					{
						// Set the dimensions of the image.
						list($width, $height) = getimagesize($file['tmp_name']);
						$asset->width = $width;
						$asset->height = $height;
					}

					$asset->create();

					try
					{
						if ($asset_id !== NULL)
						{
							// We're replacing an existing asset so move the old file to prevent overwriting it.
							// Backup asset files with the curretn version ID (the version at which they were replaced).
							// This is so that we can easily get the version details from the db to report who replaced the asset.
							@rename(ASSETPATH . $asset->id, ASSETPATH . $asset->id . "." . $asset->version->id . ".bak");
						}

						Upload::save($file, $asset->id, ASSETPATH);
					}
					catch (Exception $e)
					{
						// If we couldn't save the file then delete the record in the database.
						if ($asset_id !== NULL)
						{
							$asset->delete();
						}
						else
						{
							// Move the old file back.
							if ( ! file_exists(ASSETPATH . $asset->id))
							{
								rename(ASSETPATH . $asset->id . "." . $asset->version->id . ".bak", ASSETPATH . $asset->id);
							}
						}

						throw $e;
					}

					/**
					 * Delete any resized cached files.
					 * When an asset is resized a cache file is created in the format <asset_id>_<width>_<height>.cache to prevent having to resize the image again.
					 * We don't want to carry on serving one of these files after an asset has been replaced.
					 *
					 * We could get the dimensions out of the filenames and recreate the cache files here but that could be quite slow if there's a lot of different dimensions used across the site.
					 * Probably best to do it individually when required.
					 *
					 * @todo Cache files should sit in their own cache/ directory to avoid having to search a big asset directory for cache files.
					 */
					foreach (glob(ASSETPATH . $asset_id . "_*.cache") as $cached)
					{
						unlink($cached);
					}

					// Cache the asset ID. This allows us to later retrieve the IDs of all the assets uploaded.
					$cache_key = 'asset-upload-ids:' . $this->request->post('upload_token');
					$asset_ids = (array) Kohana::cache($cache_key);
					$asset_ids[] = $asset->pk();
					Kohana::cache($cache_key, $asset_ids);
				// }
			}
		}

	}

	/**
	* Get the rids of newly uploaded assets.
	*
	*/
	public function action_get_rids()
	{
		if ($this->request->post('upload_token') !== NULL)
		{
			// Return the IDs of all the assets uploaded in this 'batch'.
			$cache_key = 'asset-upload-ids:' . $this->request->post('upload_token');
			$asset_ids = Kohana::cache($cache_key);
			Kohana::cache($cache_key, array(), -1);

			if ( ! empty($asset_ids))
			{
				$this->response->body( json_encode( array('rids' => $asset_ids, 'errors' => array())));
			}
		}
	}

}
