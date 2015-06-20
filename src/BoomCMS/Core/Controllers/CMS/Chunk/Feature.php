<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use Illuminate\Support\Facades\View;

class Feature extends Chunk
{
    public function edit()
    {
        return View::make('boom::editor.chunk.feature', [
            'page' => $this->page,
        ]);
    }
}
