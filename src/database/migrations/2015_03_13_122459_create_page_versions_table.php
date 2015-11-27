<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePageVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned()->nullable()->index('page_v_rid');
            $table->smallInteger('template_id')->nullable()->index('page_versions_template_id');
            $table->string('title', 100)->nullable()->default('Untitled');
            $table->smallInteger('edited_by')->unsigned()->nullable()->index('edited_by');
            $table->integer('edited_time')->unsigned()->nullable();
            $table->boolean('published')->nullable()->default(0);
            $table->integer('embargoed_until')->unsigned()->nullable();
            $table->boolean('stashed')->nullable()->default(0)->index('page_versions_stashed');
            $table->boolean('pending_approval')->nullable()->default(0)->index('page_versions_pending_approval');
            $table->index(['edited_time', 'page_id'], 'page_v_aduit_time_rid_deleted');
            $table->index(['title', 'page_id'], 'page_v_title_rid_deleted');
            $table->index(['page_id', 'published', 'embargoed_until'], 'page_versions_page_id_published_embargoed_until');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('page_versions');
    }
}
