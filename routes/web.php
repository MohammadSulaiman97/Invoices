<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoicesAttachmentsController;
use App\Http\Controllers\InvoiceAchiveController;
use Illuminate\Support\Facades\Auth;

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
    return view('auth.login');
});



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('invoices', InvoicesController::class);

Route::resource('sections', SectionsController::class);

Route::resource('products', ProductsController::class);

Route::resource('InvoiceAttachments', InvoicesAttachmentsController::class);

Route::resource('Archive', InvoiceAchiveController::class);

/* ...................................InvoicesController............................................... */

Route::get('/section/{id}', 'App\Http\Controllers\InvoicesController@getproducts');

Route::get('/edit_invoice/{id}', 'App\Http\Controllers\InvoicesController@edit');

Route::get('/Status_show/{id}', 'App\Http\Controllers\InvoicesController@show')->name('Status_show');
Route::post('/Status_Update/{id}', 'App\Http\Controllers\InvoicesController@Status_Update')->name('Status_Update');

Route::get('/Invoice_Paid', 'App\Http\Controllers\InvoicesController@Invoice_Paid');
Route::get('/Invoice_Partial', 'App\Http\Controllers\InvoicesController@Invoice_Partial');
Route::get('/Invoice_UnPaid', 'App\Http\Controllers\InvoicesController@Invoice_UnPaid');

Route::get('Print_invoice/{id}','App\Http\Controllers\InvoicesController@Print_invoice');



Route::get('export_invoices', 'App\Http\Controllers\InvoicesController@export');


Route::get('MarkAsRead_all','App\Http\Controllers\InvoicesController@MarkAsRead_all')->name('MarkAsRead_all');


/* .......................................InvoicesDetailsController ........................................... */

Route::get('/InvoicesDetails/{id}', 'App\Http\Controllers\InvoicesDetailsController@edit');

Route::get('download/{invoice_number}/{file_name}', 'App\Http\Controllers\InvoicesDetailsController@get_file');

Route::get('View_file/{invoice_number}/{file_name}', 'App\Http\Controllers\InvoicesDetailsController@open_file');

Route::post('delete_file', 'App\Http\Controllers\InvoicesDetailsController@destroy')->name('delete_file');


/* .......................................permission ........................................... */
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles','App\Http\Controllers\RoleController');
    Route::resource('users','App\Http\Controllers\UserController');
});

/* .......................................report ........................................... */
Route::get('invoices_report', 'App\Http\Controllers\Invoices_Report@index');

Route::post('Search_invoices', 'App\Http\Controllers\Invoices_Report@Search_invoices');

Route::get('customers_report', 'App\Http\Controllers\Customers_Report@index')->name("customers_report");

Route::post('Search_customers', 'App\Http\Controllers\Customers_Report@Search_customers');

/* .......................................AdminController ........................................... */

Route::get('/{page}', [AdminController::class, 'index']);
