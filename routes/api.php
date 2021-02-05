<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\Users\UserController;
use App\Http\Controllers\API\InvoicesController;

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




/*Route::post('register', 'RegisterController@register');
Route::post('login', 'RegisterController@login');*/

Route::post('register',                         [RegisterController::class, 'register']);
Route::post('login',                            [RegisterController::class, 'login']);



Route::get('/invoices',                            [InvoicesController::class, 'index']);
Route::post('/invoice',                            [InvoicesController::class, 'store']);

Route::get('/invoices/{id}',                       [InvoicesController::class, 'show']);
Route::put('/invoice/{id}',                        [InvoicesController::class, 'update']);


Route::middleware('auth:api')->group( function (){

    Route::post('logout',                            [UserController::class, 'logout']);

    Route::get('/details',                            [UserController::class, 'details']);

    Route::post('/Add_invoice',                       [UserController::class, 'store']);

  //  Route::resource('invoices', 'App\Http\Controllers\API\InvoicesController');
    Route::resource('sections', 'App\Http\Controllers\API\SectionsController');
    Route::resource('products', 'App\Http\Controllers\API\ProductController');

});