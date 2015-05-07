<?php

class Controller_Cms_Chunk_Tag extends Controller_Cms_Chunk
{
    protected $_type = 'tag';

    public function edit()
    {
        return View::make('boom/editor/slot/tag', [
            'current_tag' => new Model_Tag($this->request->query('tag')),
        ]);
    }

    protected function _preview_chunk()
    {
        $chunk = new \Boom\Chunk\Tag($this->page, $this->_model, $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }

    protected function _preview_default_chunk()
    {
        $chunk = new \Boom\Chunk\Tag($this->page, new Model_Chunk_Tag(), $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }
}
