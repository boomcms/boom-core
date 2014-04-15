<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_PeopleManager extends Controller_Cms
{
	public function before()
	{
		parent::before();

		$this->authorization('manage_people');
	}

	public function action_index()
	{
		$people = ORM::factory('Person')
			->by_group($this->request->query('group'))
			->order_by('name', 'asc')
			->find_all();

		$this->template = new View("boom/people/list", array(
			'people' => $people
		));
	}

	protected function _show(View $view = null)
	{
		if ( ! $this->request->is_ajax())
		{
			$this->template = View::factory("boom/people/manager", array(
				'groups' => ORM::Factory('Group')->where('deleted', '=', false)->order_by('name', 'asc')->find_all(),
				'content' => $view,
			));
		}
	}

	public function after()
	{
		$this->_show($this->template);

		parent::after();
	}
}