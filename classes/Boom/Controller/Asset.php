<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset controller.
 *
 * @package		BoomCMS
 * @category	Assets
 * @category	Controllers
 * @author		Rob Taylor
 * @copyright	Hoop Associates
 */
abstract class Boom_Controller_Asset extends Boom_Controller
{
	/**
	 *
	 * @var Model_Asset
	 */
	public $asset;

	/**
	 * The value to use for the max-age header.
	 *
	 * @var integer
	 */
	public $max_age = 86400;

	public function before()
	{
		parent::before();

		// Make the asset accessible from $this->asset
		$this->asset = $this->request->param('asset');

		// Check that the asset is visible
		if ($this->asset->visible_from > $_SERVER['REQUEST_TIME'] AND ! $this->auth->logged_in())
		{
			// The asset isn't visible and the user isn't logged in - 404.
			throw new HTTP_Exception_404;
		}

		// Set the cache to public.
		$this->response->headers('Cache-Control', 'public, max-age='.$this->max_age);

		// Check the cache.
		HTTP::check_cache($this->request, $this->response, $this->asset->last_modified);

	}

	public function action_embed()
	{
		$this->response->body(HTML::anchor('asset/view/'.$this->asset->id, "Download {$this->asset->title}"));
	}

	abstract public function action_view();

	abstract public function action_thumb();
}
