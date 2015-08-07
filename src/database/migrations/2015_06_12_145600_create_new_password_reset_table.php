<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNewPasswordResetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token')->index();
            $table->timestamp('created_at');
        });

        Schema::drop('password_tokens');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('password_resets');

        Schema::create('password_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('person_id')->unsigned()->index('password_tokens_person_id');
            $table->string('token', 40)->unique('uniq_token');
            $table->integer('created')->unsigned();
            $table->integer('expires')->unsigned()->index('password_tokens_expires');
        });
    }
}
