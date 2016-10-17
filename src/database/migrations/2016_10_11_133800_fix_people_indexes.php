<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FixPeopleIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('people', function (Blueprint $table) {
            // Credit: https://github.com/laravel/framework/issues/3253#issuecomment-51961561
            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $doctrineTable = $dbSchemaManager->listTableDetails('people');

            if ($doctrineTable->hasIndex('people_email_unique')) {
                $table->dropUnique('people_email_unique');
            }

            $table->dropUnique('deleted_at');
            $table->unique(['email', 'deleted_at']);
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
            $table->dropUnique('people_email_deleted_at_unique');
            $table->unique('email');
            $table->unique('deleted_at');
        });
    }
}
