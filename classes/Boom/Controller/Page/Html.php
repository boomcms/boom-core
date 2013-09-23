<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package 	Boom
 * @category 	Controllers
 */
class Boom_Controller_Page_Html extends Controller_Page
{
	protected $_chunks = array();

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
		$this->template = View::factory($template->filename());

		$this->_chunks = $this->_load_chunks($this->_chunks);

		$this->_bind_view_globals();
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

	protected function _bind_view_globals()
	{
		View::bind_global('auth', $this->auth);
		View::bind_global('cunks', $this->_chunks);
		View::bind_global('editor', $this->editor);
		View::bind_global('page', $this->page);
		View::bind_global('request', $this->request);
	}

	protected function _load_chunks(array $chunks)
	{
		foreach ($chunks as $type => $slotnames)
		{
			$type = ucfirst($type);
			$class = "Chunk_$type";

			$cs = ORM::factory($class)
				->where('page_vid', '=', $this->page->version->id)
				->where('slotname', 'in', $slotnames)
				->find_all()
				->as_array();

			foreach ($cs as & $c)
			{
				$c = new $class($this->page, $cs, $cs->slotname);
			}
		}

		return $chunks;
	}
}