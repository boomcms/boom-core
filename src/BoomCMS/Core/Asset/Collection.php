<?php

namespace BoomCMS\Core\Asset;

use Illuminate\Support\Facades\DB;

/**
 * Defines methods for working with a collection of assets.
 */
class Collection
{
    /**
     * @var array
     */
    private $assetIds;

    public function __construct(array $assetIds)
    {
        $this->assetIds = array_unique($assetIds);

        foreach ($this->assetIds as $i => $assetId) {
            if (!$assetId) {
                unset($this->assetIds[$i]);
            }
        }

        $this->assetIds = array_values($this->assetIds);
    }

    /**
     * Add a tag to all assets in the collection.
     *
     * @param string $tag
     *
     * @return Collection
     */
    public function addTag($tag)
    {
        // If an asset already has the tag then a Database_Exception will be thrown due to a unique key on asset_id and tag.
        // Therefore, the tag is added to each asset individually.

        foreach ($this->getAssetIds() as $assetId) {
            try {
                DB::table('assets_tags')
                    ->insert([
                        'asset_id' => $assetId,
                        'tag'      => $tag,
                    ]);
            } catch (\Exception $e) {
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAssetIds()
    {
        return $this->assetIds;
    }

    /**
     * Get an array of tags which are applied to all of the assets in the collection.
     *
     * @return array
     */
    public function getTags()
    {
        $query = DB::table('assets_tags')
            ->select('tag')
            ->groupBy('tag');

        if (!empty($this->getAssetIds())) {
            $query
                ->whereIn('asset_id', $this->getAssetIds())
                ->havingRaw(DB::raw('count(distinct asset_id) ='.count($this->getAssetIds())));
        }

        return $query->lists('tag');
    }

    /**
     * Remove a tag from all assets in the collection.
     *
     * @param string $tag
     *
     * @return Collection
     */
    public function removeTag($tag)
    {
        DB::table('assets_tags')
            ->where('tag', '=', $tag)
            ->whereIn('asset_id', $this->getAssetIds())
            ->delete();

        return $this;
    }
}
