<?php

use BoomCMS\Database\Models\AssetVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateAssetVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
            $table->text(AssetVersion::ATTR_METADATA)->nullable();
        });

        DB::statement('insert into asset_versions (id, asset_id, width, height, filename, filesize, edited_at, edited_by) select id, id, width, height, filename, filesize, uploaded_time, uploaded_by from assets');

        Schema::table('assets', function ($table) {
            $table->dropColumn('width');
            $table->dropColumn('height');
            $table->dropColumn('filename');
            $table->dropColumn('filesize');
            $table->dropColumn('duration');
            $table->dropColumn('last_modified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('asset_versions');
    }
}
