<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
            $table->string('location', 2048);
            $table->boolean('is_primary')->nullable();
            $table->index(['page_id', 'is_primary'], 'page_uri_page_id_primary_uri');
        });

        DB::statement('ALTER TABLE page_urls ADD FULLTEXT page_urls_location(location)');
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
