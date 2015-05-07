<?php

class Controller_Cms_Chunk extends Boom\Controller
{
    /**
	 * @var ORM
	 */
    protected $_model;

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

        $this->page =  \Boom\Page\Factory::byId($this->request->param('page_id'));
    }

    public function insert_url()
    {
        return View::make('boom/editor/slot/insert_link');
    }

    public function remove()
    {
        $this->authCheck();
        $this->_createVersion();
        $this->page->getTemplate()->onPageSave($this->page);

        $this->_send_response($this->_preview_default_chunk());
    }

    public function save()
    {
        $this->authCheck();
        $this->_createVersion();
        $this->_save_chunk();
        $this->page->getTemplate()->onPageChunkSave($this->page, $this->_model);

        $this->_send_response($this->_preview_chunk());
    }

    public function authCheck()
    {
        $this->page->wasCreatedBy($this->person) || parent::authorization('edit_page_content', $this->page);
    }

    protected function _createVersion()
    {
        $old_version = $this->page->getCurrentVersion();

        $this->_new_version = $this->page->createVersion($old_version, ['edited_by' => $this->person->getId()]);

        if ($this->_new_version->embargoed_until <= $_SERVER['REQUEST_TIME']) {
            $this->_new_version->embargoed_until = null;
        }

        $this->_new_version
            ->create()
            ->copy_chunks($old_version, [$this->_type => [$this->request->input('slotname')]]);
    }

    protected function _preview_chunk() {}

    protected function _save_chunk()
    {
        return $this->_model = ORM::factory("Chunk_".ucfirst($this->_type))
            ->values($this->request->input())
            ->set('page_vid', $this->_new_version->id)
            ->create();
    }

    protected function _send_response($html)
    {
        $this->response->body(json_encode([
            'status' => $this->_new_version->status(),
            'html' => $html,
        ]));
    }
}
