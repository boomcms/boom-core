<?php

namespace BoomCMS\Http\Controllers\Asset;

use BoomCMS\Core\Asset\Collection;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var string
     */
    protected $role = 'manageAssets';

    /**
     * @var string
     */
    protected $tag;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->collection = new Collection($request->input('assets', []));
        $this->tag = $request->input('tag');
    }

    public function listTags()
    {
        return $this->collection->getTags();
    }

    public function add()
    {
        $this->collection->addTag($this->tag);
    }

    public function remove()
    {
        $this->collection->removeTag($this->tag);
    }
}
