<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package 	Boom
 * @category 	Controllers
 */
class Boom_Controller_Page_Html extends Controller_Page
{
	/**
	 *
	 * @var View
	 */
	public $template;

	public function before()
	{
		parent::before();

		$this->_save_last_url();
		$template = $this->page->version()->template;
		$this->template = View::factory($template->path());

		// Set some variables which need to be used globally in the views.
		View::bind_global('auth', $this->auth);
		View::bind_global('editor', $this->editor);
		View::bind_global('page', $this->page);
	}

	public function action_show() {}

	public function after()
	{
		// If we're in the CMS then add the boom editor the the page.
		if ($this->auth->logged_in())
		{
			$content = $this->editor->insert((string) $this->template, $this->page->id);
		}
		else
		{
			$content = (string) $this->template;
		}

		$this->response->body($content);
	}
}