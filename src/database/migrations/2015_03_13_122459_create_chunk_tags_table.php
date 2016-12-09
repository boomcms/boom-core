<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChunkTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunk_libraries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slotname', 50);
            $table->integer('page_vid')->unsigned();
            $table->unique(['page_vid', 'slotname'], 'chunk_tags_page_vid_slotname');
            $table->integer('page_id')->unsigned();
            $table->index(['page_id', 'page_vid']);
            $table->text('params', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chunk_tags');
    }
}
