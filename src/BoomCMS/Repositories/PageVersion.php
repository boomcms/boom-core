<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageModelInterface;
use BoomCMS\Contracts\Repositories\PageVersion as PageVersionRepositoryInterface;
use BoomCMS\Database\Models\PageVersion as Model;
use BoomCMS\Support\Facades\Chunk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use DateTime;

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
            ->orderBy(Model::ATTR_EDITED_AT, 'desc')
            ->with('editedBy')
            ->get();
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

    /**
     * Find a version by page and version ID
     *
     * @param PageModelInterface $page
     * @param type $versionId
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

        // For all the chunks that have changed since the old version:
        // Get the chunk as it was at the old version with Chunk::find()
        // If the chunk existed then save a new chunk with the old content.
        // If it didn't exist then save a new chunk with no content.
        // We only need chunk::since to return an array of [type => slotnames] rather than all chunk data.

        foreach ($types as $type => $chunks) {
            foreach ($chunks as $chunk) {
                $new = clone $chunk;
                $new->page_vid = $newVersion->getId();
                $new->save();

                if ($type === 'slideshow') {
                    $slides = $chunk->slides->get();

                    foreach ($slides as $slide) {
                        $newSlide = clone $slide;
                        $newSlide->chunk_id = $new->getId();
                        $newSlide->save();
                    }
                }

                if ($type === 'linkset') {
                    $links = $chunk->links->get();

                    foreach ($links as $link) {
                        $newLink = clone $link;
                        $newLink->chunk_id = $new->getId();
                        $newLink->save();
                    }
                }
            }
        }

        return $newVersion;
    }
}
