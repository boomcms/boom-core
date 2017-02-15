<?php

use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\FileInfo\Facade as FileInfo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\HttpFoundation\File\File;

class AddAspectRatioToAssetsAndImportMetadata extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_versions', function (Blueprint $table) {
            $table->float('aspect_ratio', 8, 3)
                ->nullable()
                ->default(null);
        });

        $versions = AssetVersion::all();

        foreach ($versions as $version) {
            $path = storage_path().'/boomcms/assets/'.$version->id;

            if (is_readable($path)) {
                $info = FileInfo::create(new File($path));

                $version->fill([
                    'aspect_ratio' => $info->getAspectRatio(),
                    'width'        => $info->getWidth(),
                    'height'       => $info->getHeight(),
                    'metadata'     => $info->getMetadata(),
                ])->save();
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
        Schema::table('asset_versions', function (Blueprint $table) {
            $table->dropColumn('aspect_ratio');
        });
    }
}
