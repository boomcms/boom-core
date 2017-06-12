<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixUrlOfHomepage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('update pages set primary_uri = "" where primary_uri = "/"');
        DB::statement('update page_urls set location = "" where location = "/"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('update pages set primary_uri = "/" where primary_uri = ""');
        DB::statement('update page_urls set location = "/" where location = ""');
    }
}
