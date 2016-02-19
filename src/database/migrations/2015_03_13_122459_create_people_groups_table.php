<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePeopleGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_person', function (Blueprint $table) {
            $table->integer('person_id')->unsigned();
            $table->smallInteger('group_id')->unsigned()->index('group_id');
            $table->unique(['person_id', 'group_id'], 'person_group_person_id_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('group_person');
    }
}
