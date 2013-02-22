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
	 *
	 */
	public function action_begin()
	{
		$this->template = View::factory("$this->_view_directory/upload");
	}

	/**
	 * Create assets from a file upload.
	 *
	 * @uses Model_Asset::create_from_file()
	 *
	 */
	public function action_process()
	{
		// An array of IDs of the new assets, which we'll return to the client when the upload has been processed.
		$asset_ids = array();

		// Values which will be the same for all of the new assets.
		$common_values = array(
			'uploaded_by'	=>	$this->person->id,
			'visible_from'	=>	'now',
			'last_modified'	=>	$_SERVER['REQUEST_TIME'],
		);

		// Loop through the file inputs.
		foreach ( (array) $_FILES as $files)
		{
			// Loop through the files uploaded under this input name.
			foreach ( (array) $files['tmp_name'] as $i => $filename)
			{
				// Set the common values.
				$this->asset->values($common_values, array_keys($common_values));

				// Set the title of the asset to the filename without the file extension.
				$this->asset->title = pathinfo($files['name'][$i], PATHINFO_FILENAME);
				$this->asset->filename = $files['name'][$i];

				// Create the asset from the temporary file.
				$this->asset->create_from_file($filename);

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