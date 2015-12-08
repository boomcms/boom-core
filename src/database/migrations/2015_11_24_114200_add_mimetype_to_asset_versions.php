<?php

use BoomCMS\Database\Models\AssetVersion;
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

            if ($path) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $path);
                finfo_close($finfo);

                if ($mime) {
                    $v->mimetype = $mime;
                    $v->save();
                }
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
