<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Base controller for editing page and asset tags.
 *
 * @package BoomCMS
 * @category Controllers
 * @author Rob Taylor
 * @copyright	Hoop Associates
 */
abstract class Boom_Controller_Cms_Tags extends Boom_Controller
{
	/**
	 *
	 * @var array
	 */
	public $ids = array();

	/**
	 *
	 * @var Model_Taggable
	 */
	public $model;

	public $tags;

	/**
	 *
	 * @var integer
	 */
	public $type;

	public function action_add()
	{
		$this->model->add_tag_with_name($this->request->post('tag'), $this->ids);
	}

	public function action_list()
	{
		$this->tags = empty($this->ids)? array() : $this->model->list_tags($this->ids);

		$this->template = View::factory("boom/tags/list", array(
			'tags'	=>	$this->tags,
		));
	}

	public function action_remove()
	{
		$this->model->remove_tag_with_name($this->request->post('tag'), $this->ids);
	}
}