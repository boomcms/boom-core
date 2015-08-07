<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddPageIdToChunks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (['chunk_texts', 'chunk_features', 'chunk_slideshows', 'chunk_assets', 'chunk_linksets', 'chunk_tags', 'chunk_timestamps'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->integer('page_id')->unsigned();
            });

            DB::statement("update $t inner join page_versions on page_vid = page_versions.id set {$t}.page_id = page_versions.page_id");

            Schema::table($t, function (Blueprint $table) {
                $table
                    ->foreign('page_id')
                    ->references('id')
                    ->on('pages')
                    ->onUpdate('CASCADE')
                    ->onDelete('CASCADE');

                $table->index(['page_id', 'page_vid']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (['chunk_texts', 'chunk_features', 'chunk_slideshows', 'chunk_assets', 'chunk_linksets', 'chunk_tags', 'chunk_timestamps'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('page_id');
            });
        }
    }
}
