<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPageUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_urls', function (Blueprint $table) {
            $table->foreign('page_id', 'page_urls_ibfk_1')->references('id')->on('pages')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_urls', function (Blueprint $table) {
            $table->dropForeign('page_urls_ibfk_1');
        });
    }
}
