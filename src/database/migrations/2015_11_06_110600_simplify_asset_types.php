<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SimplifyAssetTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('update assets set type = 1 where type = 4');
        DB::statement('update assets set type = 2 where type in (6,7,8)');
        DB::statement('update assets set type = 4 where type  = 5');

        DB::statement('alter table assets drop index asset_v_type');
        DB::statement('alter table assets change type type char(5)');
        DB::statement('update assets set type = "image" where type = 1');
        DB::statement('update assets set type = "doc" where type = 2');
        DB::statement('update assets set type = "video" where type = 3');
        DB::statement('update assets set type = "audio" where type = 4');

        DB::statement('create index assets_type on assets(type)');
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
