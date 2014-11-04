<?php

namespace Boom\Tag;

use \DB as DB;

class Tag
{
    /**
	 *
	 * @var \Model_Tag
	 */
    protected $model;

    public function __construct(\Model_Tag $model)
    {
        $this->model = $model;
    }

    public function getId()
    {
        return $this->model->id;
    }

    public function getName()
    {
        return $this->model->name;
    }

    public function addToAssets(array $assetIds)
    {
        foreach ($assetIds as $id) {
            try {
                DB::insert('assets_tags', ['asset_id', 'tag_id'])
                    ->values([$id, $this->getId()])
                    ->execute();
            } catch (Database_Exception $e) {}
        }
    }

    public function addToPages(array $pageIds)
    {
        foreach ($pageIds as $id) {
            try {
                // Have to do this as individual queries rather than a single query with multiple values incase the tag is already applied to some of the objects.
                DB::insert('pages_tags', ['page_id', 'tag_id'])
                    ->values([$id, $this->getId()])
                    ->execute();
            } catch (Database_Exception $e) {}
        }
    }

    public function removeFromAssets(array $assetIds)
    {
        if ( ! empty($assetIds)) {
            DB::delete('assets_tags')
                ->where('tag_id', '=', $this->getId())
                ->where('asset_id', 'in', $assetIds)
                ->execute();
        }

        return $this;
    }

    public function removeFromPages(array $pageIds)
    {
        if ( ! empty($pageIds)) {
            DB::delete('pages_tags')
                ->where('tag_id', '=', $this->getId())
                ->where('page_id', 'in', $pageIds)
                ->execute();
        }

        return $this;
    }
}
