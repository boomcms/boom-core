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

	/**
	* Set the default template.
	* Used by Controller_Template to know which template to use.
	* @see http://kohanaframework.org/3.0/guide/kohana/tutorials/hello-world#that-was-good-but-we-can-do-better
	* @access public
	* @var string
	*/
	public $template = 'cms/standard_template';
	
	public function before()
	{
		// Require a user to be logged in for anything cmsy.
		if (!Auth::instance()->logged_in())
		{
			Cookie::set( 'redirect_after', Request::current()->uri() );
			Request::factory( '/cms/login' )->execute();
			exit();
		}
		
		// Check the user has the required permissions to view this page.
		/*if (!Permissions::may_i( do this )
		{
			Request::factory( 'error/403' )->execute();
		}*/
		
		parent::before();
	}
	
	public function action_index()
	{
		if (!$this->auth->logged_in())
		{
			Request::factory( 'cms/login' )->execute();
		}
		else
		{
			$this->request->redirect( '/' );
		}	
	}
	
	public function action_mode()
	{
		die( 'hello' );
	}

	public function after()
	{
		// Add the header subtemplate.
		$this->template->title = 'CMS';
		$this->template->subtpl_header = View::factory( 'site/subtpl_header' );
		$this->template->client = Kohana::$config->load('config')->get( 'client_name' );
		
		$actionbar = null;
		$buttonbar = null;
		
		View::bind_global( 'actionbar', $actionbar );
		View::bind_global( 'buttonbar', $buttonbar );
		
		parent::after();
	}
	

	public function action_who()
	{
		die( 'hello' );
		$this->template->subtpl_main = View::factory( 'cms/pages/who/index' );
		
	}
}


?>
