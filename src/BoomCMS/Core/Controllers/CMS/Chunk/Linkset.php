<?php

class Controller_Cms_Chunk_Linkset extends Controller_Cms_Chunk
{
    protected $_type = 'linkset';

    public function action_edit()
    {
        $chunk = Chunk::find('linkset', $this->request->query('slotname'), $this->page->getCurrentVersion());

        $this->template = View::factory('boom/editor/slot/linkset', [
            'links' => $chunk->links(),
            'title' => $chunk->title
        ]);
    }

    protected function _preview_chunk()
    {
        $chunk = new \Boom\Chunk\Linkset($this->page, $this->_model, $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }

    protected function _preview_default_chunk()
    {
        $model = new Model_Chunk_Linkset();

        $chunk = new \Boom\Chunk\Linkset($this->page, $model, $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }

    protected function _save_chunk()
    {
        $chunk = parent::_save_chunk();
        $chunk
            ->links($this->request->input('links'))
            ->save_links();
    }
}
