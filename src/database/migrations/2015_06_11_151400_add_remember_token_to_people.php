<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRememberTokenToPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->rememberToken();
        });

        Schema::drop('user_tokens');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('fk_user_id');
            $table->string('user_agent', 40);
            $table->string('token', 40)->unique('uniq_token');
            $table->integer('created')->unsigned();
            $table->integer('expires')->unsigned()->index('expires');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropRememberToken();
        });
    }
}
