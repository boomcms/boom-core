<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DropChunkTextAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('chunk_text_assets');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('chunk_text_assets', function (Blueprint $table) {
            $table->integer('chunk_id')->unsigned()->nullable()->index('chunk_text_assets_chunk_id');
            $table->smallInteger('asset_id')->nullable();
            $table->smallInteger('position')->unsigned()->nullable();
            $table->unique(['chunk_id','asset_id'], 'chunk_text_assets');
        });
    }
}
