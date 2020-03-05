<?php

namespace Damms005\LaravelActivitylogUi;

use Damms005\LaravelActivitylogUi\Controllers\ActivitylogUiController;
use Illuminate\Support\ServiceProvider;

class ActivitylogUiServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->make(ActivitylogUiController::class);
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadRoutesFrom(__DIR__ . '/../routes.php');
		$this->loadViewsFrom(__DIR__ . '/../views', 'activitylog-ui');
	}
}
