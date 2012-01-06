<?php defined('SYSPATH') or die('No direct script access.');

/**
* Roles controller
* Pages for managing user roles.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Cms_Roles extends Controller_Cms {
	
	public function before()
	{		
		parent::before();
		
		if (!$this->person->can( 'manage roles' ))
			Request::factory( 'error/403' )->execute();
		
		$this->template->title = 'People Manager';
		$actionbar = View::factory( 'cms/pages/people/actionbar' );
		$buttonbar = View::factory( 'cms/pages/people/buttonbar' );
		
		View::bind_global( 'actionbar', $actionbar );
		View::bind_global( 'buttonbar', $buttonbar );
	}
	
	public function action_save()
	{
				
	}
	
	public function action_add()
	{
	
	}
	
	public function action_view()
	{
			
	}
}

?>
