<?php

use Illuminate\Support\Facades\Route;
use Damms005\LaravelActivitylogUi\Http\Controllers\ActivitylogUiController;

Route::group(['prefix' => '/admin/activitylog-ui', 'middleware' => ['web', 'auth']], function () {
	Route::get('/', [ActivitylogUiController::class, "index"])->name('activitylog.index');
	Route::post('submit', [ActivitylogUiController::class, "show"])->name('activitylog.filter.submit');
});
