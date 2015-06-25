<?php

class Controller_Cms_Chunk_Linkset extends Controller_Cms_Chunk
{
    protected $_type = 'linkset';

    public function edit()
    {
        $chunk = Chunk::find('linkset', $this->request->query('slotname'), $this->page->getCurrentVersion());

        return View::make('boom/editor/slot/linkset', [
            'links' => $chunk->links(),
            'title' => $chunk->title
        ]);
    }
}
