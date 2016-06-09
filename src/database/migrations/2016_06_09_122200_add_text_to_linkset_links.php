<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTextToLinksetLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chunk_linkset_links', function (Blueprint $table) {
            $table->text('text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chunk_linkset_links', function (Blueprint $table) {
            $table->dropColumn('text');
        });
    }
}
