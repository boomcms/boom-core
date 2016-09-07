<?php

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PageACL extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_acl', function (Blueprint $table) {
            $table
                ->integer('page_id')
                ->unsigned()
                ->references('id')
                ->on('pages')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->integer('group_id')
                ->unsigned()
                ->references('id')
                ->on('groups')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->primary(['page_id', 'group_id']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean(Page::ATTR_ENABLE_ACL)->default(false);
        });

        Role::create([
            'name' => 'editAcl',
            'tree' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('page_acl');

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(Page::ATTR_ENABLE_ACL);
        });

        Role::where('name', 'editAcl')->delete();
    }
}
