<?php defined('SYSPATH') OR die('No direct script access.');
/**
* MP3 asset decorator
*
* @package Boom
* @category Assets
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Boom_Asset_MP3 extends Asset
{
	public function show(Response $response)
	{
		$response->headers('Content-type', 'audio/mpeg');
		$response->headers("Content-Length", filesize(ASSETPATH . $this->instance()->pk()));
		ob_clean();
		flush();
		readfile(ASSETPATH . $this->_asset->filename);
	}


	public function preview(Response $response)
	{
		$response->body("<a href='/asset/" . $this->_asset->id . "'>Click here to listen to MP3</a>");
	}
}
