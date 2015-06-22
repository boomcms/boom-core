<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLogsTable extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
		Schema::drop('logs');
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
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
}
