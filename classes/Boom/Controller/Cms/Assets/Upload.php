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
		if (isset($_FILES['b-assets-upload-files']))
		{
			// An array of IDs of the new assets, which we'll return to the client when the upload has been processed.
			$asset_ids = array();

			// Allow us to get the file upload info without having to type that mamouth each time...
			$files = $_FILES['b-assets-upload-files'];

			foreach ( (array) $files['name'] as $i => $filename)
			{
				// Set the values for the asset model.
				$this->asset
					->values(array(
						'filename'		=>	$filename,
						'filesize'		=>	$files['size'][$i],
						'uploaded_by'	=>	$this->person->id,
						'type'		=>	Boom_Asset::type_from_mime(File::mime($files['tmp_name'][$i])),
						'visible_from'	=>	'now',
						'last_modified'	=>	$_SERVER['REQUEST_TIME']
					));

				// If the asset is an image then set the dimensionis.
				if ($this->asset->type == Boom_Asset::IMAGE)
				{
					// Set the dimensions of the image.
					list($width, $height) = getimagesize($files['tmp_name'][$i]);

					$this->asset
						->set('width', $width)
						->set('height', $height);
				}

				// Create a record for the asset in the database.
				$this->asset->create();

				try
				{
					// Save the uploaded file to the assets directory.
					Upload::save(array('tmp_name' => $files['tmp_name'][$i]), $this->asset->id, Boom_Asset::$path);
				}
				catch (Exception $e)
				{
					// There was a problem - delete the database record.
					$this->asset->delete();

					throw $e;
				}

				// Add the asset ID to the array of uploaded assets.
				$asset_ids[] = $this->asset->id;

				// Clear the model so that it can be re-used for the next iteration.
				// This way we don't have to instantiate an asset model for each loop iteration.
				$this->asset->clear();
			}

			// Give an array of IDs for the new assets in the response body.
			$this->response
				->headers('Content-Type', 'application/json')
				->body(json_encode($asset_ids));
		}
	}
}