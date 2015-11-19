<?php

use BoomCMS\Database\Models\Asset\Version;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAssetExtensionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update('alter table asset_versions add extension varchar(10)');

        $versions = Version::all();

        foreach ($versions as $v) {
            preg_match('|\.([a-z]+)$|', $v->filename, $matches);

            if (isset($matches[1])) {
                $v->extension = $matches[1];
                $v->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::update('alter table assets drop extension');
    }
}
