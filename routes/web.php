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

use App\Kartica;
use App\Zona;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/radnici', 'RadnikController@index');

Route::get('/radnici/{id}', 'RadnikController@show');

Route::post('/radnici', 'RadnikController@store');

Route::patch('/radnici/{id}', 'RadnikController@update');

Route::delete('/radnici/{id}', 'RadnikController@destroy');



Route::get('/kartice', function () {
    return Kartica::all(['id', 'kod']);
});

Route::get('/kartice/{id}', function ($id) {
    return Kartica::with('radnik')->findOrFail($id);
});



Route::get('/sluzbe', 'SluzbaController@index');

Route::get('/sluzbe/{id}', 'SluzbaController@show');

Route::post('/sluzbe', 'SluzbaController@store');



Route::get('/grupe', 'GrupaController@index');

Route::get('/grupe/{id}', 'GrupaController@show');

Route::post('/grupe', 'GrupaController@store');



Route::get('/zone', function() {
   return Zona::all(['id', 'ime']);
});