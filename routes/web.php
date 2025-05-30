<?php

use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Debt\DebtController;
use App\Http\Controllers\Category\SubcategoryController;
use App\Http\Controllers\Debt\DebtWithSupplierController;
use App\Http\Controllers\FuelStation\FulstationController;
use App\Http\Controllers\Print\PrinterController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\TractorDriver\TractorDriverController;
use App\Http\Controllers\Vehicle\VehicleController;
use App\Models\Debt;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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

$controller_path = 'App\Http\Controllers';
Session::put('theme', 'dark');
Session::put('locale', 'ar');

Route::group(['middleware' => ['auth']], function () {
  Route::get('/theme/{theme}', function($theme){
    Session::put('theme',$theme);
    return redirect()->back();
  });

  Route::get('/lang/{lang}', function($lang){
    Session::put('locale', $lang);
    App::setLocale($lang);
    return redirect()->back();
  });
});

// Main Page Route
Route::get('/', 'App\Http\Controllers\dashboard\Analytics@index2')->name('dashboard-analytics')->middleware('auth');
Route::get('/template', 'App\Http\Controllers\dashboard\Analytics@index')->name('dashboard-analytics-template')->middleware('auth');

// layout
Route::group(['middleware' => ['auth']], function () {
  // ** start route services
  Route::resource('services/building-materals', CategoryController::class)->names('services.building-materials');
  Route::resource('services/subcategory', SubcategoryController::class)->names('services.subcategory');
  Route::resource('services/tractor-driver', TractorDriverController::class)->names('services.tractor-driver');
  Route::resource('services/vehicle', VehicleController::class)->names('services.vehicle');
  Route::post('services/vehicle/{vehicle}/added-date', [VehicleController::class, 'addDateIns'])->name('services.vehicle.added-date');
  // ** end of services


  Route::resource('debt', DebtController::class);
  Route::patch('debt/pays/{debt}', [DebtController::class, 'payDebt'])->name('debt.pay');
  Route::get('debt/status/paid', [DebtController::class, 'indexPaid'])->name('debt.index-paid');

  Route::resource('debt-supplier', DebtWithSupplierController::class);
  Route::patch('debt-supplier/pays/{debt}', [DebtWithSupplierController::class, 'payDebt'])->name('debt-supplier.pay');
  Route::get('debt-supplier/status/paid', [DebtWithSupplierController::class, 'indexPaid'])->name('debt-supplier.index-paid');

  Route::post('debt/search', [DebtController::class, 'searchName'])->name('debt.search');

  Route::resource('fuel-stations', FulstationController::class);
  Route::patch('fuel-stations/status/{id}', [FulstationController::class, 'status'])->name('fuel-stations.status');
  Route::get('fuel-stations/status/paid', [FulstationController::class, 'indexPaid'])->name('fuel-stations.index-paid');
  Route::get('fuel-stations/search', [FulstationController::class, 'indexA'])->name('fuel-stations.index-search');
  Route::post('fuel-stations/change-status', [FulstationController::class, 'updateStatus'])->name('fuel-stations.update.status');



  Route::get('print/printer-facteur/{debt}/{fullname}', [PrinterController::class, 'factuerClient'])->name('debt.printer-facteur-client');


});

// authentication
Route::get('/auth/login-basic', 'App\Http\Controllers\authentications\LoginBasic@index')->name('login');
Route::get('/auth/register-basic', 'App\Http\Controllers\authentications\RegisterBasic@index')->name('auth-register-basic');
Route::post('/auth/register-action', 'App\Http\Controllers\authentications\RegisterBasic@register');
Route::post('/auth/login-action', 'App\Http\Controllers\authentications\LoginBasic@login');
Route::get('/auth/forgot-password-basic', 'App\Http\Controllers\authentications\ForgotPasswordBasic@index')->name('auth-reset-password-basic');
Route::get('/auth/logout', 'App\Http\Controllers\authentications\LogoutBasic@logout')->name('auth-logout');

Route::get('list/debt/supplier/', function() {
  $date = now();
      $dateToday = $date->format('Y-m-d');

      $debts = Debt::whereStatus('unpaid')->where('tractor_driver_id','!=',1)->orderBy('id', 'desc')->get();;

      return view('content.Liste.index', compact('debts', ));
});

Route::get('password/hash', function() {
  $password = '123456789';
  $hashedPassword = Hash::make($password);
  return $hashedPassword;
});


