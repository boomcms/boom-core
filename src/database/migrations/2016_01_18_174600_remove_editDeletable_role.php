<?php

use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;

class RemoveEditDeletableRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::where('name', '=', 'editDeletable')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::create([
            'name'        => 'editDeletable',
            'description' => 'Edit whether a page can be deleted',
        ]);
    }
}
