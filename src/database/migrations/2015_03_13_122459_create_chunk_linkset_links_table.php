<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChunkLinksetLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunk_linkset_links', function (Blueprint $table) {
            $table->smallInteger('id', true)->unsigned();
            $table->integer('target_page_id')->unsigned()->nullable();
            $table->smallInteger('chunk_linkset_id')->unsigned()->index('linksetlinks_chunk_linkset_id');
            $table->string('url')->nullable();
            $table->string('title', 100)->nullable();
            $table->integer('asset_id')->unsigned()->nullable();
            $table->text('text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chunk_linkset_links');
    }
}
