<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetsTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets_tags', function (Blueprint $table) {
            $table->integer('asset_id')->unsigned()->default(0);
            $table->string('tag', 50);
            $table->unique(['tag', 'asset_id'], 'assets_tags_tag_asset_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assets_tags');
    }
}
