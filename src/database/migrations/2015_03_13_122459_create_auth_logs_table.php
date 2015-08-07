<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_logs', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->integer('person_id')->unsigned()->nullable();
            $table->boolean('action');
            $table->string('method', 10)->nullable();
            $table->integer('ip')->index('auth_log_ip');
            $table->string('user_agent', 2000)->nullable();
            $table->integer('time')->unsigned();
            $table->index(['person_id','time'], 'auth_log_person_id_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auth_logs');
    }
}
