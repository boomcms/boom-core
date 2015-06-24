<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\DB;

class AddParentIdToPagesTable extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('pages')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        DB::statement('update pages inner join page_mptt on pages.id = page_mptt.id set pages.parent_id = page_mptt.parent_id');
        Schema::drop('page_mptt');
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
