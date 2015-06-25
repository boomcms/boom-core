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
}
