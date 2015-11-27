<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePeopleRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people_roles', function (Blueprint $table) {
            $table->integer('person_id')->unsigned();
            $table->smallInteger('role_id')->unsigned()->index('role_id');
            $table->smallInteger('group_id')->unsigned();
            $table->integer('page_id')->unsigned()->default(0);
            $table->boolean('allowed');
            $table->primary(['person_id', 'role_id', 'group_id', 'page_id']);
            $table->index(['group_id', 'person_id'], 'people_roles_group_id_person_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('people_roles');
    }
}
