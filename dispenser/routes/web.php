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
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('college/import', [CollegeController::class, 'index']);
    Route::post('college/import', [CollegeController::class, 'importExcelData']);
    Route::get('college/edit/{id}', [CollegeController::class, 'edit']);
    Route::post('college/update/{id}', [CollegeController::class, 'update']);
    Route::delete('college/delete/{id}', [CollegeController::class, 'destroy']);

    // department import
    Route::get('department/import', [DepartmentController::class, 'index']);
    Route::post('department/import', [DepartmentController::class, 'importExcelData']);
    Route::get('department/edit/{id}', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::put('department/update/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('department/delete/{id}', [DepartmentController::class, 'destroy']);

    // course import
    Route::get('course/import', [CourseController::class, 'index']);
    Route::post('course/import', [CourseController::class, 'importExcelData']);
    Route::get('course/edit/{id}', [CourseController::class, 'edit'])->name('department.edit');
    Route::put('course/update/{id}', [CourseController::class, 'update'])->name('department.update');
    Route::delete('course/delete/{id}', [CourseController::class, 'destroy']);

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

