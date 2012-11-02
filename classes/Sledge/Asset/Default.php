<?php defined('SYSPATH') OR die('No direct script access.');
/**
*
* @package Sledge
* @category Assets
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Sledge_Asset_Default extends Sledge_Asset
{	
	public function show(Response $response)
	{
		return '';	
	}
	
	public function preview(Response $response)
	{
		$image = Image::factory(MODPATH . 'sledge/static/cms/img/icons/40x40/default_icon.gif');
			
		$response->headers('Content-type', 'image/gif');
		$response->body($image->render());
	}
}