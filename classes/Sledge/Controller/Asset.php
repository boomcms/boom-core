<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Asset controller.
* @package Sledge
* @category Assets
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Sledge_Controller_Asset extends Sledge_Controller
{
	private $asset;

	public function before()
	{
		parent::before();

		// Load the asset from the database.
		$this->asset = ORM::factory('Asset', $this->request->param('id'));

		// Check that the asset exists.
		if ( ! $this->asset->loaded())
		{
			throw new HTTP_Exception_404;
		}
	}

	public function action_embed()
	{
		$asset = Sledge_Asset::factory($this->asset);
		$this->response->body($asset->embed());
	}

	public function action_view()
	{
		if ($this->asset->status == Model_Asset::STATUS_PUBLISHED OR Auth::instance()->logged_in())
		{
			// Don't send HTTP cache headers when in development.
			// We don't want the browser caching images with the dimensions watermark.
			if (Kohana::$environment !== Kohana::DEVELOPMENT)
			{
				$this->response->headers('Cache-Control', 'public');

				// cache by etag.
				HTTP::check_cache($this->request, $this->response, $this->asset->last_modified);

				// Cache by last modified time.
				$this->response->headers('Last-Modified', gmdate(DATE_RFC1123, $this->asset->last_modified));

				if ($this->request->headers('If-Modified-Since') AND strtotime($this->request->headers('If-Modified-Since')) >= $this->asset->last_modified)
				{
					$this->response->status(304);
					return;
				}
			}

			$this->asset = Sledge_Asset::factory($this->asset);
			$this->asset->show($this->response, $this->request->param('width'), $this->request->param('height'), $this->request->param('quality'), (bool) $this->request->param('crop'));
		}
	}

	public function action_thumb()
	{
		if ($this->asset->status == Model_Asset::STATUS_PUBLISHED OR $this->auth->logged_in())
		{
			// TODO: this is overloaded in Sledge_Controller::after()
			$this->response->headers('Cache-Control', 'public');

			// cache by etag.
			HTTP::check_cache($this->request, $this->response, $this->asset->last_modified);

			$asset = Sledge_Asset::factory($this->asset);
			$asset->preview($this->response, $this->request->param('width'), $this->request->param('height'), $this->request->param('quality'), (bool) $this->request->param('crop'));
		}
	}
}