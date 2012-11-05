<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * People controller
 * Pages for managing people.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller_Cms_People extends Sledge_Controller
{
	/**
	 * Check that they can manage people.
	 */
	public function before()
	{
		parent::before();

		if ( ! $this->auth->logged_in('manage_people'))
		{
			throw new HTTP_Exception_403;
		}
	}

	/**
	 * Add person controller.
	 * Displays the form to enter the new user's details.
	 *
	 */
	public function action_add()
	{
		$this->template = View::factory('sledge/people/create_person', array(
			'groups'	=>	ORM::factory('Group')
				->where('deleted', '=', FALSE)
				->order_by('name', 'asc')
				->find_all(),
		));
	}

	/**
	 * Add the user into a group.
	 * Dual function controller. If the request method is POST then the user is added into the given groups, otherwise a template listing the groups which the user is not a member of is shown.
	 *
	 * **Accepted POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * groups		|	array 	|	Array of group IDs to add the user into.
	 */
	public function action_add_group()
	{
		$person = ORM::factory('Person', $this->request->param('id'));

		if ($person->loaded())
		{
			if ($this->request->method() === Request::POST)
			{
				$groups = $this->request->post('groups');

				foreach ( (array) $groups as $group_id)
				{
					Sledge::log("Added person $person->emailaddress to group with ID $group_id");

					try
					{
						$person->add('groups', $group);
					}
					catch (Database_Exception $e) {}
				}
			}
			else
			{
				// Find the groups that this person isn't already a member of.
				$groups = ORM::factory('Group')
					->where('group.id', 'NOT IN',
						DB::Select('group_id')
							->from('people_groups')
							->where('person_id', '=', $person->id)
					)
					->where('deleted', '=', FALSE)
					->find_all();

				$this->template = View::factory('sledge/people/addgroup', array(
					'person'	=>	$person,
					'groups'	=>	$groups,
				));
			}
		}
	}

	/**
	 * Delete people.
	 * Dual function controller, but it doesn't need to be!
	 *
	 * **Accepted POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * people		|	array	|	Array of person IDs of the people who are to be deleted.
	 *
	 * @todo Change JS to request confirmation rather than depending on templates.
	 * @uses Model_Person::delete()
	 */
	public function action_delete()
	{
		if ($this->request->method() === Request::POST)
		{
			$people = (array) $this->request->post('people');

			foreach ($people as $person_id)
			{
				Sledge::log("Deleted person $person->emailaddress");

				try
				{
					ORM::factory('Person', $person_id)
						->delete();
				}
				catch (Exception $e) {}
			}
		}
		else
		{
			$this->response->body(View::factory('sledge/people/confirm_delete'));
		}
	}

	/**
	 * Removes a user from a group
	 *
	 * **Accepted POST variables:**
	 * Name		|	Type	|	Description
	 * ---------------|-----------|---------------
	 * groups		|
	 *
	 */
	public function action_delete_group()
	{
//		try
//		{
			ORM::factory('person', $this->request->param('id'))
				->remove('groups', $this->request->post('groups'));
//		}
//		catch (Exception $e) {}
	}

	/**
	* Display the people manager.
	*/
	public function action_index()
	{
		$this->template = View::factory('sledge/people/index', array(
			'groups'	=>	ORM::factory('Group')
				->where('deleted', '=', FALSE)
				->order_by('name', 'asc')
				->find_all()
		));
	}

	/**
	 * List the people matching certain filters.
	 * Used to provide the main content for the people manager.
	 *
	 * **Accepted GET variables:**
	 * Name			|	Type		|	Description
	 * ----------------------|-----------------|---------------
	 * page			|	int		|	Which page of results to display. Default is 1.
	 * group			|	int		|	Enables filtering people by group.
	 * sortby			|	string	|	Which column to sort results by. Default is firstname, lastname.
	 * order			|	string	|	CWhich direction to sort results in, can be 'asc' or 'desc'. Default is asc.
	 *
	 */
	public function action_list()
	{
		// Import query string paramaters
		$page	=	max(1, $this->request->query('page'));
		$group	=	$this->request->query('tag');
		$sortby	=	$this->request->query('sortby');
		$order	=	$this->request->query('order');

		$subquery = DB::select('person_id')
			->from('people_groups');

		if ($group)
		{
			// Restrict results by group.
			$subquery->where('group_id', '=', $group);
		}
		else
		{
			// Only get people who belong to a group in this CMS.
			$subquery->distinct(TRUE);
		}

		$subquery = $subquery
			->execute()
			->as_array();

		$query = ORM::factory('Person')
			->where('deleted', '=', FALSE)
			->where('person.id', 'IN', $subquery);

		if ($sortby == 'audit_time' AND ($order == 'desc' OR $order == 'asc'))
		{
			$query->order_by($sortby, $order);
		}
		elseif ($sortby == 'name' AND ($order == 'desc' OR $order == 'asc'))
		{
			$query->order_by('firstname', $order);
			$query->order_by('lastname', $order);
		}
		else
		{
			$sortby = 'name';
			$order = 'asc';
			$query->order_by('firstname', $order);
			$query->order_by('lastname', $order);
		}

		$count = clone $query;
		$total = $count->count_all();

		$people = $query
			->limit(30)
			->offset( ($page - 1) * 30)
			->find_all();

		$this->template = View::factory('sledge/people/list', array(
			'people'	=>	$people,
			'group'	=>	ORM::factory('Group', $group),
			'total'	=>	$total,
			'sortby'	=>	$sortby,
			'order'	=>	$order,
		));

		$pages = ceil($total / 30);

		if ($pages > 1)
		{
			$url = '#tag/' . Arr::get($get, 'tag', 0);
			$pagination = View::factory('pagination/query', array(
				'current_page'	=>	$page,
				'total_pages'	=>	$pages,
				'base_url'		=>	$url,
				'previous_page'	=>	$page - 1,
				'next_page'	=>	($page == $pages)? 0 : ($page + 1),
			));

			$this->template->set('pagination', $pagination);
		}
	}

	/**
	 * Save changes to a person's details.
	 * Used to edit the details of an existing user and save the details for a new user.
	 *
	 * **Accepted POST variables:**
	 * Name			|	Type		|	Description
	 * ---------------------|-----------------|---------------
	 * emailaddress		|	string	|	Email address of the new user. Required.
	 * firstname		|	string	|	User's first name. Required.
	 * surname		|	string	|	User's last name. Required.
	 * password		|	string	|	User's password.
	 * group_id		|	int		|	ID of a group to add the user to.
	 *
	 * If any of the required POST parameters are missing then an ORM_Validation_Exception is thrown.
	 *
	 */
	public function action_save()
	{
		$id = $this->request->param('id');

		if ($id)
		{
			$person = ORM::factory('Person', $id);
		}
		else
		{
			$person = ORM::factory('Person', array('emailaddress' => $this->request->post('email')));
		}

		if ( ! $person->loaded())
		{
			// Set the person details.
			$person->firstname = $this->request->post('firstname');
			$person->lastname = $this->request->post('surname');
			$person->enabled = $this->request->post('status');

			$person->save();
		}

		// If we're adding a new user then a group ID may be given to add the user to an inital group.
		$group_id = (int) $this->request->post('group_id');

		if ($group_id > 0)
		{
			$person->add_group($group_id);
		}

		Sledge::log("Added user $person->emailaddress (ID: $person->id) to the CMS");
		$this->response->body($person->id);
	}

	/**
	 * People manager view person.
	 * Accepts multiple person IDs in the route's ID parameter, separated by hyphens.
	 *
	 * @example http://site.com/cms/people/view/1-2-3
	 */
	public function action_view()
	{
		$people_ids = (array) explode("-", $this->request->param('id'));
		$people = array();

		foreach ($people_ids as $person_id)
		{
			$person = ORM::factory('Person', $person_id);

			if ($person->loaded())
			{
				$people[] = $person;
			}
		}

		$this->template = View::factory('sledge/people/detailview', array(
			'people'	=>	$people,
		));
	}
}
