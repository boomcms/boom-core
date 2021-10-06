<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedAtColumnInAssetDownloads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_downloads', function (Blueprint $table) {
            $table->date('created_at');
        });

        $downloads = DB::table('asset_downloads')->get(['id', 'time']);

        foreach ($downloads as $download) {
            DB::table('asset_downloads')->where('id', $download->id)
                ->update(['created_at' => date('Y-m-d', $download->time)]);
        }
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
