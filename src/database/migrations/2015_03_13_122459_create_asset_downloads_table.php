<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_downloads', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->integer('asset_id')->unsigned()->index('asset_downloads_asset_id');
            $table->integer('time')->unsigned()->nullable();
            $table->integer('ip')->nullable();
            $table->index(['ip', 'asset_id', 'time'], 'asset_downloads_ip_asset_id_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('asset_downloads');
    }
}
