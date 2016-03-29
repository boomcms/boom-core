<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPeopleGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_person', function (Blueprint $table) {
            $table->foreign('person_id', 'people_groups_ibfk_2')->references('id')->on('people')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('group_id', 'people_groups_ibfk_1')->references('id')->on('groups')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_person', function (Blueprint $table) {
            $table->dropForeign('people_groups_ibfk_2');
            $table->dropForeign('people_groups_ibfk_1');
        });
    }
}
