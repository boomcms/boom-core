<?php

namespace BoomCMS\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		include __DIR__ . '/../../routes.php';
	}

	/**
	 *
	 * @return void
	 */
	public function register()
	{
	}

}
