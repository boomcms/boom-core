<?php

use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AddLanguagePermissionRole extends Migration
{
    protected $roles = [
        ['name' => 'setPageLanguage', 'tree' => true],
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

