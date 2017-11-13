<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class IncludeDeletedAtOnAlbumsSlugIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table albums drop index albums_slug_unique');
        DB::statement('alter table albums add unique index albums_deleted_at_slug_unique(deleted_at, slug)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table albums drop index albums_deleted_at_slug_unique');
        DB::statement('alter table albums add unique index albums_slug_unique(slug)');

    }
}
