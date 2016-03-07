<?php

use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;

class RenamePagePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::where('name', '=', 'edit')
            ->update([
                'name'        => 'toolbar',
                'description' => 'See the CMS editor toolbar',
            ]);

        Role::where('name', '=', 'editContent')
            ->update([
                'name'        => 'edit',
                'description' => 'Page - edit',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', '=', 'edit')
            ->update([
                'name'        => 'editContent',
                'description' => 'Edit page - content',
            ]);

        Role::where('name', '=', 'toolbar')
            ->update([
                'name'        => 'edit',
                'description' => 'Page - edit',
            ]);
    }
}
