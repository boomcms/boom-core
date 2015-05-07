<?php

class Controller_Cms_Chunk_Asset extends Controller_Cms_Chunk
{
    protected $_type = 'asset';

    protected function _preview_chunk()
    {
        $chunk = new \Boom\Chunk\Asset($this->page, $this->_model, $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }

    protected function _preview_default_chunk()
    {
        $chunk = new \Boom\Chunk\Asset($this->page, new Model_Chunk_Asset(), $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }

    public function edit()
    {
        $chunk = Chunk::factory('asset', $this->request->query('slotname'), $this->page);

        return View::make('boom/editor/slot/asset', [
            'chunk' => $chunk,
        ]);
    }
}
