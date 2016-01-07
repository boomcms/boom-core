<?php

use BoomCMS\Database\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddButtonSettingColumnsOnPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->tinyinteger(Page::ATTR_ADD_BEHAVIOUR)->default(1);
            $table->tinyinteger(Page::ATTR_CHILD_ADD_BEHAVIOUR)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(Page::ATTR_ADD_BEHAVIOUR);
            $table->dropColumn(Page::ATTR_CHILD_ADD_BEHAVIOUR);
        });
    }
}
