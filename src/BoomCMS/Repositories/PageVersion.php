<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Contracts\Repositories\PageVersion as PageVersionRepositoryInterface;
use BoomCMS\Database\Models\PageVersion as Model;
use BoomCMS\Foundation\Repository;
use BoomCMS\Support\Facades\Chunk;

class PageVersion extends Repository implements PageVersionRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function history(PageModelInterface $page)
    {
        return $this->model
            ->where(Model::ATTR_PAGE, $page->getId())
            ->orderBy(Model::ATTR_CREATED_AT, 'desc')
            ->with('editedBy')
            ->get();
    }

    /**
     * Find a version by page and version ID.
     *
     * @param PageModelInterface $page
     * @param type               $versionId
     *
     * @return Model
     */
    public function find(PageModelInterface $page, $versionId)
    {
        return $this->model
            ->where(Model::ATTR_PAGE, $page->getId())
            ->where(Model::ATTR_ID, $versionId)
            ->first();
    }

    /**
     * Restore an older version of a page.
     *
     * Creates a new version based on the old one.
     *
     * @param Model $version
     *
     * @return Model
     */
    public function restore(Model $version)
    {
        $newVersion = new Model($version->toArray());
        $newVersion->setRestoredFrom($version)->save();

        $types = Chunk::since($version);

        foreach ($types as $type => $chunks) {
            $className = Chunk::getModelName($type);

            foreach ($chunks as $chunk) {
                $old = Chunk::find($type, $chunk->slotname, $version);
                $attrs = ($old === null) ? [] : array_except($old->toArray(), ['id', 'page_vid']);

                $attrs['page_id'] = $newVersion->getPageId();
                $attrs['slotname'] = $chunk->slotname;
                $attrs['page_vid'] = $newVersion->getId();

                $className::create($attrs);
            }
        }

        return $newVersion;
    }
}
