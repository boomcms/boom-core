<?php

use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;

class RemoveManageApprovalsRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::where('name', 'manageApprovals')->delete();
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
