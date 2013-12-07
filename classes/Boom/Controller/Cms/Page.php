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

	/**
	 * Add a new page to the CMS.
	 */
	public function action_add()
	{
		$this->_csrf_check();

		// Get the parent page and template of the new page from the POST data.
		$parent_id = $this->page->id;
		$template_id = $this->request->post('template_id');
		$title = ($this->request->post('title'))? $this->request->post('title') : 'Untitled';

		if ($template_id == NULL)
		{
			// Work out which template to use for the new page.
			// Priority is the parent page's children_template_id
			// then the grandparent's grandchild_template_id
			// then the parent page template id.
			if ($this->page->children_template_id == 0)
			{
				$grandparent = $this->page->parent();
				$template_id = ($grandparent->grandchild_template_id != 0)? $grandparent->grandchild_template_id : $this->page->version()->template_id;
			}
			else
			{
				$template_id = $this->page->children_template_id;
			}
		}

		// Start a database transaction.
		Database::instance()->begin();

		// Find the parent page.
		$parent = $this->page;

		// Check for add permissions on the parent page.
		$this->authorization('add_page', $parent);

		// Create the new page with nav values inherited from the parent.
		$page = ORM::factory('Page')
			->values(array(
				'visible_in_nav'				=>	$parent->children_visible_in_nav,
				'visible_in_nav_cms'			=>	$parent->children_visible_in_nav_cms,
				'children_visible_in_nav'		=>	$parent->children_visible_in_nav,
				'children_visible_in_nav_cms'	=>	$parent->children_visible_in_nav_cms,
				'visible_from'				=>	$_SERVER['REQUEST_TIME'],
				'created_by'				=>	$this->person->id,
			))
			->create();

		// Create a version for the page.
		ORM::factory('Page_Version')
			->values(array(
				'edited_by'	=>	$this->person->id,
				'page_id'		=>	$page->id,
				'template_id'	=>	$template_id,
				'title'			=>	$title,
				'published' => TRUE,
				'embargoed_until' => $_SERVER['REQUEST_TIME'],
			))
			->create();

		// Set the ID for the page's record in the mptt table to the page_id
		$page->mptt->id = $page->id;

		// Add the page to the MPTT tree.
		$page->mptt->insert_as_last_child($parent->mptt);

		// Generate the link for the page.
		// What is the prefix for the link? If a default default_chinl_link_prefix has been set for the parent then use that, otherwise use the parent's primary link.
		$prefix = ($parent->children_url_prefix)? $parent->children_url_prefix : $parent->url()->location;

		// Generate a link from the prefix and the page's title.
		$url = URL::generate($prefix, $title);

		// Add the link as the primary link for this page.
		ORM::factory('Page_URL')
			->values(array(
				'location'		=>	$url,
				'page_id'		=>	$page->id,
				'is_primary'	=>	TRUE,
			))
			->create();

		$this->log("Added a new page under " . $parent->version()->title, "Page ID: " . $page->id);

		Database::instance()->commit();

		$this->response->body(json_encode(array(
			'url' => URL::site($url),
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