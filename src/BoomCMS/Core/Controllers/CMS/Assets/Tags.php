<?php

namespace BoomCMS\Core\Controllers\CMS\Assets;

use BoomCMS\Core\Controllers\Controller;
use BoomCMS\Core\Asset;

use Illuminate\Support\Facades\View;

class Tags extends Controller
{
    public function listTags()
    {
        $collection = new Asset\Collection(explode('-', $this->request->route('assets')));

        return View::make('boom::assets.tags', [
            'tags' => $collection->getTags()
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
