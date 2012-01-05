<?php defined('SYSPATH') or die('No direct script access.');

/**
* Modal Controller
* Handles displaying modal boxes for AJAX requests.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Cms_Modal extends Controller_Cms
{
	public function before()
	{
		// Modal boxes should only be shown via AJAX.
		/*
		But disabled for now for testing.
		if (!$this->request->is_ajax())
			exit();
		*/
		
		parent::before();
	}
	
	/**
	* Edit the user's account details.
	*/
	public function action_account()
	{
		$this->template = View::factory( 'cms/modal/account_details' );
		
		$people = ORM::factory( 'person' )->order_by( 'firstname', 'asc' )->order_by( 'lastname', 'asc' )->find_all()->as_array();
		$this->template->people = $people;
	}
}

?>