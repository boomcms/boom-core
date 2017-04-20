<?php

use BoomCMS\Database\Models\Chunk\Linkset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateFeatureChunksToLinkset extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $features = DB::table('chunk_features')
            ->get()
            ->toArray();

        foreach ($features as $feature) {
            $feature = (array) $feature;

            $slotname = $feature['slotname'];

            $exists = Linkset::where('slotname', $slotname)
                ->where('page_id', $feature['page_id'])
                ->exists();

            if ($exists === true) {
                $slotname = 'feature-'.$slotname;
            }

            Linkset::create([
                'slotname'       => $slotname,
                'page_vid'       => $feature['page_vid'],
                'page_id'        => $feature['page_id'],
                'links'          => [
                    ['target_page_id' => $feature['target_page_id']],
                ],
            ]);
        }

        Schema::drop('chunk_features');

        DB::table('page_versions')
            ->whereIn('chunk_type', ['feature', 'link'])
            ->update([
                'chunk_type' => 'linkset',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
