<?php

namespace BoomCMS\Core\Page\Finder;

use Boom\Model\Page as Model;

class ParentId extends \Boom\Finder\Filter
{
    protected $parentId;

    public function __construct($parentId)
    {
        $this->parentId = $parentId;
    }

    public function execute(Model $query)
    {
        return $query
            ->join('page_mptt', 'inner')
            ->on('page.id', '=', 'page_mptt.id')
            ->where('page_mptt.parent_id', '=', $this->parentId);
    }
}
