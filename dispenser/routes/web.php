<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\VoucherController;

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

// College import routes
Route::get('college/import', [CollegeController::class, 'index']);
Route::post('college/import', [CollegeController::class, 'importExcelData']);
Route::get('college/edit/{id}', [CollegeController::class, 'edit']);
Route::post('college/update/{id}', [CollegeController::class, 'update']);
Route::delete('college/delete/{id}', [CollegeController::class, 'destroy']);

// Department import routes
Route::get('department/import', [DepartmentController::class, 'index']);
Route::post('department/import', [DepartmentController::class, 'importExcelData']);
Route::get('department/edit/{id}', [DepartmentController::class, 'edit'])->name('department.edit');
Route::put('department/update/{id}', [DepartmentController::class, 'update'])->name('department.update');
Route::delete('department/delete/{id}', [DepartmentController::class, 'destroy']);

// Course import routes
Route::get('course/import', [CourseController::class, 'index']);
Route::post('course/import', [CourseController::class, 'importExcelData']);
Route::get('course/edit/{id}', [CourseController::class, 'edit'])->name('course.edit');
Route::put('course/update/{id}', [CourseController::class, 'update'])->name('course.update');
Route::delete('course/delete/{id}', [CourseController::class, 'destroy']);

// Student import routes
Route::get('students/import', [StudentController::class, 'index'])->name('students.index');
Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
Route::get('student/edit/{id}', [StudentController::class, 'edit'])->name('student.edit');
Route::put('student/update/{id}', [StudentController::class, 'update'])->name('student.update');
Route::delete('student/delete/{id}', [StudentController::class, 'destroy'])->name('student.destroy');

// Welcome page and check student routes
Route::get('/', [StudentController::class, 'welcomeview'])->name('welcome');
Route::post('/check-student', [StudentController::class, 'checkStudent'])->name('check.student');
Route::post('/create-student-account', [StudentController::class, 'createStudentAccount'])->name('create.student.account');

// Voucher routes
Route::get('voucher/import', [VoucherController::class, 'index']);
Route::post('voucher/import', [VoucherController::class, 'importExcelData']);
Route::post('/voucher', [VoucherController::class, 'show'])->name('voucher.show');

Route::get('/voucher', function () {
    return view('voucher');
})->name('voucher');

// Authentication routes
Auth::routes();
Auth::routes(['register' => true]);
Auth::routes(['password.reset' => false]);

Route::get('/student/{id}/generate-voucher', [VoucherController::class, 'generateVoucherCode'])->name('voucher.generate');

 

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Optional route to handle custom registration logic
Route::get('register', function () {
    return abort(404);
});
Route::get('/student/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/student/store', [StudentController::class, 'store'])->name('students.store');
Route::get('/student/search', [StudentController::class, 'search'])->name('students.search');
