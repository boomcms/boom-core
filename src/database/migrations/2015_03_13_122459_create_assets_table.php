<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 150)->nullable()->index('asset_v_deleted_title_asc');
            $table->text('description', 65535)->nullable();
            $table->smallInteger('width')->unsigned()->nullable();
            $table->smallInteger('height')->unsigned()->nullable();
            $table->string('filename', 150);
            $table->string('type', 100)->nullable()->index('asset_v_type');
            $table->integer('filesize')->unsigned()->nullable()->default(0)->index('asset_v_deleted_filesize_desc');
            $table->integer('duration')->unsigned()->nullable();
            $table->integer('uploaded_by')->unsigned()->nullable()->index('uploaded_by');
            $table->integer('uploaded_time')->unsigned()->nullable();
            $table->integer('last_modified')->unsigned()->nullable();
            $table->integer('thumbnail_asset_id')->unsigned()->nullable();
            $table->string('credits')->nullable();
            $table->bigInteger('downloads')->unsigned()->nullable()->default(0)->index('assets_downloads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assets');
    }
}
