<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package 	Boom
 * @category 	Controllers
 */
class Boom_Controller_Page_Html extends Controller_Page
{
	protected $_page_content;

	public function before()
	{
		parent::before();

		// Set some variables which need to be used globally in the views.
		View::bind_global('auth', $this->auth);
		View::bind_global('editor', $this->editor);
		View::bind_global('page', $this->page);
	}

	public function action_show()
	{
		$this->_page_content = $this->_render_template();
	}

	protected function _render_template($template_vars = array())
	{
		$template = $this->page->version()->template;

		return View::factory(Model_Template::DIRECTORY.$template->filename, $template_vars)->render();
	}

	public function after()
	{
		// If we're in the CMS then add the boom editor the the page.
		if ($this->auth->logged_in())
		{
			$this->_page_content = $this->editor->insert($this->_page_content, $this->page->id);
		}

		$this->response->body($this->_page_content);
	}
}