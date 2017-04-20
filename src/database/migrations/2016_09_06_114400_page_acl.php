<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PageACL extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_acl', function (Blueprint $table) {
            $table
                ->integer('page_id')
                ->unsigned()
                ->references('id')
                ->on('pages')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->integer('group_id')
                ->unsigned()
                ->references('id')
                ->on('groups')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->primary(['page_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('page_acl');
    }
}
