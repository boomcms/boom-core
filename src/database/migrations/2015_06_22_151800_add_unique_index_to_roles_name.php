<?php

use Illuminate\Database\Migrations\Migration;

class AddUniqueIndexToRolesName extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
		Schema::table('roles', function($table)
		{
			$table->dropIndex('actions_name');
			$table->unique('name');
		});
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
