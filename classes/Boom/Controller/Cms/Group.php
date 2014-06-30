<?php

use \Boom\Group as Group;

class Boom_Controller_Cms_Group extends Controller_Cms_PeopleManager
{
	/**
	 * @var string
	 */
	protected $viewDirectory = 'boom/groups';

	/**
	 * @var Model_Group
	 */
	public $group;

	public function before()
	{
		parent::before();

		$this->authorization('manage_people');
		$this->group = Group\Factory::byId($this->request->param('id'));
	}
}