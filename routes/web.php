<?php

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
   return redirect('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/deposit', 'HomeController@deposit')->name('deposit');
Route::post('/deposit', 'HomeController@saveDeposit')->name('saveDeposit');
Route::get('/withdraw', 'HomeController@withdraw')->name('withdraw');
Route::post('/withdraw', 'HomeController@saveWithdraw')->name('saveWithdraw');
Route::get('/transfer', 'HomeController@transfer')->name('transfer');
Route::post('/transfer', 'HomeController@saveTransfer')->name('saveTransfer');
Route::get('/statement', 'HomeController@statement')->name('statement');
