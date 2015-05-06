<?php

class Controller_Cms_Chunk_Feature extends Controller_Cms_Chunk
{
    protected $_type = 'feature';

    public function edit()
    {
        $this->template = View::factory('boom/editor/slot/feature', [
            'page'    =>    $this->page,
        ]);
    }

    protected function _preview_chunk()
    {
        $chunk = new \Boom\Chunk\Feature($this->page, $this->_model, $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }

    protected function _preview_default_chunk()
    {
        $chunk = new \Boom\Chunk\Feature($this->page, new Model_Chunk_Feature(), $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }
}
