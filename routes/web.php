<?php declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/transaction', 'TransactionController@index')->name('transaction');
Route::post('/transaction', 'TransactionController@execute')->name('executeTransaction');
Route::get('/add-balance', 'TransactionController@addBalance')->name('addBalance');
Route::get('/report', 'ReportController@index')->name('report');
Route::get('/new-account', 'AccountController@new')->name('newAccount');
