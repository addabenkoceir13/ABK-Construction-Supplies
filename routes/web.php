<?php

use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Debt\DebtController;
use App\Http\Controllers\Category\SubcategoryController;
use App\Http\Controllers\Debt\DebtWithSupplierController;
use Illuminate\Support\Facades\App;
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
Route::get('/', 'App\Http\Controllers\dashboard\Analytics@index')->name('dashboard-analytics')->middleware('auth');

// layout
Route::group(['middleware' => ['auth']], function () {

  Route::resource('building-materals', CategoryController::class);
  Route::resource('debt', DebtController::class);
  Route::resource('debt-supplier', DebtWithSupplierController::class);
  Route::patch('debt/pays/{debt}', [DebtController::class, 'payDebt'])->name('debt.pay');
  Route::resource('subcategory', SubcategoryController::class);

});

// authentication
Route::get('/auth/login-basic', 'App\Http\Controllers\authentications\LoginBasic@index')->name('login');
Route::get('/auth/register-basic', 'App\Http\Controllers\authentications\RegisterBasic@index')->name('auth-register-basic');
Route::post('/auth/register-action', 'App\Http\Controllers\authentications\RegisterBasic@register');
Route::post('/auth/login-action', 'App\Http\Controllers\authentications\LoginBasic@login');
Route::get('/auth/forgot-password-basic', 'App\Http\Controllers\authentications\ForgotPasswordBasic@index')->name('auth-reset-password-basic');
Route::get('/auth/logout', 'App\Http\Controllers\authentications\LogoutBasic@logout')->name('auth-logout');




