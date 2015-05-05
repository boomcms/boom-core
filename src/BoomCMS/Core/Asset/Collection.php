<?php

namespace BoomCMS\Core\Asset;

use DB;

/**
 * Defines methods for working with a collection of assets
 *
 */
class Collection
{
    /**
     *
     * @var array
     */
    private $assetIds;

    public function __construct(array $assetIds)
    {
        $this->assetIds = array_unique($assetIds);
    }

    /**
     * Add a tag to all assets in the collection
     *
     * @param string $tag
     * @return \Boom\Asset\Collection
     */
    public function addTag($tag)
    {
        // If an asset already has the tag then a Database_Exception will be thrown due to a unique key on asset_id and tag.
        // Therefore, the tag is added to each asset individually.

        foreach ($this->assetIds as $id) {
            try {
                DB::insert('assets_tags', ['asset_id', 'tag'])
                    ->values(array($id, $tag))
                    ->execute();
            } catch (\Database_Exception $e) {}
        }

        return $this;
    }

    /**
     * Get an array of tags which are applied to all of the assets in the collection.
     *
     * @return array
     */
    public function getTags()
    {
        $results = DB::select('tag')
            ->from('assets_tags')
            ->where('asset_id', 'in', $this->assetIds)
            ->group_by('tag')
            ->having(DB::expr('count(distinct asset_id)'), '>=', count($this->assetIds))
            ->execute()
            ->as_array('tag');

        return array_keys($results);
    }

    /**
     * Remove a tag from all assets in the collection
     *
     * @param string $tag
     * @return \Boom\Asset\Collection
     */
    public function removeTag($tag)
    {
        DB::delete('assets_tags')
            ->where('tag', '=', $tag)
            ->where('asset_id', 'in', $this->assetIds)
            ->execute();

        return $this;
    }
}