<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChunkTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunk_texts', function (Blueprint $table) {
            $table->text('text', 65535);
            $table->increments('id');
            $table->string('slotname', 50)->nullable();
            $table->integer('page_vid')->unsigned()->nullable()->index('page_vid');
            $table->boolean('is_block')->nullable()->default(0);
            $table->text('site_text', 65535)->nullable();
            $table->unique(['slotname', 'page_vid'], 'slotname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chunk_texts');
    }
}
