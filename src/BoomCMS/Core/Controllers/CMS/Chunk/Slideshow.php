<?php

class Controller_Cms_Chunk_Slideshow extends Controller_Cms_Chunk
{
    protected $_type = 'slideshow';

    public function edit()
    {
        $chunk = Chunk::find('slideshow', $this->request->query('slotname'), $this->page->getCurrentVersion());

        $this->template = View::factory('boom/editor/slot/slideshow', [
            'slides' => $chunk->slides(),
        ]);
    }

    protected function _preview_chunk()
    {
        $chunk = new \Boom\Chunk\Slideshow($this->page, $this->_model, $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }

    protected function _preview_default_chunk()
    {
        $model = new Model_Chunk_Slideshow();

        $chunk = new \Boom\Chunk\Slideshow($this->page, $model, $this->request->input('slotname'));
        $chunk->template($this->request->input('template'));

        return $chunk->execute();
    }

    protected function _save_chunk()
    {
        $chunk = parent::_save_chunk();
        $chunk
            ->slides($this->request->input('slides'))
            ->save_slides();
    }
}
