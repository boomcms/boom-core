<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCreatedColumnsToPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function(Blueprint $table) {
            $table->integer('created_by')
                ->unsigned()
                ->nullable()
                ->references('id')
                ->on('people')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->integer('created_at')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function(Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('created_at');
        });
    }
}
