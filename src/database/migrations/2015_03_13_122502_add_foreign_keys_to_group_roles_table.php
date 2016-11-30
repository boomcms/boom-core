<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_role', function (Blueprint $table) {
            $table->foreign('group_id', 'group_roles_ibfk_2')->references('id')->on('groups')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('role_id', 'group_roles_ibfk_1')->references('id')->on('roles')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('deleted_by')->references('id')->on('people')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_role', function (Blueprint $table) {
            $table->dropForeign('group_roles_ibfk_2');
            $table->dropForeign('group_roles_ibfk_1');
        });
    }
}
