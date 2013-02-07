<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller for viewing an editing page tags.
 *
 * @package BoomCMS
 * @category Controllers
 * @author Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Page_Tags extends Controller_Cms_Page
{
	public function before()
	{
		parent::before();

		// Before allowing viewing or editing of page tags check for that the current user has the 'edit_page' role for this page.
		$this->authorization('edit_page', $this->page);
	}

	/**
	 * Add a tag to the current page.
	 *
	 * @uses Model_Page::add_tag_with_path()
	 */
	public function action_add()
	{
		// Call [Model_Page::add_tag_with_path()] with the tag path given in the POST data.
		$this->page->add_tag_with_path($this->request->post('tag'));
	}

	/**
	 * List the tags current assigned to a page for editing.
	 *
	 * @uses Model_Page::get_tags()
	 */
	public function action_list()
	{
		// Show the tag editor with the tags current assigned to this page.
		$this->template = View::factory("boom/tags/list", array(
			'tags'	=>	$this->page->tags->find_all(),
		));
	}

	/**
	 * Remove a tag from the current page.
	 *
	 * @uses Model_Page::remove_tag_with_path()
	 */
	public function action_remove()
	{
		// Call [Model_Page::remove_tag_with_path()] with the tag path given in the POST data.
		$this->page->remove_tag_with_path($this->request->post('tag'));
	}
} // End Boom_Controller_Cms_Page_Tags