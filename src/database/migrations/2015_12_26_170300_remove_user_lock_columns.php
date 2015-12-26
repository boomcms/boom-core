<?php

use BoomCMS\Database\Models\AssetVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveUserLockColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('failed_logins');
            $table->dropColumn('locked_until');
            $table->dropColumn('last_failed_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('people', function (Blueprint $table) {
            $table->integer('failed_logins')->nullable()->default(0);
            $table->integer('locked_until')->unsigned()->nullable()->default(0);
            $table->integer('last_failed_login')->unsigned()->nullable()->default(0);
        });
    }
}
