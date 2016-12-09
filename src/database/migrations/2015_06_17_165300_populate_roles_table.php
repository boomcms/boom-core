<?php

use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PopulateRolesTable extends Migration
{
    protected $roles = [
        ['name' => 'managePeople', 'tree' => false],
        ['name' => 'manageAssets', 'tree' => false],
        ['name' => 'toolbar', 'tree' => true],
        ['name' => 'delete', 'tree' => true],
        ['name' => 'add', 'tree' => true],
        ['name' => 'editFeature', 'tree' => true],
        ['name' => 'editTemplate', 'tree' => true],
        ['name' => 'publish', 'tree' => true],
        ['name' => 'editNavBasic', 'tree' => true],
        ['name' => 'editNavAdvanced', 'tree' => true],
        ['name' => 'editSearchBasic', 'tree' => true],
        ['name' => 'editSearchAdvanced', 'tree' => true],
        ['name' => 'editChildrenBasic', 'tree' => true],
        ['name' => 'editChildrenAdvanced', 'tree' => true],
        ['name' => 'editAdmin', 'tree' => true],
        ['name' => 'edit', 'tree' => true],
        ['name' => 'editUrls', 'tree' => true],
        ['name' => 'editAcl', 'tree' => true],
        ['name' => 'uploadAssets', 'tree' => false]
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create($this->roles);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('truncate roles');
    }
}
