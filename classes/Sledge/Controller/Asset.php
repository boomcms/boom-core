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
		$this->asset = new Model_Asset($this->request->param('id'));

		// Check that the asset exists.
		if ( ! $this->asset->loaded() OR ($this->asset->visible_from > $_SERVER['REQUEST_TIME'] AND ! $this->auth->logged_in()))
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
		$this->response->headers('Cache-Control', 'public');

		// cache by etag.
		HTTP::check_cache($this->request, $this->response, $this->asset->last_modified);

		$asset = Sledge_Asset::factory($this->asset);
		$asset->show($this->response, $this->request->param('width'), $this->request->param('height'), $this->request->param('quality'), (bool) $this->request->param('crop'));
	}

	public function action_thumb()
	{
		// TODO: this is overloaded in Sledge_Controller::after()
		$this->response->headers('Cache-Control', 'public');

		// cache by etag.
		HTTP::check_cache($this->request, $this->response, $this->asset->last_modified);

		$asset = Sledge_Asset::factory($this->asset);
		$asset->preview($this->response, $this->request->param('width'), $this->request->param('height'), $this->request->param('quality'), (bool) $this->request->param('crop'));
	}
}