<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class RemovePageVisibleColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('pages')
            ->where('visible', '=', 0)
            ->update([
                'visible_from' => null,
            ]);

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('visible');
            $table->index(['deleted_at', 'visible_from', 'visible_to', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('visible')->default(false);
            $table->dropIndex('pages_deleted_at_visible_from_visible_to_parent_id');
        });
    }
}
