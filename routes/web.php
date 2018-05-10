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

Route::get('/', 'HomeController@index');
Route::get('/login', 'HomeController@index')->name('login');
Route::post('/login', 'HomeController@login');
Route::post('/signup', 'HomeController@signup');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard', 'DashboardController@dashboard');
    Route::get('/dashboard/transactions', 'DashboardController@transactions');
    Route::get('/dashboard/potsize', 'DashboardController@potSize');
    Route::get('/dashboard/chips', 'DashboardController@chips');
    Route::get('/dashboard/history', 'DashboardController@history');
    Route::get('/dashboard/raffle', 'DashboardController@raffle');
    Route::post('/dashboard/add', 'DashboardController@addTransaction');
    Route::post('/dashboard/upload', 'DashboardController@uploadPicture');
    Route::post('/dashboard/edit', 'DashboardController@edit');
    Route::get('/coinimp', 'DashboardController@getCoinimpScript');

    Route::get('/logout', 'DashboardController@logout');
});
