<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveIsBlockColumnFromChunkTexts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chunk_texts', function (Blueprint $table) {
            $table->dropColumn('is_block');
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
            $table->boolean('is_block')->nullable()->default(0);
        });
    }
}
