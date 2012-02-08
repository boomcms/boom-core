<?php defined('SYSPATH') or die('No direct script access.');

/**
* People controller
* Pages for managing people.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/

class Controller_Cms_People extends Controller_Cms
{	
	public function before()
	{		
		parent::before();
		
		if (!$this->person->can( 'manage people' ))
			Request::factory( 'error/403' )->execute();
		
		$this->template->title = 'People Manager';
		$subtpl_topbar = View::factory( 'cms/ui/people/topbar' );
		
		View::bind_global( 'subtpl_topbar', $subtpl_topbar );
	}
	
	public function action_save()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		$person = ORM::factory( 'person', $id );
		
		if ($person->loaded())
		{
			$person->firstname = Arr::get( $_POST, 'firstname' );
			$person->lastname = Arr::get( $_POST, 'surname' );		
			$person->emailaddress = Arr::get( $_POST, 'email' );
		
			if (Arr::get( $_POST, 'password' ))
			{
				$person->password = Arr::get( $_POST, 'password' );
			}
		
			$person->save();
		
			$this->request->redirect( "/cms/people/view/$person->id" );	
		}		
	}
	
	/**
	* Add person controller.
	*/
	public function action_add()
	{
		if ($this->request->method() == 'POST')
		{
			// Check that person doesn't already exist.
			$person = ORM::factory( 'person' )->where( 'emailaddress', '=', Arr::get( $_POST, 'email' ) )->find();
			
			if (!$person->loaded())
			{
				$person = ORM::factory( 'person' );
				$person->emailaddress = Arr::get( $_POST, 'email' );
			}
			
			// Add this user to the group and update details.
			$person->firstname = Arr::get( $_POST, 'firstname' );
			$person->lastname = Arr::get( $_POST, 'surname' );
			$person->password = Arr::get( $_POST, 'password' );		
			$person->save();	
			
			$group_id = Arr::get( $_POST, 'group_id' );	
			$person->add_group( $group_id );
			
			$this->request->redirect( '/cms/people/view/' . $person->pk() );
		}
		else
		{
			$v = View::factory( 'cms/ui/people/create_person' );
			$v->groups = ORM::factory( 'group' )->find_all();
			echo $v;
			exit;
		}	
	}
	
	public function action_add_group()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		
		if ($person->loaded())
		{
			if ($this->request->method() == 'POST')
			{
				$groups = Arr::get( $_POST, 'group_id' );
				
				if (is_array( $groups ))
				{
					foreach( $groups as $group_id )
					{
						$person->add_group( $group_id );
					}	
				}
				else
				{
					$person->add_group( $groups );
				}				
				$this->request->redirect( '/cms/people/view/' . $person->pk() );
			}
			else
			{
				// Find the groups that this person isn't already a member of.
				$groups = ORM::factory( 'group' )
						->where('group.id', 'NOT IN', 
							DB::Select( 'group_id' )
							->from( 'person_group' )
							->where( 'person_id', '=', $person->pk() )
						)
						->find_all();
							
				$this->template->subtpl_main = View::factory( 'cms/ui/people/addgroup' );
				$this->template->subtpl_main->person = $person;
				$this->template->subtpl_main->groups = $groups;
			}
		}
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
		
		$this->template->subtpl_main = View::factory( 'cms/ui/people/detailview' );
		$this->template->subtpl_main->person = $person;		
	}
	
	public function action_delete()
	{
		if ($this->request->method() == 'POST')
		{
			$people = Arr::get( $_POST, 'people' );
			
			if (is_array( $people ))
			{	
				foreach( $people as $person_id )
				{
					$person_id = str_replace( "person_", "", $person_id );
					$person = ORM::factory( 'person', $person_id );
					$person->delete();
				}	
			}
			else
			{
				$person_id = str_replace( "person_", "", $people );
				$person = ORM::factory( 'person', $person_id );
				$person->delete();
			}				
		}
		else
		{
			echo View::factory( 'cms/ui/people/confirm_delete' );
		}
		
		exit;
	}
	
	public function action_delete_group()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		
		$person = ORM::factory( 'person', $id );
		
		if ($person->loaded())
		{
			$groups = Arr::get( $_POST, 'group_id' );
		
			if (is_array( $groups ))
			{
				ORM::factory( 'person_group' )->where( 'group_id', '=', $groups )->where( 'person_id', '=', $person->pk() )->delete();
			}
			else
			{
				ORM::factory( 'person_group' )->where( 'group_id', '=', $groups )->where( 'person_id', '=', $person->pk() )->delete();
			}
		}
		
		$this->request->redirect( '/cms/people/view/' . $person->pk() );
	}
	
	/**
	* People manager default page.
	* Displays the people manager template with an array of people.
	*
	* @return void
	*/
	public function action_index()
	{	
		$this->template->subtpl_main = View::factory( 'cms/ui/people/manager' );
		$this->template->subtpl_main->groups = ORM::factory( 'group' )->find_all();
		$this->template->subtpl_main->people = ORM::factory( 'person' )->where( 'deleted', '=', 'false' )->find_all();
	}
	
}

?>
