<?php

use BoomCMS\Core\Page\Finder;
use BoomCMS\Support\Facades\Chunk;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AddMetaColumnToSearchTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_texts', function (Blueprint $table) {
            $table->text('meta')->nullable();
        });

        DB::statement('CREATE FULLTEXT INDEX search_texts_meta on search_texts(meta)');
        DB::statement('ALTER TABLE search_texts drop index search_texts_all');
        DB::statement('CREATE FULLTEXT INDEX search_texts_all on search_texts(title, standfirst, text, meta)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('search_texts', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
}
