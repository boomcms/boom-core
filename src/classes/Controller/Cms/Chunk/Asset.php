<?php

class Controller_Cms_Chunk_Asset extends Controller_Cms_Chunk
{
    protected $_type = 'asset';

    protected function _preview_chunk()
    {
        $chunk = new \Boom\Chunk\Asset($this->page, $this->_model, $this->request->post('slotname'));
        $chunk->template($this->request->post('template'));

        return $chunk->execute();
    }

    protected function _preview_default_chunk()
    {
        $chunk = new \Boom\Chunk\Asset($this->page, new Model_Chunk_Asset(), $this->request->post('slotname'));
        $chunk->template($this->request->post('template'));

        return $chunk->execute();
    }

    public function action_edit()
    {
        $chunk = Chunk::factory('asset', $this->request->query('slotname'), $this->page);

        $this->template = new View('boom/editor/slot/asset', array(
            'chunk' => $chunk,
        ));
    }
}
