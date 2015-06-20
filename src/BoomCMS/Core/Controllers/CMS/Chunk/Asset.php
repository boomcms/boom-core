<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use BoomCMS\Core\Facades\Chunk as ChunkFacade;
use Illuminate\Support\Facades\View;

class Asset extends Chunk
{
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
        $chunk = ChunkFacade::get('asset', $this->request->query('slotname'), $this->page);

        return View::make('boom::editor.chunk.asset', [
            'chunk' => $chunk,
        ]);
    }
}
