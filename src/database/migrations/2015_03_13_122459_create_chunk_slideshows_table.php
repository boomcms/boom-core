<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChunkSlideshowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunk_slideshows', function (Blueprint $table) {
            $table->string('title', 25);
            $table->increments('id');
            $table->string('slotname', 50)->nullable();
            $table->integer('page_vid')->unsigned()->nullable()->index('page_vid');
            $table->unique(['slotname', 'page_vid'], 'slotname');
            $table->integer('page_id')->unsigned();
            $table->index(['page_id', 'page_vid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chunk_slideshows');
    }
}
