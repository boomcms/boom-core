<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMinionMigrationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('minion_migrations', function(Blueprint $table)
		{
			$table->string('timestamp', 14);
			$table->string('description', 100);
			$table->string('group', 100);
			$table->boolean('applied')->nullable()->default(0);
			$table->primary(['timestamp','group']);
			$table->unique(['timestamp','description'], 'MIGRATION_ID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('minion_migrations');
	}

}
