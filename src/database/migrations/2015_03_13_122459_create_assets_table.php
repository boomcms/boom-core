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
            $table->string('type', 5)->nullable();
            $table->integer('uploaded_by')->unsigned()->nullable()->index('uploaded_by');
            $table->integer('uploaded_time')->unsigned()->nullable();
            $table->integer('thumbnail_asset_id')->unsigned()->nullable();
            $table->string('credits')->nullable();
            $table->bigInteger('downloads')->unsigned()->nullable()->default(0)->index('assets_downloads');
        });

        Schema::create('asset_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asset_id')->unsigned();
            $table->smallInteger('width')->unsigned()->nullable();
            $table->smallInteger('height')->unsigned()->nullable();
            $table->string('filename', 150);
            $table->integer('filesize')->unsigned()->nullable()->default(0)->index('asset_versions_filesize');
            $table->integer('edited_at')->unsigned()->nullable();
            $table->integer('edited_by')
                ->unsigned()
                ->nullable()
                ->references('id')
                ->on('people')
                ->onUpdate('CASCADE')
                ->onDelete('set null');

            $table
                ->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->string('extension', 10);
            $table->string('mimetype', 255);
            $table->text('metadata')->nullable();
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
