<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CourseController;



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
//department import
Route::get('department/import', [App\Http\Controllers\DepartmentController::class, 'index']);
Route::post('department/import', [App\Http\Controllers\DepartmentController::class, 'importExcelData']);
Route::get('department/edit/{id}', [App\Http\Controllers\DepartmentController::class, 'edit'])->name('department.edit');
Route::put('department/update/{id}', [App\Http\Controllers\DepartmentController::class, 'update'])->name('department.update');
Route::delete('department/delete/{id}', [App\Http\Controllers\DepartmentController::class, 'destroy']);
//course import
Route::get('course/import', [App\Http\Controllers\CourseController::class, 'index']);
Route::post('course/import', [App\Http\Controllers\CourseController::class, 'importExcelData']);
Route::get('course/edit/{id}', [App\Http\Controllers\CourseController::class, 'edit'])->name('department.edit');
Route::put('course/update/{id}', [App\Http\Controllers\CourseController::class, 'update'])->name('department.update');
Route::delete('course/delete/{id}', [App\Http\Controllers\CourseController::class, 'destroy']);

// Example route in routes/web.php
Route::get('/', [App\Http\Controllers\CourseController::class, 'getCourses']);

Route::get('students/import', [StudentController::class, 'index'])->name('students.index');
Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
Route::get('student/edit/{id}', [StudentController::class, 'edit']);
Route::post('student/update/{id}', [StudentController::class, 'update']);
Route::delete('student/delete/{id}', [StudentController::class, 'destroy']);

Route::post('/check-student', [StudentController::class, 'checkStudent'])->name('check.student');
Route::post('/create-student-account', [StudentController::class, 'createStudentAccount'])->name('create.student.account');


Route::get('/signin', function () {
    return view('signin');
})->name('signin');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

