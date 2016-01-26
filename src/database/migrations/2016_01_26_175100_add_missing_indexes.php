<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMissingIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chunk_locations', function (Blueprint $table) {
            $table->index(['page_id', 'slotname', 'page_vid']);
        });

        Schema::table('search_texts', function (Blueprint $table) {
            $table->index(['page_id', 'embargoed_until']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->index(['deleted_at', 'parent_id', 'visible']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chunk_locations', function (Blueprint $table) {
            $table->dropIndex('chunk_locations_page_id_slotname_page_vid_index');
        });

        Schema::table('search_texts', function (Blueprint $table) {
            $table->dropIndex('search_texts_page_id_embargoed_until_index');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex('pages_deleted_at_parent_id_visible_index');
        });
    }
}
