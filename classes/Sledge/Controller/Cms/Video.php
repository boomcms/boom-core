<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Video controller
 * Contains methods for interacting with Bits on the Run to upload / save videos.
 *
 *
 * @package Sledge
 * @category Assets
 * @category Controllers
 */

class Sledge_Controller_Cms_Video extends Sledge_Controller
{
	/**
	 * Bits on the Run API object.
	 */
	private $botr_api;

	public function before()
	{
		parent::before();

		require(Kohana::find_file('vendor', 'botr'));

		// Load the botr config to get the api key / secret.
		$botr_config = Kohana::$config->load('botr');

		// Instantiate the Bits on the Run API.
		$this->botr_api = new BotrAPI($botr_config->key, $botr_config->secret);
	}

	/**
	 * Update the video with metadata from Bits on the Run
	 * Expects an asset ID to be given in the URL
	 */
	public function action_sync()
	{
		$asset = new Model_Asset($this->request->param('id'));

		// Throw a 404 if the asset doesn't exist.
		if ( ! $asset->loaded())
		{
			throw new HTTP_Exception_404;
		}

		// Check that the specified asset is a botr hosted video.
		if ($asset->type == Sledge_Asset::BOTR)
		{
			// Try and get the video details from BOTR.
			$response = $this->botr_api->call('/videos/show', array(
				'video_key' => $asset->filename,
			));

			if ($response['status'] == 'error')
			{
				throw new Kohana_Exception($response['subject'] . "\n" . $response['message']);
			}

			// Sync the video metadata.
			$asset->title = $response['video']['title'];
			$asset->filesize = $response['video']['size'];
			$asset->duration = round($response['video']['duration']);
			$asset->encoded = ($response['video']['status'] == 'ready');
			$asset->views = $response['video']['views'];
			$asset->update();

			// Save the video thumbnail localy.
			try
			{
				copy("http://content.bitsontherun.com/thumbs/" . $asset->filename . ".jpg", ASSETPATH . $asset->id . ".thumb");
			}
			catch (Exception $e) {}
		}
	}

	/**
	 * Controller to upload a new video to Bits on the Run.
	 */
	public function action_upload()
	{
		if ( ! $this->request->query('video_key'))
		{
			// No video key is given so we're in the first stage of upload.

			// Make a call to the botr API to create a video.
			$response = $this->botr_api->call('/videos/create');
			$token = $response['link']['query']['token'];

			// If there's an error then throw an exception so that it gets handled by the CMS error handling.
			if ($response['status'] == 'error')
			{
				throw new Kohana_Exception($response['subject'] . "\n" . $response['message']);
			}

			$url  = 'http://' . $response['link']['address'] . $response['link']['path'];
			$url .= '?key=' . $response['link']['query']['key'];
			$url .= '&api_format=xml';
			$url .= '&redirect_address=' . URL::base($this->request) . $_SERVER["REQUEST_URI"];
			$url .=  '&token=' . $token;

			$this->template = View::factory('sledge/assets/upload_video', array(
				'url'	=>	$url,
				'token'	=>	$token,
			));
		}
		else
		{
			// Got a video key, the video has been uploaded to bits on the Run, we need to create a corresponding asset and display it.

			$video_key = $this->request->query('video_key');
			$response = $this->botr_api->call('/videos/show', array('video_key'=>$video_key));

			// If there's an error then throw an exception so that it gets handled by the CMS error handling.
			if ($response['status'] == 'error')
			{
				throw new Kohana_Exception($response['subject'] . "\n" . $response['message']);
			}

			$asset = ORM::factory('Asset')
				->values(array(
					'filename'		=>	$video_key,		// Store the video key in the filename column.
					'type'		=>	Sledge_Asset::BOTR,
					'encoded'		=>	FALSE,
					'visible_from'	=>	$_SERVER['REQUEST_TIME']
				))
				->create();

			$this->redirect("/cms/assets#asset/" . $asset->id);
		}
	}
}
