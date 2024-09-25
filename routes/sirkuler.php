<?php

header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

// time track
Route::get('/sirkuler/time_tracking', [App\Http\Controllers\Sirkuler\TrackingController::class, 'time_tracking']);

// tracking
Route::get('/sirkuler/tracking/get_detail_sirkuler', [App\Http\Controllers\Sirkuler\TrackingController::class, 'get_detail_sirkuler']);

Route::get('/sirkuler/tracking/catatan/{id}', [App\Http\Controllers\Sirkuler\TrackingController::class, 'get_catatan_history']);

Route::get('/sirkuler/tracking/{ticket_id}', [App\Http\Controllers\Sirkuler\TrackingController::class, 'tracking']);

Route::group([
    'namespace' => 'App\Http\Controllers\Sirkuler',
    'prefix' => config('sirkuler.prefix'),
    'middleware' => ['auth'],
], function () {
    
    // home
    Route::resource('home', 'HomeController');

    // master pihak lain
    Route::post('master_pihak_lain/check_duplicate_code', 'MasterPihakLainController@check_duplicate_code');
    Route::post('master_pihak_lain/check_duplicate', 'MasterPihakLainController@check_duplicate');
    Route::post('master_pihak_lain/get_companies', 'MasterPihakLainController@get_companies');
    Route::get('master_pihak_lain/json_index', 'MasterPihakLainController@json_index');
    Route::resource('master_pihak_lain', 'MasterPihakLainController');

    // master type pihak lain
    Route::post('master_type_pihak_lain/check_duplicate', 'MasterTypePihakLainController@check_duplicate');
    Route::post('master_type_pihak_lain/get_companies', 'MasterTypePihakLainController@get_companies');
    Route::get('master_type_pihak_lain/json_index', 'MasterTypePihakLainController@json_index');
    Route::resource('master_type_pihak_lain', 'MasterTypePihakLainController');
    
});
