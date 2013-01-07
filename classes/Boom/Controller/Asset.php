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
	public $asset;

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
		$this->response->headers('Cache-Control', 'public');

		// Check the cache.
		HTTP::check_cache($this->request, $this->response, $this->asset->last_modified);

	}

	public function action_embed()
	{
		$asset = Boom_Asset::factory($this->asset);
		$this->response->body($asset->embed());
	}

	/**
	 * View an asset.
	 */
	abstract public function action_view();

	public function action_thumb()
	{
		// TODO: this is overloaded in Boom_Controller::after()
		$this->response->headers('Cache-Control', 'public');

		// cache by etag.
		HTTP::check_cache($this->request, $this->response, $this->asset->last_modified);

		$asset = Boom_Asset::factory($this->asset);
		$asset->preview($this->response, $this->request->param('width'), $this->request->param('height'), $this->request->param('quality'), (bool) $this->request->param('crop'));
	}
}