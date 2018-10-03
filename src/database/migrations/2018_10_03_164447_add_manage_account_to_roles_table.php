<?php

use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AddManageAccountToRolesTable extends Migration
{
    protected $roles = [
        ['name' => 'manageAccount', 'tree' => false],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::insert($this->roles);
    }
}
