<?php

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
		$this->template = View::factory("$this->viewDirectory/upload");
	}

	/**
	 * Create assets from a file upload.
	 *
	 * @uses Model_Asset::create_from_file()
	 *
	 */
	public function action_process()
	{
		$this->_csrf_check();

		$asset_ids = $errors = array();

		$this->response->headers('Content-Type', static::JSON_RESPONSE_MIME);

		// Values which will be the same for all of the new assets.
		$common_values = array(
			'uploaded_by'	=>	$this->person->id,
			'visible_from'	=>	'now',
			'last_modified'	=>	$_SERVER['REQUEST_TIME'],
		);

		foreach ( (array) $_FILES as $files)
		{
			// Loop through the files uploaded under this input name.
			foreach ( (array) $files['tmp_name'] as $i => $filename)
			{
				$mime = File::mime($filename);
				if (\Boom\Asset\Mimetype::isSupported($mime))
				{
					$this->asset->values($common_values, array_keys($common_values));

					$this->asset->title = pathinfo($files['name'][$i], PATHINFO_FILENAME);
					$this->asset->filename = $files['name'][$i];
					$this->asset->create_from_file($filename);

					$asset_ids[] = $this->asset->getId();

					// Clear the model so that it can be re-used for the next iteration.
					// This way we don't have to instantiate an asset model for each loop iteration.
					$this->asset->clear();
				}
				else
				{
					$errors[] = "File {$files['name'][$i]} is of an unsuported type: {$mime}";
				}
			}

			if (count($errors))
			{
				$this->response
					->status(500)
					->body(json_encode($errors));
			}
			else
			{
				$this->response
					->body(json_encode($asset_ids));
			}
		}
	}

	public function action_replace()
	{
		$asset = new Model_Asset($this->request->post('asset_id'));

		$filename = Arr::pluck($_FILES, 'tmp_name');
		$filename = $filename[0][0];

		$asset->replace_with_file($filename);

		$this->response
			->headers('Content-Type', static::JSON_RESPONSE_MIME)
			->body(json_encode(array($asset->getId())));
	}
}