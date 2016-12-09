<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChunkTimestampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunk_timestamps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('timestamp')->unsigned()->default(0)->index('timestamp');
            $table->string('format', 15);
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
        Schema::drop('chunk_timestamps');
    }
}
