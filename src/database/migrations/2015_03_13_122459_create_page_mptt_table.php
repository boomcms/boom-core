<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePageMpttTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_mptt', function (Blueprint $table) {
            $table->smallInteger('lft')->unsigned()->default(0)->index('page_mptt_lft');
            $table->smallInteger('rgt')->unsigned();
            $table->integer('parent_id')->nullable()->index('page_mptt_parent_id');
            $table->boolean('lvl')->index('page_mptt_lvl');
            $table->boolean('scope')->default(1);
            $table->smallInteger('id', true)->unsigned();
            $table->index(['lft', 'rgt'], 'page_mptt_lft_rgt');
            $table->index(['lft', 'scope'], 'page_mptt_lft_scope_page_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('page_mptt');
    }
}
