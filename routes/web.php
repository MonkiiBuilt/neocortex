<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');

// Routes for user registration, login, logout, and password reset
Auth::routes();

// Resource routes (index, create, store, etc) for items
Route::resource('item', 'ItemController');
Route::post('/item/lucky', ['uses' => 'ItemController@randomImage', 'as' => 'item.randomImage']);

// Resource routes (destroy) for queue entry
Route::resource('queue', 'QueueController');

// Queue
Route::get('/queue', ['uses' => 'QueueController@index', 'as' => 'queue']);
