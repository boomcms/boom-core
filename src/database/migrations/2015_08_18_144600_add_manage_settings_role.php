<?php

use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AddManageSettingsRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create([
            'name'        => 'manage_settings',
            'description' => 'View the site settings editor',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', '=', 'manage_settings')->delete();
    }
}
