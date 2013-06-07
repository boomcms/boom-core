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
	 * Person object to be edited.
	 *
	 * **CAUTION**
	 *
	 * [Boom_Controller::before()] sets a person property which is the logged in person.
	 * YOU DON'T WANT TO USE THE WRONG PROPERTY.
	 *
	 * @var Model_Person
	 */
	public $edit_person;

	/**
	 * Checks that the current user can access the people manager.
	 * Also instantiates a Model_Person object and assigns it to [Boom_Controller_Cms_People::$person]
	 *
	 * @uses Boom_Controller::before()
	 * @uses Boom_Controller::authorization()
	 * @uses Boom_Controller_Cms_People::$edit_person
	 */
	public function before()
	{
		parent::before();

		// Check that we're allowed to be here.
		$this->authorization('manage_people');

		// Set the person to be edited.
		$this->edit_person = new Model_Person($this->request->param('id'));
	}

	/**
	 * Adds a new person to the CMS.
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Person::values()
	 * @uses Model_Person::create()
	 */
	public function action_add()
	{
		if ($this->request->method() === Request::POST)
		{
			$password = Text::random(NULL, 15);
			$enc_password = $this->auth->hash_password($password);

			// POST request - add a person to the CMS.
			$this->edit_person
				->values($this->request->post(), array('name', 'email'))
				->set('password', $enc_password)
				->create()
				->add_group($this->request->post('group_id'));

			Email::factory('CMS Account Created')
				->to($this->edit_person->email)
				->from('support@uxblondon.com')
				->message(View::factory('email/signup', array(
					'password' => $password,
					'person' => $this->edit_person
				)))
				->send();
		}
		else
		{
			// Not a POST request.
			// Display a view to enter the person's email address and select an initial group for the person.
			$this->template = View::factory($this->_view_directory."new", array(
				'groups'	=>	ORM::factory('Group')->names(),
			));
		}
	}

	/**
	 * Add the user to a group.
	 *
	 * If the request method is POST then the user is added into the given groups.
	 * Otherwise a view listing the groups which the user is not a member of is shown.
	 *
	 * @uses Model_Person::add_group()
	 * @uses Boom_Controller::log()
	 */
	public function action_add_group()
	{
		if ($this->request->method() === Request::POST)
		{
			// POST request - add the person to a group.

			// Get an array of group IDs from the POST data.
			$groups = $this->request->post('groups');

			// Loop through the groups.
			foreach ($groups as $group_id)
			{
				// Log the action
				$this->log("Added person $this->person->email to group with ID $group_id");

				// Add the person to the given group.
				$this->edit_person->add_group($group_id);
			}
		}
		else
		{
			// Non-POST request, show a view.

			// Find the groups that this person isn't already a member of.
			$groups = ORM::factory('Group')
				->names(
					DB::Select('group_id')
						->from('people_groups')
						->where('person_id', '=', $this->edit_person->id)
				);

			// Set the response template.
			$this->template = View::factory("$this->_view_directory/addgroup", array(
				'person'	=>	$this->edit_person,
				'groups'	=>	$groups,
			));
		}
	}

	/**
	 * Delete a person.
	 *
	 * @uses Boom_Controller_Cms_People::log()
	 * @uses Model_Person::delete()
	 */
	public function action_delete()
	{
		// Log the action.
		$this->log("Deleted person with email address: ".$this->edit_person->email);

		// Delete the person.
		$this->edit_person->delete();
	}

	/**
	 * Display the people manager.
	 */
	public function action_index()
	{
		$this->template = View::factory("$this->_view_directory/index", array(
			'groups'	=>	ORM::factory('Group')->names(),
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
		$sortby		=	$this->request->query('sortby');

		$query = new Model_Person;

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

		$column = 'name';

		if ( strpos( $sortby, '-' ) > 1 ){
			$sort_params = explode( '-', $sortby );
			$column = $sort_params[0];
			$order = $sort_params[1];
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
			'sortby'		=>	$sortby,
			'order'	=>	$order,
		));

		$pages = ceil($total / 30);

		if ($pages > 1)
		{
			$url = '#tag/' . $this->request->query('tag');
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
	 * @uses Boom_Controller::log()
	 * @uses Model_Person::remove_group()
	 */
	public function action_remove_group()
	{
		// Log the action.
		$this->log("Edited the groups for person ".$this->edit_person->email);

		// Remove the person from the group given in the POST data.
		$this->edit_person->remove_group($this->request->post('group_id'));
	}

	/**
	 * Save changes to an existing person.
	 *
	 * @uses Boom_Controller::log()
	 * @uses Model_Person::values()
	 * @uses Model_Person::update()
	 */
	public function action_save()
	{
		// Log the action.
		$this->log("Edited user $this->edit_person->email (ID: $this->edit_person->id) to the CMS");

		// Update the person's details.
		$this->edit_person
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
		if ( ! $this->edit_person->loaded())
		{
			// No they don't, throw an exception.
			throw new HTTP_Exception_404;
		}

		// Show the person's details.
		$this->template = View::factory($this->_view_directory."view", array(
			'person'		=>	$this->edit_person,
			'request'		=>	$this->request,
			'activities'	=>	$this->edit_person->logs->order_by('time', 'desc')->limit(50)->find_all(),
			'groups'		=>	$this->edit_person->groups->order_by('name', 'asc')->find_all(),
		));
	}
}
