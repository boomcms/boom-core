<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreatePageUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_urls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->string('location', 2048)->nullable()->index(DB::raw('page_urls_location(2048)'));
            $table->boolean('is_primary')->nullable();
            $table->index(['page_id', 'is_primary'], 'page_uri_page_id_primary_uri');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('page_urls');
    }
}
