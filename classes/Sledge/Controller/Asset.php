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

		$id = $this->request->param('id');

		$this->asset = ORM::factory('Asset', $id);
		if ( ! $this->asset->loaded())
		{
			$this->redirect('/');
		}
	}

	public function action_embed()
	{
		$asset = Sledge_Asset::factory($this->asset);
		$this->response->body($asset->embed());
	}

	public function action_view()
	{
		if ($this->asset->loaded() AND ($this->asset->status == Model_Asset::STATUS_PUBLISHED OR Auth::instance()->logged_in()))
		{
			// Don't send HTTP cache headers when in development.
			// We don't want the browser caching images with the dimensions watermark.
			if (Kohana::$environment !== Kohana::DEVELOPMENT)
			{
				$this->response->headers('Cache-Control', 'public');

				// cache by etag.
				$this->response->check_cache($this->asset->version->id, $this->request);

				// Cache by last modified time.
				$this->response->headers('Last-Modified', gmdate(DATE_RFC1123, $this->asset->audit_time));

				if ($this->request->headers('If-Modified-Since') AND strtotime($this->request->headers('If-Modified-Since')) >= $this->asset->audit_time)
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
		if ($this->asset->loaded() AND ($this->asset->status == Model_Asset::STATUS_PUBLISHED OR Auth::instance()->logged_in()))
		{
			// Don't send HTTP cache headers when in development.
			// We don't want the browser caching images with the dimensions watermark.
			if (Kohana::$environment !== Kohana::DEVELOPMENT)
			{
				$this->response->headers('Cache-Control', 'public');

				// cache by etag.
				$this->response->check_cache($this->asset->version->id, $this->request);

				// Cache by last modified time.
				$this->response->headers('Last-Modified', gmdate(DATE_RFC1123, $this->asset->audit_time));

				if ($this->request->headers('If-Modified-Since') AND strtotime($this->request->headers('If-Modified-Since')) >= $this->asset->audit_time)
				{
					$this->response->status(304);
				}
			}

			$this->asset = Sledge_Asset::factory($this->asset);
			$this->asset->preview($this->response, $this->request->param('width'), $this->request->param('height'), $this->request->param('quality'), (bool) $this->request->param('crop'));
		}
	}
}