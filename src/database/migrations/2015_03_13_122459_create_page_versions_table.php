<?php

use BoomCMS\Database\Models\PageVersion;
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
            $table->integer('embargoed_until')->unsigned()->nullable();
            $table->boolean('pending_approval')->nullable()->default(0)->index('page_versions_pending_approval');
            $table->index('title');
            $table->index(['page_id', 'edited_time', 'embargoed_until']);
            $table->string(PageVersion::ATTR_CHUNK_TYPE, 15);
            $table->integer(PageVersion::ATTR_CHUNK_ID)->unsigned();

            $table
                ->integer('restored_from')
                ->unsigned()
                ->references('id')
                ->on('page_versions')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');
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
