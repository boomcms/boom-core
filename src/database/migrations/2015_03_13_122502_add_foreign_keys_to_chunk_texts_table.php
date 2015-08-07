<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToChunkTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chunk_texts', function (Blueprint $table) {
            $table->foreign('page_vid', 'chunk_texts_ibfk_1')->references('id')->on('page_versions')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chunk_texts', function (Blueprint $table) {
            $table->dropForeign('chunk_texts_ibfk_1');
        });
    }
}
