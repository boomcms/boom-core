<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChunkCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunk_calendars', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('page_vid')
                ->unsigned()
                ->nullable()
                ->references('id')
                ->on('page_versions')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->integer('page_id')
                ->unsigned()
                ->references('id')
                ->on('pages')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->string('slotname', 50)->nullable();
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chunk_linksets');
    }
}
