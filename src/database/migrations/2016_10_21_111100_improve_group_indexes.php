<?php

use BoomCMS\Database\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ImproveGroupIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string(Group::ATTR_NAME)->change();
            $table->unique([Group::ATTR_SITE, Group::ATTR_NAME, 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string(Group::ATTR_NAME)->nullable()->change();
            $table->dropUnique('groups_site_id_name_deleted_at');
        });
    }
}
