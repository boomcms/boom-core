<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Core\Asset;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Asset as AssetFacade;

class Tags extends Controller
{
    protected $role = 'manageAssets';

    public function listTags()
    {
        return AssetFacade::tags();
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
