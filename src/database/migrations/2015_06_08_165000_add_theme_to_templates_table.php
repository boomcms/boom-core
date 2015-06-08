<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\DB;

class AddThemeToTemplates extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('theme', 100);
            $table->unique(['theme', 'filename']);
        });
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {
        Schema::table('templates', function ($table) {
            $table->dropColumn('theme');
        });
    }

}
