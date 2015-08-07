<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSearchIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table chunk_texts drop foreign key chunk_texts_page_id_foreign');
        DB::statement('alter table chunk_texts drop foreign key chunk_texts_ibfk_1');
        DB::statement('alter table chunk_texts drop foreign key chunk_texts_ibfk_2');
        DB::statement('alter table chunk_texts engine = "MyISAM"');
        DB::statement('CREATE FULLTEXT INDEX text_fulltext on chunk_texts(text)');
        DB::statement('create index chunk_texts_page_id on chunk_texts(page_id)');
        DB::statement('create index chunk_texts_page_vid on chunk_texts(page_vid)');

        DB::statement('alter table page_versions drop foreign key page_versions_ibfk_1');

        DB::statement('alter table chunk_features drop foreign key chunk_features_ibfk_1');
        DB::statement('alter table chunk_features drop foreign key chunk_features_ibfk_2');

        DB::statement('alter table chunk_assets drop foreign key chunk_assets_ibfk_1');
        DB::statement('alter table chunk_assets drop foreign key chunk_assets_ibfk_3');

        DB::statement('alter table chunk_slideshows drop foreign key chunk_slideshows_ibfk_1');
        DB::statement('alter table chunk_slideshows drop foreign key chunk_slideshows_ibfk_2');

        DB::statement('alter table chunk_linksets drop foreign key chunk_linksets_ibfk_1');
        DB::statement('alter table chunk_linksets drop foreign key chunk_linksets_ibfk_2');

        DB::statement('alter table chunk_timestamps drop foreign key chunk_timestamps_ibfk_1');
        DB::statement('alter table chunk_timestamps drop foreign key chunk_timestamps_ibfk_2');

        DB::statement('alter table page_versions engine = "MyISAM"');
        DB::statement('CREATE FULLTEXT INDEX title_fulltext on page_versions(title)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
