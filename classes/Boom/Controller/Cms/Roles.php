<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Roles controller
 *
 * Lists so that they can be assigned to a group.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Roles extends Boom_Controller
{
	/**
	 *
	 * @var	string	Directory where the views which relate to this class are held.
	 */
	protected $_view_directory = 'boom/roles';

	/**
	 * A role model (lol) to run queries against.
	 *
	 * @var Model_Roles
	 */
	public $roles;

	/**
	 * Checks that the current user can access the people manager.
	 *
	 * Also instantiates a Model_Role object and assigns it to [Boom_Controller_Cms_Roles::$roles] for building queries.
	 *
	 * @uses Boom_Controller::before()
	 * @uses Boom_Controller::authorization()
	 * @uses Boom_Controller_Cms_Roles::$roles
	 */
	public function before()
	{
		parent::before();

		// Check that we're allowed to be here.
		$this->authorization('manage_people');

		// Get a Model_Role
		$this->roles = new Model_Role;
	}

	/**
	 * Lists 'general' roles which don't need to be applied to a particular point in the page tree.
	 *
	 * These roles will not have their name prefixed with 'page_'
	 *
	 */
	public function action_general()
	{
		$this->roles->where('name', 'not like', 'page_%');
	}

	/**
	 * Lists 'page' roles which need to be applied to a particular point in the page tree.
	 *
	 * These roles will have their name prefixed with 'page_'
	 *
	 */
	public function action_page()
	{
		$this->roles->where('name', 'like', 'page_%');
	}

	public function after()
	{
		$this->template = View::factory("$this->_view_directory/list", array(
			'roles'	=> $this->roles->find_all(),
		));

		parent::after();
	}
}