<?php

use Damms005\LaravelActivitylogUi\Controllers\ActivitylogUiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'activitylog-ui', 'middleware' => 'auth'], function () {
	Route::get('/', [ActivitylogUiController::class, "index"]);
	Route::post('submit', [ActivitylogUiController::class, "show"])->name('activitylog.filter.submit');
});
