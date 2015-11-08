<?php

use BoomCMS\Database\Models\Chunk\Library;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateChunkLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('alter table chunk_libraries add params varchar(255)');

        $chunks = Library::all();

        foreach ($chunks as $c) {
            $c->params = ['tag' => $c->tag];
            $c->save();
        }

        DB::statement('alter table chunk_libraries drop tag');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table chunk_libraries add tag char(50)');

        $chunks = Library::all();

        foreach ($chunks as $c) {
            $params = $c->params;

            if (isset($params['tag'])) {
                $c->tag = $params['tag'];
                $c->save();
            }
        }

        DB::statement('alter table chunk_libraries drop params');
    }
}
