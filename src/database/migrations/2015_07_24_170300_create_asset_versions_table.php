<?php

use BoomCMS\Core\Models\Asset;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
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
            $table
                ->integer('asset_id')
                ->unsigned()
                ->references('id')
                ->on('assets')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->smallInteger('width')->unsigned()->nullable();
            $table->smallInteger('height')->unsigned()->nullable();
            $table->string('filename', 150);
            $table->string('type', 100)->nullable()->index('asset_v_type');
            $table->integer('filesize')->unsigned()->nullable()->default(0)->index('asset_versions_filesize_desc');
            $table->integer('duration')->unsigned()->nullable();
            $table->integer('created_at')->unsigned()->nullable();
            $table->integer('created_by')
                ->unsigned()
                ->nullable()
                ->references('id')
                ->on('people')
                ->onUpdate('CASCADE')
                ->onDelete('set null');
        });

        foreach (Asset::all() as $asset) {
            Asset\Version::create([
                'asset_id' => $asset->id,
                'width' => $asset->width,
                'height' => $asset->height,
                'filename' => $asset->filename,
                'type' => $asset->type,
                'filesize' => $asset->filesize,
                'duration' => $asset->duration,
                'created_at' => $asset->uploaded_time,
                'created_by' => $asset->uploaded_by
            ]);
        }

        Schema::table('assets', function($table) {
            $table->dropColumn('width');
            $table->dropColumn('height');
            $table->dropColumn('filename');
            $table->dropColumn('type');
            $table->dropColumn('filesize');
            $table->dropColumn('duration');
            $table->dropColumn('last_modified');
        });

        DB::statement('update asset_versions inner join assets on asset_id = assets.id set asset_versions.id = assets.id');            
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
