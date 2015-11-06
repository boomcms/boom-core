<?php

namespace BoomCMS\Http\Controllers\CMS\Assets;

use BoomCMS\Core\Asset;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class Tags extends Controller
{
    public function listTags()
    {
        $collection = new Asset\Collection(explode('-', $this->request->route('assets')));

        return View::make('boomcms::assets.tags', [
            'tags' => $collection->getTags(),
        ]);
    }

    public function add()
    {
        $collection = new Asset\Collection($this->request->input('assets'));
        $collection->addTag($this->request->input('tag'));
    }

    public function remove()
    {
        $collection = new Asset\Collection($this->request->input('assets'));
        $collection->removeTag($this->request->input('tag'));
    }
}
