<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;
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
// Route::get('generate-pdf', [PDFController::class, 'generatePDF']);



Route::get('/', function () {
    return view('home');
});

Route::get('/facture', function () {
    return view('facture');
});

// Route::get('/pdf', function () {
//  return view('myPDF');});
Route::get('/pdf', 'ClientController@pdf')->name('pdf');


Route::get('{any}', function () {
    return view('home');
})->where('any','.*');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
