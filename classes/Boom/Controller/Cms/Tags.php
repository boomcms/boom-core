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

	/**
	 *
	 * @var integer
	 */
	public $type;

	/**
	 * Add a tag to the current object.
	 *
	 * @uses Model_Taggable::add_tag_with_path()
	 */
	public function action_add()
	{
		// Call [Model_Page::add_tag_with_path()] with the tag path given in the POST data.
		$this->model->add_tag_with_path($this->request->post('tag'), $this->type, $this->ids);
	}

	/**
	 * List the tags current assigned to an asset or page for editing.
	 *
	 */
	public function action_list()
	{
		// Show the tag editor with the tags current assigned to this page.
		$this->template = View::factory("boom/tags/list", array(
			'tags'	=>	$this->model->list_tags($this->ids),
		));
	}

	/**
	 * Remove a tag from the current object.
	 *
	 * @uses Model_Taggable::remove_tag_with_path()
	 */
	public function action_remove()
	{
		// Call [Model_Taggable::remove_tag_with_path()] with the tag path given in the POST data.
		$this->model->remove_tag_with_path($this->request->post('tag'), $this->type, $this->ids);
	}
} // End Boom_Controller_Cms_Page_Tags