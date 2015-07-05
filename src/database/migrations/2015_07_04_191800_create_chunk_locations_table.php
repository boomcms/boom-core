<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChunkLocationsTable extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('chunk_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_vid')
                ->unsigned()
                ->nullable()
                ->references('id')
                ->on('page_versions')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->integer('page_id')
                ->unsigned()
                ->references('id')
                ->on('pages')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->string('slotname', 50)->nullable();
            $table->decimal('lat', 9, 6)->nullable;
            $table->decimal('lng', 9, 6)->nullable();
            $table->mediumText('address')->nullable();
            $table->string('title')->nullable();
            $table->string('postcode', 10)->nullable();
        });
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
