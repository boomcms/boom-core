<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Contracts\Repositories\PageVersion as PageVersionRepositoryInterface;
use BoomCMS\Database\Models\PageVersion as Model;
use Illuminate\Database\Eloquent\Builder;

class PageVersion implements PageVersionRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Delete the draft versions for a page.
     *
     * @param PageModelInterface $page
     *
     * @return $this
     */
    public function deleteDrafts(PageModelInterface $page)
    {
        $this->model
            ->where(Model::ATTR_PAGE, $page->getId())
            ->where(function (Builder $query) {
                $query
                    ->whereNull(Model::ATTR_EMBARGOED_UNTIL)
                    ->orWhere(Model::ATTR_EMBARGOED_UNTIL, '>', time());
            })
            ->where(Model::ATTR_EDITED_AT, '>', $page->getLastPublishedTime()->getTimestamp())
            ->delete();

        return $this;
    }
}
