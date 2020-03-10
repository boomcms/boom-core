<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAssetUsagesIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_usages', function (Blueprint $table) {
            $table->index(['asset_id'], 'asset_views_asset_id');
            $table->index(['ip_address', 'asset_id', 'time'], 'asset_views_ip_asset_id_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
