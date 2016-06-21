<?php

use BoomCMS\Support\Facades\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class MakeAssetsSingleSite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table
                ->integer('site_id')
                ->unsigned()
                ->references('id')
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });

        $site = Site::findDefault();

        if ($site) {
            DB::table('assets')->update(['site_id' => $site->getId()]);
        }

        Schema::drop('asset_site');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $site = Site::findDefault();

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('site_id');
        });

        Schema::create('asset_site', function (Blueprint $table) {
            $table
                ->integer('asset_id')
                ->unsigned()
                ->references('id')
                ->on('assets')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->integer('site_id')
                ->unsigned()
                ->references('id')
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->unique(['asset_id', 'site_id']);
        });

        DB::statement('insert into asset_site (asset_id, site_id) select asset_id, "'.$site->getId().'" from assets');
    }
}
