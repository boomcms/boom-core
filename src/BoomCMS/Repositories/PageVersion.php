<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Database\Models\PageVersion as Model;
use BoomCMS\Support\Facades\Chunk;

class PageVersion
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

    public function find(PageModelInterface $page, $versionId)
    {
        return $this->model
            ->where(Model::ATTR_PAGE, $page->getId())
            ->where(Model::ATTR_ID, $versionId)
            ->first();
    }

    public function restore(Model $version): Model
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
