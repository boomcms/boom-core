<?php

use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Support\File;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMimeTypeToAssetVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table asset_versions add mimetype varchar(255)');

        $versions = AssetVersion::all();
        $directory = storage_path('boomcms/assets');

        foreach ($versions as $v) {
            $path = realpath($directory.DIRECTORY_SEPARATOR.$v->id);
            $mime = File::mime($path);

            if ($mime) {
                $v->mimetype = $mime;
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
        DB::statement('alter table asset_versions drop mimetype');
    }
}
