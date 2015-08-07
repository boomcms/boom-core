<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToChunkAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chunk_assets', function (Blueprint $table) {
            $table->foreign('page_vid', 'chunk_assets_ibfk_1')->references('id')->on('page_versions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('asset_id', 'chunk_assets_ibfk_2')->references('id')->on('assets')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chunk_assets', function (Blueprint $table) {
            $table->dropForeign('chunk_assets_ibfk_1');
            $table->dropForeign('chunk_assets_ibfk_2');
        });
    }
}
