<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use App\Http\Controllers\GASheet\MasterMaintenanceController;
use App\Http\Controllers\GASheet\CleaningSheetController;
use App\Http\Controllers\GASheet\ITSheetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return Inertia::render('Auth/Login');
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
})->middleware('guest');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group([
    'namespace' => 'App\Http\Controllers\GASheet',
    'prefix' => 'ga_sheet',
    'middleware' => ['auth'],
], function () {
    
    
    // Route::resource('sirkuler/home', 'HomeController');

    // // master pihak lain
    // Route::post('master_pihak_lain/check_duplicate_code', 'MasterPihakLainController@check_duplicate_code');
    // Route::post('master_pihak_lain/check_duplicate', 'MasterPihakLainController@check_duplicate');
    // Route::post('master_pihak_lain/get_companies', 'MasterPihakLainController@get_companies');
    // Route::get('master_pihak_lain/json_index', 'MasterPihakLainController@json_index');
    // Route::resource('master_pihak_lain', 'MasterPihakLainController');

    // // master type pihak lain
    // Route::post('master_type_pihak_lain/check_duplicate', 'MasterTypePihakLainController@check_duplicate');
    // Route::post('master_type_pihak_lain/get_companies', 'MasterTypePihakLainController@get_companies');
    
    Route::get('report/sheet/{type}/{date}/{idReport}/{pic_name}', 'ReportGASheetController@reportPdf');

    Route::post('simbol_kondisi/get_alat_cleaning', 'MasterSimbolKondisiController@get_alat_cleaning');
    Route::post('simbol_kondisi/get_gedung', 'MasterSimbolKondisiController@get_gedung');
    Route::post('simbol_kondisi/get_ruangan', 'MasterSimbolKondisiController@get_ruangan');
    Route::post('simbol_kondisi/get_lokasi', 'MasterSimbolKondisiController@get_lokasi');
    Route::post('simbol_kondisi/get_alat', 'MasterSimbolKondisiController@get_alat');
    Route::get('simbol_kondisi/json_index', 'MasterSimbolKondisiController@json_index');
    Route::post('simbol_kondisi/check_duplicate_code', 'MasterSimbolKondisiController@check_duplicate_code');
    Route::resource('simbol_kondisi', 'MasterSimbolKondisiController');

    Route::get('pic/json_index', 'MasterPICController@json_index');
    Route::resource('pic', 'MasterPICController');

    Route::post('maintenance/check_create_today', 'MasterMaintenanceController@check_create_today');
    Route::get('maintenance/{id}/report', 'MasterMaintenanceController@report');
    Route::get('maintenance/{id}/history', 'MasterMaintenanceController@history');
    Route::post('maintenance/create_qr', 'MasterMaintenanceController@create_qr');
    Route::post('maintenance/get_komponen_simbol_kondisi_qr', 'MasterMaintenanceController@get_komponen_simbol_kondisi_qr');
    Route::get('/maintenance/scan', [MasterMaintenanceController::class, 'scan'])->name('maintenance.scan');
    Route::post('maintenance/get_komponen_simbol_kondisi', 'MasterMaintenanceController@get_komponen_simbol_kondisi');
    Route::post('maintenance/index_group', 'MasterMaintenanceController@index_group');
    Route::get('maintenance/json_index', 'MasterMaintenanceController@json_index');
    Route::resource('maintenance','MasterMaintenanceController');
    
    Route::post('alat/get_default_simbol', 'MasterAlatController@get_default_simbol');
    Route::get('alat/json_index', 'MasterAlatController@json_index');
    Route::resource('alat','MasterAlatController');

    Route::get('lokasi/json_index', 'MasterLokasiController@json_index');
    Route::resource('lokasi','MasterLokasiController');

    Route::get('gedung/json_index', 'MasterGedungController@json_index');
    Route::resource('gedung','MasterGedungController');

    Route::post('ruangan/get_lokasi_gedung', 'MasterRuanganController@get_lokasi_gedung');
    Route::get('ruangan/json_index', 'MasterRuanganController@json_index');
    Route::resource('ruangan','MasterRuanganController');

    Route::get('sheet_roles/json_index', 'MasterRolesController@json_index');
    Route::resource('sheet_roles','MasterRolesController');

    Route::post('cleaning/get_lokasi_gedung', 'CleaningSheetController@get_lokasi_gedung');
    Route::get('cleaning/{id}/history', 'CleaningSheetController@history');
    Route::post('cleaning/create_qr', 'CleaningSheetController@create_qr');
    Route::post('cleaning/get_alat_simbol_kondisi', 'CleaningSheetController@get_alat_simbol_kondisi');
    Route::get('/cleaning/scan', [CleaningSheetController::class, 'scan'])->name('cleaning.scan');
    Route::get('cleaning/json_index', 'CleaningSheetController@json_index');
    Route::resource('cleaning', 'CleaningSheetController');

    Route::get('number_of_work/json_index', 'MasterNumberOfWorkController@json_index');
    Route::resource('number_of_work', 'MasterNumberOfWorkController');

    Route::get('category_preventive_breakdown/json_index', 'MasterCategoryPreventiveBreakdownController@json_index');
    Route::resource('category_preventive_breakdown', 'MasterCategoryPreventiveBreakdownController');


    Route::post('cleaning_head/get_alat_simbol_kondisi', 'CleaningHeadSheetController@get_alat_simbol_kondisi');
    Route::post('cleaning_head/status', 'CleaningHeadSheetController@status');
    Route::post('cleaning_head/update', 'CleaningHeadSheetController@update');
    Route::get('cleaning_head/history', 'CleaningHeadSheetController@history');
    Route::get('cleaning_head/json_index', 'CleaningHeadSheetController@json_index');
    Route::resource('cleaning_head', 'CleaningHeadSheetController');

    Route::post('maintenance_head/get_alat_simbol_kondisi', 'MaintenanceHeadSheetController@get_alat_simbol_kondisi');
    Route::post('maintenance_head/status', 'MaintenanceHeadSheetController@status');
    Route::post('maintenance_head/data_detail', 'MaintenanceHeadSheetController@data_detail');
    Route::get('maintenance_head/json_index', 'MaintenanceHeadSheetController@json_index');
    Route::resource('maintenance_head', 'MaintenanceHeadSheetController');

    Route::post('komponen/get_default_simbol', 'MasterKomponenController@get_default_simbol');
    Route::get('komponen/json_index', 'MasterKomponenController@json_index');
    Route::resource('komponen', 'MasterKomponenController');
    
    Route::get('it/{id}/history', 'ITSheetController@history');
    Route::post('it/create_qr', 'ITSheetController@create_qr');
    Route::post('it/get_komponen_simbol_kondisi_qr', 'ITSheetController@get_komponen_simbol_kondisi_qr');
    Route::get('/it/scan', [ITSheetController::class, 'scan'])->name('it.scan');
    Route::post('it/get_komponen_simbol_kondisi', 'ITSheetController@get_komponen_simbol_kondisi');
    Route::post('it/index_group', 'ITSheetController@index_group');
    Route::get('it/json_index', 'ITSheetController@json_index');
    Route::resource('it','ITSheetController');

    Route::post('it_head/get_alat_simbol_kondisi', 'ITHeadSheetController@get_alat_simbol_kondisi');
    Route::post('it_head/status', 'ITHeadSheetController@status');
    Route::post('it_head/data_detail', 'ITHeadSheetController@data_detail');
    Route::get('it_head/json_index', 'ITHeadSheetController@json_index');
    Route::resource('it_head', 'ITHeadSheetController');

    Route::post('report/get_pic', 'ReportGASheetController@get_pic');
    Route::post('report/get_alat_cleaning', 'ReportGASheetController@get_alat_cleaning');
    Route::post('report/get_pdf', 'ReportGASheetController@report_pdf');
    Route::post('report/get_gedung', 'ReportGASheetController@get_gedung');
    Route::post('report/get_ruangan', 'ReportGASheetController@get_ruangan');
    Route::post('report/get_lokasi', 'ReportGASheetController@get_lokasi');
    Route::post('report/get_alat', 'ReportGASheetController@get_alat');
    Route::get('report/json_index', 'ReportGASheetController@json_index');
    Route::resource('report', 'ReportGASheetController');
});

require __DIR__.'/auth.php';
