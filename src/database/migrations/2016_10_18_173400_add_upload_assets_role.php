<?php

use BoomCMS\Database\Models\Role;
use Illuminate\Database\Migrations\Migration;

class AddUploadAssetsRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $upload = Role::create([
            'name' => 'uploadAssets'
        ]);

        $manage = Role::where('name', 'manageAssets')->first();

        DB::statement('insert into group_role (role_id, group_id, allowed) select "'.$upload->getId().'", group_id, allowed from group_role where role_id = '.$manage->getId());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', 'uploadAssets')->delete();
    }
}
