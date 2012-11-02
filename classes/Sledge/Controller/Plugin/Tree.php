<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Tree controller.
* Generate page trees for cms, site, or feature box.
*
* @package Sledge
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Sledge_Controller_Plugin_Tree extends Kohana_Controller
{	
	public function action_leftnav()
	{
		$page = Request::initial()->param('page');

		$this->response->body(Request::factory("cms/tree/leftnav/$page->id")->execute());
	}
}