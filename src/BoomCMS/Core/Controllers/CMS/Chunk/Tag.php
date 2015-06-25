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
}
