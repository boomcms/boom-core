<?php

namespace BoomCMS\Core\Controllers\CMS\Chunk;

use BoomCMS\Core\Tag\Provider as TagProvider;
use Illuminate\Support\Facades\View;

class Tag extends Chunk
{
    public function edit()
    {
        $provider = new TagProvider();

        return View::make('boom::editor.chunk.tag', [
            'current_tag' => $provider->findByName($this->request->input('tag')),
        ]);
    }
}
