<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages_tags', function (Blueprint $table) {
            $table->integer('page_id')->unsigned()->default(0)->index('pages_tags_page_id');
            $table->integer('tag_id')->unsigned()->default(0)->index('pages_tags_tag_id');
            $table->primary(['page_id','tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages_tags');
    }
}
