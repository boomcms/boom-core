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
	 *
	 * @var	string	Directory where the views which relate to this class are held.
	 */
	protected $_view_directory = 'sledge/people/';

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
		$this->template = View::factory($this->_view_directory . "new", array(
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
		$person = new Model_Person($this->request->param('id'));

		if ($person->loaded())
		{
			if ($this->request->method() === Request::POST)
			{
				$groups = $this->request->post('groups');

				foreach ( (array) $groups as $group_id)
				{
					$this->_log("Added person $person->email to group with ID $group_id");

					try
					{
						$person->add('groups', $group_id);
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
	 * @uses Model_Person::delete()
	 */
	public function action_delete()
	{
		$people = (array) $this->request->post('people');

		foreach ($people as $person_id)
		{
			$this->_log("Deleted person $person->email");

			try
			{
				ORM::factory('Person', $person_id)
					->delete();
			}
			catch (Exception $e) {}
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
			ORM::factory('Person', $this->request->param('id'))
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
	 * sortby			|	string	|	Which column to sort results by. Default is name.
	 * order			|	string	|	CWhich direction to sort results in, can be 'asc' or 'desc'. Default is asc.
	 *
	 */
	public function action_list()
	{
		// Import query string paramaters
		$page	=	max(1, $this->request->query('page'));
		$group	=	$this->request->query('tag');
		$order	=	$this->request->query('order');

		$query =new Model_Person;

		if ($group)
		{
			// Restrict results by group.
			$query
				->join('people_groups', 'inner')
				->on('person_id', '=', 'id')
				->where('group_id', '=', $group);
		}

		if ( ! ($order == 'desc' OR $order == 'asc'))
		{
			// Sort ascending by default.
			$order = 'asc';
		}

		$query->order_by('name', $order);

		$count = clone $query;
		$total = $count->count_all();

		$people = $query
			->limit(30)
			->offset( ($page - 1) * 30)
			->find_all();

		$this->template = View::factory('sledge/people/list', array(
			'people'	=>	$people,
			'group'	=>	new Model_Group($group),
			'total'	=>	$total,
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
	 * name§			|	string	|	User's name.
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
			$person = new Model_Person($id);
		}
		else
		{
			$person = new Model_Person(array(
				'email'	=>	$this->request->post('email')
			));
		}

		if ( ! $person->loaded())
		{
			// If the person wasn't loaded then we're creating a new person so allow the email address to be set.
			$person->email = $this->request->post('email');
		}

		// Set the person details.
		$person
			->values(array(
				'name'		=>	$this->request->post('name'),
				'enabled'		=>	$this->request->post('status')
			))
			->save();

		// If we're adding a new user then a group ID may be given to add the user to an inital group.
		if ($this->request->post('group_id') > 0)
		{
			$group = new Model_Group($this->request->post('group_id'));

			if ($group->loaded())
			{
				$person->add('groups', $group);
			}
		}

		$this->_log("Added user $person->email (ID: $person->id) to the CMS");
		$this->response->body($person->id);
	}

	/**
	 * People manager view person.
	 *
	 */
	public function action_view()
	{
		$person = new Model_Person($this->request->param('id'));

		if ( ! $person->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$this->template = View::factory($this->_view_directory . "view", array(
			'person'	=>	$person,
		));
	}
}