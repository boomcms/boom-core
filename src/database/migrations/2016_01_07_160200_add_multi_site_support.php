<?php

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\URL;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddMultiSiteSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->increments(Site::ATTR_ID);
            $table->string(Site::ATTR_NAME, 100);
            $table->string(Site::ATTR_HOSTNAME)->unique();
            $table->string(Site::ATTR_ADMIN_EMAIL, 250);
            $table->text(Site::ATTR_ANALYTICS)->nullable();
            $table->softDeletes();
        });

        Scheme::table('pages', function (Blueprint $table) {
            $table
                ->integer(Page::ATTR_SITE)
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });

        Scheme::table('page_urls', function (Blueprint $table) {
            $table
                ->integer(URL::ATTR_SITE)
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->unique([URL::ATTR_SITE, URL::ATTR_LOCATION]);
        });

        Scheme::table('groups', function (Blueprint $table) {
            $table
                ->integer(Group::ATTR_SITE)
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });

        Schema::create('asset_site', function (Blueprint $table) {
            $table
                ->integer('asset_id')
                ->unsigned()
                ->references(Asset::ATTR_ID)
                ->on('assets')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->integer('site_id')
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->unique(['asset_id', 'site_id']);
        });

        Schema::create('person_site', function (Blueprint $table) {
            $table
                ->integer('person_id')
                ->unsigned()
                ->references(Person::ATTR_ID)
                ->on('people')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->integer('site_id')
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->unique(['person_id', 'site_id']);
        });

        // TODO: associate tags with a single site.

        // TODO: create site from current CMS settings
        $site = Site::create();

        foreach (['pages', 'page_urls', 'groups'] as $table) {
            DB::table($table)
                ->update([
                    'site_id' => $site->getId(),
                ]);
        }

        DB::table('roles')
            ->where('name', '=', 'manageSettings')
            ->update([
                'name'        => 'manageSites',
                'description' => 'Manage sites',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sites');
        Schema::drop('asset_site');
        Schema::drop('person_site');

        Scheme::table('pages', function (Blueprint $table) {
            $table->dropColumn(Page::ATTR_SITE);
        });

        Scheme::table('groups', function (Blueprint $table) {
            $table->dropColumn(Group::ATTR_SITE);
        });

        Scheme::table('page_urls', function (Blueprint $table) {
            $table->dropColumn(Page::ATTR_SITE);
            $table->dropUnique('page_urls_site_id_location');
        });

        DB::table('roles')
            ->where('name', '=', 'manageSites')
            ->update([
                'name'        => 'manageSettings',
                'description' => 'Manage CMS settings',
            ]);
    }
}
