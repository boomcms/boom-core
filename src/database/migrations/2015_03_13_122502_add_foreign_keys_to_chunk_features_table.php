<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToChunkFeaturesTable extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('chunk_features', function (Blueprint $table) {
            $table->foreign('page_vid', 'chunk_features_ibfk_1')->references('id')->on('page_versions')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {
        Schema::table('chunk_features', function (Blueprint $table) {
            $table->dropForeign('chunk_features_ibfk_1');
        });
    }

}
