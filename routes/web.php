<?php

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
Route::post('/upazila-list', 'HomeController@upazila_list')->name('upazila.list');


Route::group(['prefix' => 'user', 'as' => 'user.'], function(){
    Route::post('store', 'HomeController@store')->name('store');
    Route::post('list', 'HomeController@userList')->name('list');
    Route::post('edit', 'HomeController@edit')->name('edit');
    Route::post('show', 'HomeController@show')->name('show');
    Route::post('delete', 'HomeController@destroy')->name('delete');
    Route::post('change-status', 'HomeController@changeStatus')->name('change.status');
    Route::post('bulk-action-delete', 'HomeController@bulkActionDelete')->name('bulk.action.delete');
});
