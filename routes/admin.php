<?php

use Illuminate\Support\Facades\Route;
use Vinlon\Laravel\LayAdmin\AdminResponse;

Route::middleware(['auth:lay-admin', AdminResponse::class])->group(function () {
    # QueryMonitor
    Route::resource('query_monitors', 'QueryMonitorController')->only(['index', 'store', 'destroy']);
    Route::get('query_monitor_records', 'QueryMonitorController@listRecords');
    Route::get('query_monitor_summary', 'QueryMonitorController@getMonitorSummary');

    # AutoPurchase
    Route::resource('auto_purchases', 'AutoPurchaseController')->only(['index']);

    # Axie
    Route::get('axie_body_parts', 'AxieController@listParts');
    Route::get('axie_sold_histories', 'AxieController@listSoldHistories');
    Route::get('axie_orders', 'AxieController@listOrders');
    Route::get('axie_eggs', 'BreedController@listEggs');
    Route::get('breed_user_summary', 'BreedController@breedUserSummary');
    Route::get('breed_class_summary', 'BreedController@breedClassSummary');
    Route::get('breed_part_summary', 'BreedController@breedPartSummary');

    # Token SoldHistory
    Route::get('erc1155_sold_summary', 'SoldHistoryController@getSoldSummary');
    Route::get('erc1155_orders', 'SoldHistoryController@getTokenOrders');
    Route::get('erc1155_sold_histories', 'SoldHistoryController@getTokenSoldHistories');

    # Leaderboard
    Route::get('team_labels', 'LeaderboardController@listTeamLabels');
    Route::get('leaderboard_summary', 'LeaderboardController@getSummary');
    Route::resource('leaderboards', 'LeaderboardController')->only(['index']);

    # User
    Route::get('battle_histories', 'UserController@listBattleHistories');
    Route::get('user_team_summaries', 'UserController@listTeamSummary');

});
