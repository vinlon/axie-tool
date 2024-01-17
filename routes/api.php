<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware([\App\Http\Middleware\ApiResponse::class])->group(function () {
    Route::get('leaderboard', 'HomeController@leaderboard');
    Route::get('team_summary/{from}/{to}', 'HomeController@teamSummary');
});
