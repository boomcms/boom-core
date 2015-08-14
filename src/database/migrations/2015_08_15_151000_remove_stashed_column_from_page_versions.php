<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveStashedColumnFromPageVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_versions', function (Blueprint $table) {
            $table->dropColumn('stashed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_versions', function (Blueprint $table) {
            $table->boolean('stashed')->nullable()->default(0)->index('page_versions_stashed');
        });
    }
}
