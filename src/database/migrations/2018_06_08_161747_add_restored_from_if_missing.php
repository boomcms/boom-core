<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestoredFromIfMissing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('page_versions')) {
            if (!Schema::hasColumn('page_versions', 'restored_from')) {
                Schema::table('page_versions', function (Blueprint $table) {
                    $table
                    ->integer('restored_from')
                    ->unsigned()
                    ->references('id')
                    ->on('page_versions')
                    ->onUpdate('CASCADE')
                    ->onDelete('SET NULL');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
