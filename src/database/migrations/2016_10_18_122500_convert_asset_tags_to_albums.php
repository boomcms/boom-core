<?php

use BoomCMS\Database\Models\Album;
use BoomCMS\Database\Models\Chunk\Library;
use BoomCMS\Database\Models\Role;
use BoomCMS\Database\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class ConvertAssetTagsToAlbums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $site = Site::where('default', true)->first();

        Schema::create('albums', function (Blueprint $table) {
            $table->increments(Album::ATTR_ID);
            $table->string(Album::ATTR_NAME);
            $table->string(Album::ATTR_DESCRIPTION)->nullable();
            $table->string(Album::ATTR_SLUG)->unique();
            $table->string(Album::ATTR_ORDER)->nullable();
            $table->integer(Album::ATTR_ASSET_COUNT)->unsigned()->default(0);
            $table->integer(Album::ATTR_SITE)->unsigned()->references('id')->on('sites')->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->integer(Album::ATTR_FEATURE_IMAGE)->unsigned()
                ->nullable()
                ->references('id')
                ->on('assets')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->integer('created_by')
                ->unsigned()
                ->nullable()
                ->references('id')
                ->on('people')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->integer('deleted_by')
                ->unsigned()
                ->nullable()
                ->references('id')
                ->on('people')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->softDeletes();
            $table->timestamps();
            $table->index(Album::ATTR_NAME);
        });

        Schema::create('album_asset', function (Blueprint $table) {
            $table->integer('album_id')->unsigned()->references('id')->on('albums')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->integer('asset_id')->unsigned()->references('id')->on('assets')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->integer('position')->unsigned();
            $table->index(['album_id', 'position']);
            $table->primary(['album_id', 'asset_id']);
        });

        $tags = DB::table('assets_tags')
            ->select('tag')
            ->distinct()
            ->pluck('tag');

        $albums = [];

        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $albums[$tag] = Album::create([
                    Album::ATTR_NAME => $tag,
                    Album::ATTR_SITE => $site->getId(),
                ]);

                DB::statement('insert into album_asset (album_id, asset_id) select "'.$albums[$tag]->getId().'", asset_id from assets_tags where tag = "'.$tag.'"');

                $albums[$tag]->assetsUpdated();
            }
        }

        $libraries = Library::all();

        foreach ($libraries as $library) {
            if (isset($library->params->tag) && !empty($library->params->tag)) {
                if (isset($albums[$library->params->tag])) {
                    $library->params->album = $albums[$library->params->tag]->getId();
                }

                unset($library->params->tag);

                $library->save();
            }
        }

        $role = Role::create([
            'name' => 'manageAlbums',
        ]);

        $manageAssets = Role::where('name', 'manageAssets')->first();
        DB::statement("insert into group_role (role_id, group_id) select '{$role->getId()}', group_id from group_role where role_id = {$manageAssets->getId()}");

        Schema::drop('assets_tags');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
