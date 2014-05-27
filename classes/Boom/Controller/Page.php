<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package 	Boom
 * @category 	Controllers
 */
class Boom_Controller_Page extends Boom_Controller
{
	/**
	 * Whether the editor should be enabled
	 * This is mainly used for rendering the page in HTML format where the editor toolbar will be inserted into the site HTML.
	 * However it's also used for other formats to allow viewing a previous version of the page.
	 *
	 * @var	boolean
	 */
	public $editable = false;

	/**
	 * @var	Model_Page
	 *
	 */
	public $page;

	protected $_save_last_url = true;

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
	}

	protected function _page_should_be_editable()
	{
		return ($this->editor->isEnabled() && ($this->page->wasCreatedBy($this->person) || $this->auth->logged_in('edit_page', $this->page)));
	}

	protected function _page_isnt_visible_to_current_user()
	{
		// If the page shouldn't be editable then check that it's visible.
		if ( ! $this->editable)
		{
			if ($this->request->is_external() && ( ! $this->page->isVisible() && ! $this->editor->hasState(\Boom\Editor::PREVIEW)))
			{
				return false;
			}
		}

		return true;
	}
}