<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToChunkSlideshowSlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chunk_slideshow_slides', function (Blueprint $table) {
            $table->foreign('asset_id', 'chunk_slideshow_slides_ibfk_1')->references('id')->on('assets')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('chunk_id', 'chunk_slideshow_slides_ibfk_2')->references('id')->on('chunk_slideshows')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chunk_slideshow_slides', function (Blueprint $table) {
            $table->dropForeign('chunk_slideshow_slides_ibfk_1');
            $table->dropForeign('chunk_slideshow_slides_ibfk_2');
        });
    }
}
