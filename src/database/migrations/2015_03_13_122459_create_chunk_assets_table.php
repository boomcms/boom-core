<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChunkAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunk_assets', function (Blueprint $table) {
            $table->integer('asset_id')->unsigned()->nullable()->index('asset_id');
            $table->text('caption', 65535)->nullable();
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('slotname', 50)->nullable();
            $table->integer('page_vid')->unsigned()->nullable()->index('page_vid');
            $table->unique(['slotname','page_vid'], 'slotname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chunk_assets');
    }
}
