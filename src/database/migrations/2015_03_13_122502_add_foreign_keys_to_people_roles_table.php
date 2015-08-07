<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPeopleRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people_roles', function (Blueprint $table) {
            $table->foreign('person_id', 'people_roles_ibfk_2')->references('id')->on('people')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('role_id', 'people_roles_ibfk_1')->references('id')->on('roles')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people_roles', function (Blueprint $table) {
            $table->dropForeign('people_roles_ibfk_2');
            $table->dropForeign('people_roles_ibfk_1');
        });
    }
}
