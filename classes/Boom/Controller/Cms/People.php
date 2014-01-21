<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_People extends Controller_Cms
{
	/**
	 * @var string Directory where the views which relate to this class are held.
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

	public function before()
	{
		parent::before();

		$this->authorization('manage_people');
		$this->edit_person = new Model_Person($this->request->param('id'));
	}

	protected function _show(View $view)
	{
		$this->template = View::factory("$this->_view_directory/index", array(
			'groups' => ORM::Factory('Group')->order_by('name', 'asc')->find_all(),
			'content' => $view,
		));
	}
}