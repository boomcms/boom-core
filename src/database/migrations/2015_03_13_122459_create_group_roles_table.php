<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_roles', function (Blueprint $table) {
            $table->smallInteger('group_id')->unsigned();
            $table->integer('page_id')->unsigned()->default(0);
            $table->smallInteger('role_id')->unsigned()->index('role_id');
            $table->boolean('allowed')->nullable();
            $table->primary(['group_id', 'role_id', 'page_id']);
            $table->index(['group_id', 'role_id', 'page_id'], 'permissions_role_id_action_id_where_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('group_roles');
    }
}
