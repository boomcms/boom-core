<?php

use BoomCMS\Database\Models\Role;
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
        Schema::table('pages', function ($table) {
            $table->boolean('disable_delete')->default(false);
        });

        DB::table('pages')
            ->whereNull('parent_id')
            ->update([
                'disable_delete' => true,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function ($table) {
            $table->dropColumn('disable_delete');
        });
    }
}
