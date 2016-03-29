<?php

use BoomCMS\Database\Models\AssetVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMetaDataToAssetVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_versions', function (Blueprint $table) {
            $table->text(AssetVersion::ATTR_METADATA)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_versions', function (Blueprint $table) {
            $table->dropColumn(AssetVersion::ATTR_METADATA);
        });
    }
}
