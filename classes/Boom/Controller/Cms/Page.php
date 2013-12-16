<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * CMS Page controller
 * Contains methods for adding / saving a page etc.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Page extends Boom_Controller
{
	/**
	 * The directory where views used by this class are stored.
	 *
	 * @var	string
	 */
	protected $_view_directory = 'boom/editor/page';

	/**
	*
	* @var	Model_Page
	*/
	public $page;


	public function before()
	{
		parent::before();

		$this->page = new Model_Page($this->request->param('id'));
	}

	public function action_add()
	{
		$this->_csrf_check() AND $this->authorization('add_page', $this->page);

		$creator = new Page_Creator($this->page, $this->person);
		$creator->set_template_id($this->request->post('template_id'));
		$creator->set_title($this->request->post('title'));
		$page = $creator->execute();

		$this->log("Added a new page under " . $this->page->version()->title, "Page ID: " . $page->id);

		$this->response->body(json_encode(array(
			'url' => $page->url(),
			'id' => $page->id,
		)));
	}

	/**
	 * Delete page controller.
	 * This is a dual function controller. If requested via GET a confirmation dialogue is displayed.
	 * If requested via POST the page is deleted using Model_Page::delete().
	 *
	 * @uses	Model_Version_Page::delete()
	 * @uses	Model_Version_Page::parent();
	 */
	public function action_delete()
	{
		if ( ! ($this->page->was_created_by($this->person) OR $this->auth->logged_in('delete_page', $this->page)) OR $this->page->mptt->is_root())
		{
			throw new HTTP_Exception_403;
		}

		if ($this->request->method() === Request::GET)
		{
			// Get request
			// Show a confirmation dialogue warning that child pages will become inaccessible and asking whether to delete the children.
			$this->template = View::factory("$this->_view_directory/delete", array(
				'count'	=>	$this->page->mptt->count(),
				'page'	=>	$this->page,
			));
		}
		else
		{
			$this->_csrf_check();

			// Log the action.
			$this->log("Deleted page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Redirect to the parent page after we've finished.
			$this->response->body($this->page->parent()->url());

			// Are we deleting child pages?
			$with_children = ($this->request->post('with_children') == 1);

			// Delete the page.
			$this->page->delete($with_children);
		}
	}

	public function action_discard()
	{
		$this->page->remove_drafts();
	}

	/**
	 * Reverts the current page to the last published version.
	 *
	 * @uses	Model_Page::stash()
	 */
	public function action_stash()
	{
		// Call Model_Page::stash() on the current page.
		$this->page->stash();
	}
} // End Boom_Controller_Cms_Page