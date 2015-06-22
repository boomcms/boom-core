<?php

use Illuminate\Database\Migrations\Migration;

class RemoveVisibleFromFromAssets extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
		Schema::table('assets', function($table)
		{
			$table->dropColumn('visible_from');
		});
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {            
		Schema::table('assets', function($table)
		{
			$table->integer('visible_from')->unsigned()->nullable()->default(0)->index('asset_v_deleted_visible_from_status');
		});
    }
}
