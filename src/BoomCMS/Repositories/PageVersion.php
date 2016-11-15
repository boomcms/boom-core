<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Contracts\Repositories\PageVersion as PageVersionRepositoryInterface;
use BoomCMS\Database\Models\PageVersion as Model;
use BoomCMS\Support\Facades\Chunk;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
        $attrs = $version->toArray();

        $newVersion = new Model($attrs);
        $newVersion
            ->setEditedAt(new DateTime('now'))
            ->setEditedBy(Auth::user())
            ->setRestoredFrom($version)
            ->save();

        $types = Chunk::since($version);

        foreach ($types as $type => $chunks) {
            $className = Chunk::getModelName($type);

            foreach ($chunks as $chunk) {
                $old = Chunk::find($type, $chunk->slotname, $version);

                $new = new $className();
                $new->page_id = $newVersion->getPageId();
                $new->slotname = $chunk->slotname;
                $new->page_vid = $newVersion->getId();
                $new->save();

                if ($old !== null) {
                    $new->fill(array_except($old->toArray(), ['page_vid']));
                    $new->save();
                }
            }
        }

        return $newVersion;
    }
}
