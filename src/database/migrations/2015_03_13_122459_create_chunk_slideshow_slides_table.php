<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChunkSlideshowSlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunk_slideshow_slides', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('asset_id')->unsigned()->nullable()->index('asset_id');
            $table->string('url', 100)->nullable();
            $table->integer('chunk_id')->unsigned()->nullable()->index('slideshowimages_chunk_id');
            $table->string('caption')->nullable();
            $table->string('title')->nullable();
            $table->text('link_text', 65535)->nullable();
            $table->text('linktext', 65535)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chunk_slideshow_slides');
    }
}
