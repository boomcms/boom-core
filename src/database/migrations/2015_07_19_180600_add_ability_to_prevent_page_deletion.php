<?php

use BoomCMS\Core\Models\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddAbilityToPreventPageDeletion extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Role::create([
            'name' => 'p_edit_disable_delete',
            'description' => 'Edit whether a page can be deleted'
        ]);

		Schema::table('pages', function($table)
		{
			$table->boolean('disable_delete')->default(false);
		});

        DB::table('pages')
            ->whereNull('parent_id')
            ->update([
                'disable_delete' => true
            ]);
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {
		Schema::table('pages', function($table)
		{
			$table->dropColumn('disable_delete');
		});

        Role::where('name', '=', 'p_edit_disable_delete')
            ->delete();
    }
}
