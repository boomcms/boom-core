<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasswordTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('person_id')->unsigned()->index('password_tokens_person_id');
            $table->string('token', 40)->unique('uniq_token');
            $table->integer('created')->unsigned();
            $table->integer('expires')->unsigned()->index('password_tokens_expires');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('password_tokens');
    }
}
