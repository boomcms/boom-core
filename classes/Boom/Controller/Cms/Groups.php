<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Group controller
 * Pages for managing groups.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
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
}