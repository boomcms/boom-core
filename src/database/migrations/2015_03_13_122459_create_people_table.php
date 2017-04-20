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
            $table->integer('id', true, true);
            $table->string('name')->nullable();
            $table->string('email', 191);
            $table->boolean('enabled')->default(1);
            $table->string('password', 60)->nullable();
            $table->boolean('superuser')->default(false);
            $table->rememberToken();
            $table->unique(['email', 'deleted_at']);
            $table->index(['deleted_at', 'name']);
            $table->softDeletes();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->timestamp('last_login')->nullable();

            $table
                ->foreign('deleted_by')
                ->references('id')
                ->on('people')
                ->onDelete('set null');
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
