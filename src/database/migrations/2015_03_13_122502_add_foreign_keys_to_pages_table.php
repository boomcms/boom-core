<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->foreign('feature_image_id', 'pages_ibfk_1')->references('id')->on('assets')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('deleted_by')->references('id')->on('people')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign('pages_ibfk_1');
        });
    }
}
