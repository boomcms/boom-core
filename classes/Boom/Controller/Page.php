<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package 	Boom
 * @category 	Controllers
 */
class Boom_Controller_Page extends Boom_Controller
{
	protected $_cache_html;

	/**
	 * Whether the editor should be enabled
	 * This is mainly used for rendering the page in HTML format where the editor toolbar will be inserted into the site HTML.
	 * However it's also used for other formats to allow viewing a previous version of the page.
	 *
	 * @var	boolean
	 */
	public $editable = FALSE;

	/**
	 * @var	Model_Page
	 *
	 */
	public $page;

	/**
	 * Set the page and options properties.
	 */
	public function before()
	{
		// Inherit from parent.
		parent::before();

		// Assign the page we're viewing to Boom_Controller_Page::$_page;
		$this->page = $this->request->param('page');
		$this->editable = $this->_page_should_be_editable();

		if ( ! $this->_page_isnt_visible_to_current_user())
		{
			throw new HTTP_Exception_404;
		}

		$this->_cache_html = $this->auth->logged_in();
	}

	protected function _page_should_be_editable()
	{
		return ($this->editor->state_is(Editor::EDIT) AND ($this->page->was_created_by($this->person) OR $this->auth->logged_in('edit_page', $this->page)));
	}

	protected function _page_isnt_visible_to_current_user()
	{
		// If the page shouldn't be editable then check that it's visible.
		if ( ! $this->editable)
		{
			if ($this->request->is_external() AND ( ! $this->page->is_visible() AND ! $this->editor->state_is(Editor::PREVIEW)))
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}