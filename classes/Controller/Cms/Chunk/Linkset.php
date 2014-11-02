<?php

class Controller_Cms_Chunk_Linkset extends Controller_Cms_Chunk
{
    protected $_type = 'linkset';

    public function action_edit()
    {
        $this->template = View::factory('boom/editor/slot/linkset', array(
            'page'    =>    $this->page,
        ));
    }

    protected function _preview_chunk()
    {
        $chunk = new \Boom\Chunk\Linkset($this->page, $this->_model, $this->request->post('slotname'));
        $chunk->template($this->request->post('template'));

        return $chunk->execute();
    }

    protected function _preview_default_chunk()
    {
        $model = new Model_Chunk_Linkset();

        $chunk = new \Boom\Chunk\Linkset($this->page, $model, $this->request->post('slotname'));
        $chunk->template($this->request->post('template'));

        return $chunk->execute();
    }

    protected function _save_chunk()
    {
        $chunk = parent::_save_chunk();
        $chunk
            ->links($this->request->post('links'))
            ->save_links();
    }
}
