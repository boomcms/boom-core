<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset upload controller
 *
 *
 * @package	BoomCMS
 * @category	Assets
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Assets_Upload extends Controller_Cms_Assets
{
	/**
	 * Begin asset uploading.
	 *
	 * This function handles the first step of the asset upload process.
	 * A form is displayed allowing the user to select which files they wish to upload.
	 *
	 * An upload token is also created and added to the form.
	 * This is so that when it comes time to return the asset IDs of the uploaded assets
	 * If the user is uploading from two tabs
	 * We can identify only those assets which were returned in the current tag.
	 *
	 */
	public function action_begin()
	{
		$this->template = View::factory("$this->_view_directory/upload", array(
			'token'	=>	$_SERVER['REQUEST_TIME'],
		));
	}

	public function action_process()
	{
		if ( ! empty($_FILES))
		{
			// Normalise the $_FILES array, which varies according to upload method.
			// Result should be an array of files in $newfiles.
			if ( array_key_exists( 'b-assets-upload-files', $_FILES ) ) {

				if ( is_array($_FILES['b-assets-upload-files']['tmp_name']) ) {
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

				$newfiles = $newfiles['b-assets-upload-files'];

			} else {
				// Uploadify POST
				$newfiles = $_FILES;
			}


			/**
			* Validate based on the upload token.
			*/
			// Get the token data.
			$token = $this->request->post('upload_token');

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
					$asset->uploaded_by = $this->person->id;
					$asset->type = Boom_Asset::type_from_mime(File::mime($file['tmp_name']));
					$asset->visible_from = 'now';
					$asset->last_modified = $_SERVER['REQUEST_TIME'];

					if ($asset->type == Boom_Asset::IMAGE)
					{
						// Set the dimensions of the image.
						list($width, $height) = getimagesize($file['tmp_name']);
						$asset->width = $width;
						$asset->height = $height;
					}

					$asset->save();

					try
					{
						if ($asset_id !== NULL)
						{
							// We're replacing an existing asset so move the old file to prevent overwriting it.
							// Backup asset files with the curretn version ID (the version at which they were replaced).
							// This is so that we can easily get the version details from the db to report who replaced the asset.
							@rename(Boom_Asset::$path.$asset->id, Boom_Asset::$path.$asset->id.".".$_SERVER['REQUEST_TIME'].".bak");
						}

						Upload::save($file, $asset->id, Boom_Asset::$path);
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
							if ( ! file_exists(Boom_Asset::$path . $asset->id))
							{
								rename(Boom_Asset::$path . $asset->id . "." . $asset->version->id . ".bak", Boom_Asset::$path . $asset->id);
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
					foreach (glob(Boom_Asset::$path . $asset_id . "_*.cache") as $cached)
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

			if ( ! empty($asset_ids))
			{
				$this->response->body( json_encode( array('rids' => $asset_ids, 'errors' => array())));
			}
		}
	}
}