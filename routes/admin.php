<?php

use Illuminate\Support\Facades\Route;
use Vinlon\Laravel\LayAdmin\AdminResponse;

Route::middleware(['auth:lay-admin', AdminResponse::class])->group(function () {
    # 资源管理
    Route::get('image_resources', 'ResourceController@listImageResources');
    Route::post('image_resources', 'ResourceController@saveImageResource');
    Route::post('image_resources/upload', 'ResourceController@uploadImage');
    Route::get('text_resources', 'ResourceController@listTextResources');
    Route::post('text_resources', 'ResourceController@saveTextResource');

    # QueryMonitor
    Route::resource('query_monitors', 'QueryMonitorController')->only(['index', 'store', 'destroy']);
    Route::get('query_monitor_records', 'QueryMonitorController@listRecords');
    Route::get('query_monitor_summary', 'QueryMonitorController@getMonitorSummary');

    # AutoPurchase
    Route::resource('auto_purchases', 'AutoPurchaseController')->only(['index']);

    # SoldHistory
    Route::get('erc1155_sold_summary', 'SoldHistoryController@getSoldSummary');
    Route::get('erc1155_orders', 'SoldHistoryController@getTokenOrders');
    Route::get('erc1155_sold_histories', 'SoldHistoryController@getTokenSoldHistories');
});
