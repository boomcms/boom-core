<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 */
class Boom_Controller_Cms_Groups extends Boom_Controller
{
	/**
	 * @var string
	 */
	protected $_view_directory = 'boom/groups';

	/**
	 * @var Model_Group
	 */
	public $group;

	public function before()
	{
		parent::before();

		$this->authorization('manage_people');
		$this->group = new Model_Group($this->request->param('id'));
	}

	protected function _show(View $view)
	{
		$this->template = View::factory("boom/people/index", array(
			'groups' => ORM::Factory('Group')->order_by('name', 'asc')->find_all(),
			'content' => $view,
		));
	}
}