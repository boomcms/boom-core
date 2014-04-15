<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package 	Boom
 * @category 	Controllers
 */
class Boom_Controller_Page_Html extends Controller_Page
{
	protected $_chunks = array();
	protected $_chunk_defaults = array(
		'text' => array(
			'bodycopy' => array('is_block' => true),
			'bodycopy2' => array('is_block' => true),
		),
	);

	/**
	 *
	 * @var View
	 */
	public $template;

	public function before()
	{
		parent::before();

		$template = $this->page->version()->template;
		$this->template = View::factory($template->filename());

		$this->_chunks = $this->_load_chunks($this->_chunks);
		$this->_set_chunk_defaults();

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
		View::bind_global('chunks', $this->_chunks);
		View::bind_global('editor', $this->editor);
		View::bind_global('page', $this->page);
		View::bind_global('request', $this->request);
	}

	protected function _load_chunks(array $chunks)
	{
		foreach ($chunks as $type => $slotnames)
		{
			$class = "Chunk_".ucfirst($type);
			$models = Chunk::find($type, $slotnames, $this->page->version());

			$found = array();
			foreach ($models as $model)
			{
				$found[] = $model->slotname;
				$chunks[$type][$model->slotname] = new $class($this->page, $model, $model->slotname);
			}

			$not_found = array_diff($slotnames, $found);

			foreach ($not_found as $slotname)
			{
				$chunks[$type][$slotname] = new $class($this->page, ||M::factory($class), $slotname);
			}
		}

		return $chunks;
	}

	protected function _set_chunk_defaults()
	{
		foreach ($this->_chunk_defaults as $type => $defaults)
		{
			$slotnames = array_keys($defaults);
			foreach ($slotnames as $slotname)
			{
				isset($this->_chunks[$type][$slotname]) && $this->_chunks[$type][$slotname]->defaults($this->_chunk_defaults[$type][$slotname]);
			}
		}
	}
}