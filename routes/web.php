<?php

use Illuminate\Routing\Router;
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

Route::get('/', 'FileController@index');
Route::get('/files', 'FileController@show');
Route::delete('/files/{id}', 'FileController@destroy');
Route::post('/upload', 'FileController@uploadFile');
Route::post('/download/{id}', 'FileController@download');
