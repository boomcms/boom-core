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

        foreach ($this->assetIds as $id) {
            try {
                DB::table('assets_tags')
                    ->insert([
                        'asset_id' => $id,
                        'tag'      => $tag,
                    ]);
            } catch (\Exception $e) {
            }
        }

        return $this;
    }

    public function delete()
    {
        foreach ($this->assetIds as $assetId) {
            $filename = Asset::directory().$assetId;

            file_exists($filename) && unlink($filename);

            foreach (glob($filename.'.*') as $file) {
                unlink($file);
            }
        }

        DB::table('assets')
            ->whereIn('id', $this->assetIds)
            ->delete();
    }

    /**
     * Get an array of tags which are applied to all of the assets in the collection.
     *
     * @return array
     */
    public function getTags()
    {
        return DB::table('assets_tags')
            ->select('tag')
            ->whereIn('asset_id', $this->assetIds)
            ->groupBy('tag')
            ->havingRaw(DB::raw('count(distinct asset_id) ='.count($this->assetIds)))
            ->lists('tag');
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
            ->whereIn('asset_id', $this->assetIds)
            ->delete();

        return $this;
    }
}
