<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMissingIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('create index sites_default on sites(`default`)');
        DB::statement('create index album_asset_asset_id on album_asset(asset_id)');
        DB::statement('create index assets_created_at_asc on assets(created_at asc)');
        DB::statement('create index assets_created_at_desc on assets(created_at desc)');
        DB::statement('create index albums_deleted_at on albums(deleted_at)');
        DB::statement('create index albums_name_asc_deleted_at on albums(name asc, deleted_at)');

        try {
            DB::statement('alter table assets drop index asset_v_rid');
        } catch (\Exception $e) {
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table sites drop index sites_default');
        DB::statement('alter table album_asset drop index album_asset_asset_id');
        DB::statement('alter table assets drop index assets_created_at_asc');
        DB::statement('alter table assets drop index assets_created_at_desc');
        DB::statement('alter table albums drop index albums_deleted_at');
        DB::statement('alter table albums drop index albums_name_asc_deleted_at');
    }
}
