<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixForeignKeysForDeletingPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table page_urls drop foreign key page_urls_ibfk_1');
        DB::statement('alter table page_urls add constraint page_urls_ibfk_1 foreign key(page_id) references pages(id) on update cascade on delete cascade');
        DB::statement('alter table page_versions add constraint page_versions_ibfk_1 foreign key(page_id) references pages(id) on update cascade on delete cascade');
        DB::statement('alter table chunk_texts add constraint chunk_texts_ibfk1 foreign key (page_id) references pages(id) on update cascade on delete cascade');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table page_urls drop foreign key page_urls_ibfk_1');
        DB::statement('alter table page_urls add constraint page_urls_ibfk_1 foreign key(page_id) references pages(id) ');
        DB::statement('alter table page_versions drop foreign key page_versions_ibfk_1 ');
        DB::statement('alter table chunk_texts drop foreign key chunk_texts_ibfk1');

    }
}