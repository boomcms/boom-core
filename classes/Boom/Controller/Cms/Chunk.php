<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Controllers
 */
abstract class Boom_Controller_Cms_Chunk extends Boom_Controller
{
	/**
 	 * @var Model_Page
	 */
	protected $page;

	/**
	 * @var Model_Page_Version
	 */
	protected $_new_version;

	/**
	 * @var string
	 */
	protected $_type;

	public function before()
	{
		parent::before();

		$this->page = new Model_Page($this->request->param('page_id'));

		$this->page->was_created_by($this->person) OR $this->authorization('edit_page_content', $this->page);
	}

	public function action_insert_url()
	{
		$this->template = View::factory('boom/editor/slot/insert_link');
	}

	public function action_remove()
	{
		$this->_create_version();
		$this->_preview_default_chunk();
	}

	public function action_save()
	{
		$this->_create_version();
		$this->_save_chunk();
		$this->_preview_chunk();
	}

	protected function _create_version()
	{
		$old_version = $this->page->version();

		$this->_new_version = $this->page->create_version($old_version, array('edited_by' => $this->person->id))
			->create()
			->copy_chunks($old_version, array($this->request->post('slotname')));
	}

	abstract protected function _preview_chunk();

	protected function _save_chunk()
	{
		return $this->_new_version->add_chunk($this->_type, $this->request->post('slotname'), $this->request->post());
	}
}