<?php defined('SYSPATH') or die('No direct script access.');

/**
* People controller
* Pages for managing people.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Controller_Cms_People extends Controller_Cms {	
	public function before()
	{		
		parent::before();
		
		if (!$this->person->can( 'manage people' ))
			Request::factory( 'error/403' )->execute();
		
		$this->template->title = 'People Manager';
		$subtpl_topbar = View::factory( 'ui/subtpl_people_topbar' );
		
		View::bind_global( 'subtpl_topbar', $subtpl_topbar );
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
	
	/**
	* Add person controller.
	*/
	public function action_add()
	{
		if ($this->request->method() == 'post')
		{
			// Create the person
			$values = array(
				'firstname'		=>	Arr::get( $_POST, 'create-firstname' ),
				'lastname'		=>	Arr::get( $_POST, 'create-surname' ),
				'emailaddress'	=>	Arr::get( $_POST, 'create-email' ),
				'password'		=>	Arr::get( $_POST, 'create-password' ),
			);
			
			$person = ORM::factory( 'person' )->values( $values )->create();
			
			// Add the person to the initial group.
			$group_id = Arr::get( $_POST, 'group_id' );
			$person->add( 'role', ORM::factory( 'roles', $group_id ) );
			
			$person->save();
			
			echo $person->pk();
		}
		else
		{
			$v = View::factory( 'ui/subtpl_peoplemanager/create_person' );
			$v->groups = ORM::factory( 'roles' )->find_all();
			echo $v;
		}	
		
		exit;
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
	
	public function action_delete()
	{
		$people = Arr::get( $_POST, 'people' );
	
		foreach( $people as $person_id )
		{
			$person_id = str_replace( "person_", "", $person_id );
			$person = ORM::factory( 'person', $person_id );
			$person->delete();
		}	
		
		exit;
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
		$roles = ORM::factory( 'role' )->find_all();
		
		$this->template->subtpl_main = View::factory( 'cms/pages/people/index' );
		$this->template->subtpl_main->people = $people;	
		$this->template->subtpl_main->roles = $roles;
	}
	
}

?>
