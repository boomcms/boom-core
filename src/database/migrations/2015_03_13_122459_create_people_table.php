<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePeopleTable extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->mediumInteger('id', true, true);
            $table->string('name')->nullable();
            $table->string('email')->unique('people_email');
            $table->boolean('enabled')->nullable()->default(1);
            $table->string('password', 60)->nullable();
            $table->boolean('failed_logins')->nullable()->default(0);
            $table->integer('locked_until')->unsigned()->nullable()->default(0);
            $table->integer('avatar_id')->unsigned()->nullable();
        });
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {
        Schema::drop('people');
    }

}
