<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * People controller
 * Pages for managing people.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_People extends Boom_Controller
{
	/**
	 *
	 * @var	string	Directory where the views which relate to this class are held.
	 */
	protected $_view_directory = 'boom/people/';

	/**
	 *
	 * @var Model_Person
	 */
	public $person;

	/**
	 * Checks that the current user can access the people manager.
	 * Also instantiates a Model_Person object and assigns it to [Boom_Controller_Cms_People::$person]
	 *
	 * @uses Boom_Controller::before()
	 * @uses Boom_Controller::_authorization()
	 * @uses Boom_Controller_Cms_People::$person
	 */
	public function before()
	{
		parent::before();

		// Check that we're allowed to be here.
		$this->_authorization('manage_people');
	}

	/**
	 * Adds a new person to the CMS.
	 *
	 * @uses Boom_Controller::_log()
	 * @uses Model_Person::values()
	 * @uses Model_Person::create()
	 */
	public function action_add()
	{
		if ($this->request->method() === Request::POST)
		{
			// POST request - add a person to the CMS.
			$this->person
				->set('email', $this->request->post('email'))
				->create()
				->add_group($this->request->post('group_id'));
		}
		else
		{
			// Not a POST request.
			// Display a view to enter the person's email address and select an initial group for the person.
			$this->template = View::factory($this->_view_directory."new", array(
				'groups'	=>	ORM::factory('Group')
					->where('deleted', '=', FALSE)
					->order_by('name', 'asc')
					->find_all(),
			));
		}
	}

	/**
	 * Add the user to a group.
	 *
	 * If the request method is POST then the user is added into the given group.
	 * Otherwise a view listing the groups which the user is not a member of is shown.
	 *
	 * @uses Model_Person::add_group()
	 * @uses Boom_Controller::_log()
	 */
	public function action_add_group()
	{
		if ($this->request->method() === Request::POST)
		{
			// POST request - add the person to a group.

			// Get the ID of the group we're adding the person to.
			$group_id = $this->request->post('group_id');

			// Log the action
			$this->_log("Added person $this->person->email to group with ID $group_id");

			// Add the person to the given group.
			$this->person->add_group($group_id);
		}
		else
		{
			// Non-POST request, show a view.

			// Find the groups that this person isn't already a member of.
			$groups = ORM::factory('Group')
				->where('group.id', 'NOT IN',
					DB::Select('group_id')
						->from('people_groups')
						->where('person_id', '=', $this->person->id)
				)
				->where('deleted', '=', FALSE)
				->find_all();

			// Set the response template.
			$this->template = View::factory("$this->_view_directory/addgroup", array(
				'person'	=>	$this->person,
				'groups'	=>	$groups,
			));
		}
	}

	/**
	 * Delete a person.
	 *
	 * @uses Boom_Controller_Cms_People::_log()
	 * @uses Model_Person::delete()
	 */
	public function action_delete()
	{
		// Log the action.
		$this->_log("Deleted person with email address: ".$this->person->email);

		// Delete the person.
		$this->person->delete();
	}

	/**
	 * Display the people manager.
	 */
	public function action_index()
	{
		$this->template = View::factory("$this->_view_directory/index", array(
			'groups'	=>	ORM::factory('Group')
				->where('deleted', '=', FALSE)
				->order_by('name', 'asc')
				->find_all()
		));
	}

	/**
	 * List the people matching certain filters.
	 *
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

		$this->template = View::factory('boom/people/list', array(
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
	 * Removes a person from a group
	 *
	 * @uses Boom_Controller::_log()
	 * @uses Model_Person::remove_group()
	 */
	public function action_remove_group()
	{
		// Log the action.
		$this->_log("Edited the groups for person ".$this->person->email);

		// Remove the person from the group given in the POST data.
		$this->person->remove_group($this->request->post('group_id'));
	}

	/**
	 * Save changes to an existing person.
	 *
	 * @uses Boom_Controller::_log()
	 * @uses Model_Person::values()
	 * @uses Model_Person::update()
	 */
	public function action_save()
	{
		// Log the action.
		$this->_log("Edited user $this->person->email (ID: $this->person->id) to the CMS");

		// Update the person's details.
		$this->person
			->values(array(
				'name'		=>	$this->request->post('name'),
				'enabled'		=>	$this->request->post('enabled')
			))
			->update();
	}

	/**
	 * People manager view person.
	 *
	 * @throws HTTP_Exception_404
	 */
	public function action_view()
	{
		// Check that the person exists.
		if ( ! $this->person->loaded())
		{
			// No they don't, throw an exception.
			throw new HTTP_Exception_404;
		}

		// Show the person's details.
		$this->template = View::factory($this->_view_directory."view", array(
			'person'	=>	$this->person,
			'request'	=>	$this->request,
		));
	}
}