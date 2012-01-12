<?php

/**
* CMS controller
*
* @package Sledge
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates Ltd
*/
class Controller_Cms extends Sledge_Controller
{	
	public function before()
	{
		parent::before();
		
		$this->template = View::factory( 'cms/standard_template' );
		
		// Require a user to be logged in for anything cmsy.
		if (!Auth::instance()->logged_in())
		{
			Cookie::set( 'redirect_after', Request::current()->uri() );
			Request::factory( '/cms/login' )->execute();
			exit();
		}
	}
	
	public function action_index()
	{
		$this->request->redirect( '/' );	
	}

	public function after()
	{
		// Add the header subtemplate.
		$this->template->title = 'CMS';
		$this->template->subtpl_header = View::factory( 'site/subtpl_header' );
		$this->template->client = Kohana::$config->load('config')->get( 'client_name' );
		
		parent::after();
	}
}


?>
