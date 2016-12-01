<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MakeAuthColumnsConsistent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_versions', function (Blueprint $table) {
            $table->renameColumn('edited_time', 'created_at');
            $table->renameColumn('edited_by', 'created_by');
        });

        Schema::table('asset_versions', function (Blueprint $table) {
            $table->renameColumn('edited_at', 'created_at');
            $table->renameColumn('edited_by', 'created_by');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->renameColumn('uploaded_time', 'created_at');
            $table->renameColumn('uploaded_by', 'created_by');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('created_time', 'created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_versions', function (Blueprint $table) {
            $table->renameColumn('created_at', 'edited_time');
            $table->renameColumn('created_by', 'edited_by');
        });

        Schema::table('asset_versions', function (Blueprint $table) {
            $table->renameColumn('created_at', 'edited_at');
            $table->renameColumn('created_by', 'edited_by');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->renameColumn('created_at', 'uploaded_time');
            $table->renameColumn('created_by', 'uploaded_by');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('created_at', 'created_time');
        });
    }
}
