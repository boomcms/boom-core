<?php defined('SYSPATH') or die('No direct script access.');

/**
* People controller
* Pages for managing people.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Controller_Cms_People extends Controller_Template_Cms {
	
	public function before()
	{	
		parent::before();
		
		$this->template->title = 'People Manager';
		$actionbar = View::factory( 'cms/pages/people/actionbar' );
		$buttonbar = $v = View::factory( 'cms/pages/people/buttonbar' );
		
		View::bind_global( 'actionbar', $actionbar );
		View::bind_global( 'buttonbar', $buttonbar );
	}
	
	public function action_save()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		
		$v = ORM::factory( 'version_person' );
		$v->firstname = Arr::get( 'post', 'firstname', null );
		$v->lastname = Arr::get( 'post', 'firstname', null );		
		$v->emailaddress = Arr::get( 'post', 'firstname', null );
		
		$person = $v;
		$person->save();				
	}
	
	public function action_add()
	{
		$person = ORM::factory( 'person' );
		$activity = ORM::factory( 'activitylog' );
		
		$this->template->subtpl_main = View::factory( 'cms/pages/people/view' );
		$this->template->subtpl_main->person = $person;	
		$this->template->subtpl_main->activity = $activity;		
	}
	
	/**
	* People manager view person.
	*
	* @todo What if the person ID isn't valid?
	* @todo check permissions
	* @return void
	*/
	public function action_view()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		$activity = ORM::factory( 'activitylog' )->where( 'audit_person', '=', $person->id )->limit( '50' )->order_by( 'audit_time', 'desc' )->find();
		
		$this->template->subtpl_main = View::factory( 'cms/pages/people/view' );
		$this->template->subtpl_main->person = $person;	
		$this->template->subtpl_main->activity = $activity;			
	}
	
	/**
	* People manager default page.
	* Displays the people manager template with an array of people.
	*
	* @return void
	*/
	public function action_index()
	{
		$people = ORM::factory( 'person' )->find_all();
		
		$this->template->subtpl_main = View::factory( 'cms/pages/people/index' );
		$this->template->subtpl_main->people = $people;	
	}
	
}

?>