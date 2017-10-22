<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddLocalFilenameToAssetVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_versions', function (Blueprint $table) {
            $table->string('path');
            $table->string('filesystem', 30);
            $table->index(['filesystem', 'path']);
        });

        DB::statement('update asset_versions set filesystem = "boomcms-assets"');
        DB::statement('update asset_versions set path = id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_versions', function (Blueprint $table) {
            $table->dropColumn('path');
            $table->dropColumn('filesystem');
        });
    }
}
