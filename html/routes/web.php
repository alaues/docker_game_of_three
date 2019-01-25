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
    return view('welcome');
});

Route::post('/game/create', 'GameController@create');
Route::get('/game/quit', 'GameController@quit');
Route::get('/game/{id?}', 'GameController@index');
Route::post('/game/join', 'GameController@join');
Route::post('/game/move', 'GameController@move');

