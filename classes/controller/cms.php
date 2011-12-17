<?php

/**
* CMS controller
*
* @package Sledge
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates Ltd
*/
class Controller_Cms extends Controller_Template_Cms
{	

	public function action_who()
	{
		die( 'hello' );
		$this->template->subtpl_main = View::factory( 'cms/pages/who/index' );
		
	}
	
	public function action_index()
	{
		if (!Auth::instance()->logged_in())
			Request::factory( '/cms/login' )->execute();
		else
			Request::factory( '/' )->execute();
		exit();
	}
}


?>