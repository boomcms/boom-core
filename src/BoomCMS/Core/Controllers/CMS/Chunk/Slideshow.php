<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use BoomCMS\Core\Facades\Chunk as ChunkFacade;
use Illuminate\Support\Facades\View;

class Slideshow extends Chunk
{
    protected $_type = 'slideshow';

    public function edit()
    {
        $chunk = ChunkFacade::get('slideshow', $this->request->query('slotname'), $this->page);

        return View::make('boom::editor.chunk.slideshow', [
            'slides' => $chunk->getSlides(),
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
