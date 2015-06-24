<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class UseLaravelSoftDelete extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->softDeletes();
            $table->mediumInteger('deleted_by', false, true)->nullable();
            $table
                ->foreign('deleted_by')
                ->references('id')
                ->on('people')
                ->onDelete('set null');
        });

        DB::statement('update pages set deleted_at = unix_timestamp() where deleted = true');

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('deleted');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->softDeletes();
            $table->mediumInteger('deleted_by', false, true)->nullable();
            $table
                ->foreign('deleted_by')
                ->references('id')
                ->on('people')
                ->onDelete('set null');
        });

            Schema::table('groups', function (Blueprint $table) {
            $table->softDeletes();
            $table->mediumInteger('deleted_by', false, true)->nullable();
            $table
                ->foreign('deleted_by')
                ->references('id')
                ->on('people')
                ->onDelete('set null');
        });

        DB::statement('update groups set deleted_at = unix_timestamp() where deleted = true');

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('deleted');
        });
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->boolean('deleted')->nullable()->default(0)->index('groups_deleted');
        });

        DB::statement('update groups set deleted = 1 where deleted_at > 0');

        Schema::table('groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->boolean('deleted')->nullable()->default(0);
        });

        DB::statement('update pages set deleted = 1 where deleted_at > 0');

        Schema::table('pages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deleted_by');
        });
    }

}
