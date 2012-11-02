<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Video asset decorator
*
* @package Sledge
* @category Assets
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Sledge_Asset_Video extends Sledge_Asset
{
	public function embed()
	{
		return "<iframe src='" . $this->_asset->filename . "' frameborder='0' class='video' allowfullscreen></iframe>";
	}

	public function show(Response $response)
	{
		return $this->embed($response);
	}
	
	public function preview(Response $response)
	{
		$image = Image::factory(MODPATH . 'sledge/static/cms/img/icons/40x40/mov_icon.gif');

		$response->headers('Content-type', 'image/gif');
		$response->body($image->render());
	}
}