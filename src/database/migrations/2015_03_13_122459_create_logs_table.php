<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->string('ip', 15)->nullable();
            $table->string('activity')->nullable();
            $table->text('note', 65535)->nullable();
            $table->smallInteger('person_id')->unsigned()->nullable()->index('activitylog_person');
            $table->integer('time')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logs');
    }
}
