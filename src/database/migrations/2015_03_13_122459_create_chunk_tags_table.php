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
        Schema::create('chunk_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slotname', 50);
            $table->integer('page_vid')->unsigned();
            $table->string('tag', 50);
            $table->unique(['page_vid', 'slotname'], 'chunk_tags_page_vid_slotname');
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
