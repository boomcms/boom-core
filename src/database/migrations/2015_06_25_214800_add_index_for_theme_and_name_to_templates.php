<?php

use Illuminate\Database\Migrations\Migration;

class AddIndexForThemeAndNameToTemplates extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
		Schema::table('templates', function($table)
		{
			$table->index(['theme', 'name']);
		});
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {
		Schema::table('templates', function($table)
		{
			$table->dropIndex(['theme', 'name']);
		});
    }
}
