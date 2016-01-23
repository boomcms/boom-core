<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemovePageVersionPublished extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_versions', function(Blueprint $table) {
            $table->dropColumn('published');
            $table->dropIndex('page_v_rid');
            $table->dropIndex('page_v_aduit_time_rid_deleted');
            $table->dropIndex('page_v_title_rid_deleted');
            $table->dropIndex('page_versions_page_id_published_embargoed_until');
            $table->index('title');
            $table->index(['page_id', 'edited_time', 'embargoed_until']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_versions', function(Blueprint $table) {
            $table->boolean('published');
        });
    }
}
