<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('college/import', [App\Http\Controllers\CollegeController::class, 'index']);
Route::post('college/import', [App\Http\Controllers\CollegeController::class, 'importExcelData']);
Route::get('college/edit/{id}', [App\Http\Controllers\CollegeController::class, 'edit']);
Route::post('college/update/{id}', [App\Http\Controllers\CollegeController::class, 'update']);
Route::delete('college/delete/{id}', [App\Http\Controllers\CollegeController::class, 'destroy']);


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
