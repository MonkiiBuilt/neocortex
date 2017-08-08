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

// History
Route::get('/item/history', ['uses' => 'ItemController@history', 'as' => 'history']);
Route::get('/item/resurrect/{id}', ['uses' => 'ItemController@resurrect', 'as' => 'resurrect']);

// Resource routes (index, create, store, etc) for items
Route::resource('item', 'ItemController');

// Route to post to to create a new item with a random image
Route::post('/item/lucky', ['uses' => 'ItemController@randomImage', 'as' => 'item.randomImage']);

// Route just to dispay the url of a random image
Route::get('lucky-url', 'ItemController@randomImageUrl');

// Resource routes (destroy) for queue entry
Route::resource('queue', 'QueueController');

// Queue
Route::get('/queue', ['uses' => 'QueueController@index', 'as' => 'queue']);
