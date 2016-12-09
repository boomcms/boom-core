<?php

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Role;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\Tag;
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
            $table->boolean(Site::ATTR_DEFAULT)->default(false);
            $table->string(Site::ATTR_SCHEME, 10);
            $table->softDeletes();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table
                ->integer(Page::ATTR_SITE)
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });

        Schema::table('page_urls', function (Blueprint $table) {
            $table
                ->integer(URL::ATTR_SITE)
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table
                ->integer(Group::ATTR_SITE)
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->unique([Group::ATTR_SITE, Group::ATTR_NAME, 'deleted_at']);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table
                ->integer('site_id')
                ->unsigned()
                ->references('id')
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
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

        Schema::table('tags', function (Blueprint $table) {
            $table
                ->integer(Tag::ATTR_SITE)
                ->unsigned()
                ->references(Site::ATTR_ID)
                ->on('sites')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->dropIndex('tags_group_name');
            $table->unique(['site_id', 'name', 'group']);
        });

        $filename = storage_path().'/boomcms/settings.json';

        if (file_exists($filename)) {
            $settings = (array) json_decode(file_get_contents($filename));

            $site = Site::create([
                Site::ATTR_DEFAULT     => true,
                Site::ATTR_NAME        => $settings['site.name'],
                Site::ATTR_ADMIN_EMAIL => $settings['site.admin.email'],
                Site::ATTR_HOSTNAME    => '',
            ]);

            foreach (['pages', 'page_urls', 'groups', 'tags'] as $table) {
                DB::table($table)
                    ->update([
                        'site_id' => $site->getId(),
                    ]);
            }

            DB::statement("insert into asset_site (asset_id, site_id) select id, '{$site->getId()}' from assets");
            DB::statement("insert into person_site (person_id, site_id) select id, '{$site->getId()}' from people");
        }

        Role::create([
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

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(Page::ATTR_SITE);
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(Group::ATTR_SITE);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(Group::ATTR_SITE);
        });

        Schema::table('page_urls', function (Blueprint $table) {
            $table->dropColumn(Page::ATTR_SITE);
            $table->dropUnique('page_urls_site_id_location');
        });

        Role::where('name', '=', 'manageSites')->delete();
    }
}
