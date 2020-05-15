<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/folders', 'FolderController@index');
Route::post('/pages', 'PageController@index');
Route::post('/page/create', 'PageController@create');
Route::post('/page/save', 'PageController@save');
Route::post('/page/publish', 'PageController@publish');
