<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    StudentController, CollegeController, DepartmentController,
    CourseController, VoucherController, SatpController, HomeController, TransactionController, SchoologyCredentialController, KumosoftController
};
use App\Http\Controllers\FilterController;

use App\Http\Controllers\CredentialDisplayController;

use App\Http\Controllers\EmailController;

// Home and welcome route
Route::get('/', [StudentController::class, 'welcomeview'])->name('welcome');

// College routes
Route::prefix('college')->group(function () {
    Route::get('/import', [CollegeController::class, 'index']);
    Route::post('/import', [CollegeController::class, 'importExcelData']);
    Route::get('/edit/{id}', [CollegeController::class, 'edit']);
    Route::post('/update/{id}', [CollegeController::class, 'update']);
    Route::delete('/delete/{id}', [CollegeController::class, 'destroy']);
});

// Department routes
Route::prefix('department')->group(function () {
    Route::get('/import', [DepartmentController::class, 'index']);
    Route::post('/import', [DepartmentController::class, 'importExcelData']);
    Route::get('/edit/{id}', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::put('/update/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/delete/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');
});

// Course routes
Route::prefix('course')->group(function () {
    Route::get('/import', [CourseController::class, 'index']);
    Route::post('/import', [CourseController::class, 'importExcelData']);
    Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('course.edit');
    Route::put('/update/{id}', [CourseController::class, 'update'])->name('course.update');
    Route::delete('/delete/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
});

// Student routes
Route::prefix('students')->group(function () {
    Route::post('/transactions/record-show-password', [TransactionController::class, 'recordShowPassword'])->name('transactions.recordShowPassword');
    Route::post('/transactions/record-show', [TransactionController::class, 'recordShow'])->name('transactions.recordShow');
    Route::post('/check-student', [StudentController::class, 'checkStudent'])->name('check.student');
    Route::post('/voucher-and-satp', [StudentController::class, 'handleVoucherAndSatp'])->name('students.voucherAndSatp'); // Updated route for combined functionality
});

// Voucher routes
Route::prefix('voucher')->group(function () {
    Route::get('/import', [VoucherController::class, 'index']);
    Route::post('/import', [VoucherController::class, 'importExcelData']);
    Route::post('/show', [VoucherController::class, 'show'])->name('voucher.show');
    Route::get('/', [StudentController::class, 'showVoucher'])->name('voucher'); // Updated route to fetch email and password
    Route::get('/voucher/remove/{id}', [VoucherController::class, 'removeVoucherCode'])->name('voucher.remove');

});



// SATP account routes (only accessible to authenticated users)
Route::middleware(['auth'])->prefix('satpaccount')->group(function () {
  
});

// Authentication routes
Auth::routes(['register' => false, 'reset' => false]);

// Home route for authenticated users
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Additional student routes requiring authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/assign/{studentId}', [SatpController::class, 'assign'])->name('satpaccount.assign'); // Assign SATP account to a student
    Route::post('/store/{studentId}', [SatpController::class, 'store'])->name('satpaccount.store'); // Store SATP account details for a student
    // Route::get('/import', [StudentController::class, 'index'])->name('students.index');
    // Route::post('/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('students/import', [StudentController::class, 'index']);
    Route::post('students/import', [StudentController::class, 'import']);

    Route::get('/edit/{id}', [StudentController::class, 'edit'])->name('student.edit');
    Route::put('/update/{id}', [StudentController::class, 'update'])->name('student.update');
    Route::delete('/delete/{id}', [StudentController::class, 'destroy'])->name('student.destroy');
    Route::post('/create-student-account', [StudentController::class, 'createStudentAccount'])->name('create.student.account');
    Route::get('/student/create', [StudentController::class, 'create'])->name('student.create');
    Route::post('/student/store', [StudentController::class, 'store'])->name('student.store');
    Route::get('/student/search', [StudentController::class, 'search'])->name('student.search');
    Route::get('satpaccount/create', [App\Http\Controllers\SatpController::class, 'create'])->name('satpaccount.create');
    Route::post('satpaccount/store', [App\Http\Controllers\SatpController::class, 'store'])->name('satpaccount.store'); 
    Route::get('satpaccount/import',[App\Http\Controllers\SatpController::class, 'index']);
    Route::post('satpaccount/import',[App\Http\Controllers\SatpController::class, 'importExcelData']);
    Route::get('/audit/transactions', [TransactionController::class, 'index'])->name('audit.transactions');
    Route::get('/audit/transactions/export', [TransactionController::class, 'export'])->name('audit.transactions.export');
    // Route::post('/emails/import-emails', [App\Http\Controllers\EmailController::class, 'importExcelData'])->name('emails.import');

    Route::get('/emails/import', [EmailController::class, 'index'])->name('emails.index');
    Route::post('/emails/import-emails', [EmailController::class, 'importExcelData'])->name('emails.import');

    //Add Email Function
    Route::get('/emails/create', [EmailController::class, 'create'])->name('emails.create');
    Route::post('/emails/store', [EmailController::class, 'store'])->name('emails.store');
    // Schoology upload form (index)
    Route::get('/schoology-credentials', [SchoologyCredentialController::class, 'index'])->name('schoology-credentials.index');
    // Manual add form
    Route::get('/schoology-credentials/create', [SchoologyCredentialController::class, 'create'])->name('schoology-credentials.create');
    // Store manual input
    Route::post('/schoology-credentials', [SchoologyCredentialController::class, 'store'])->name('schoology-credentials.store');
    // Handle Excel upload
    // Show the form when visiting /schoology-credentials/import (GET)
    Route::get('/schoology-credentials/import', [SchoologyCredentialController::class, 'index'])->name('schoology-credentials.import');
    // Handle Excel import submission (POST)
    Route::post('/schoology-credentials/import', [SchoologyCredentialController::class, 'importExcelData']);
    //Search Route
    Route::get('/transactions/search', [TransactionController::class, 'search'])->name('transactions.search');
    
    Route::get('/students/search-ajax', [StudentController::class, 'searchAjax'])->name('students.search.ajax');

    // Show Excel form for upload
    Route::get('/kumosoft/import', [KumosoftController::class, 'index'])->name('kumosoft.import');

    // Process Excel
    Route::post('/kumosoft/import', [KumosoftController::class, 'importExcelData']);

    // Manual entry
    Route::get('/kumosoft/create', [KumosoftController::class, 'create'])->name('kumosoft.create');
    Route::post('/kumosoft/store', [KumosoftController::class, 'store'])->name('kumosoft.store');
    //Excel Export
    Route::get('/students/export', [StudentController::class, 'exportExcel'])->name('students.export');

    Route::get('/controls', [CredentialDisplayController::class, 'index'])->name('controls.index');
    Route::post('/controls/toggle', [CredentialDisplayController::class, 'toggle'])->name('controls.toggle');

    Route::get('/filters', [FilterController::class, 'index'])->name('filters.index');
    Route::post('/filters/{school_id}', [FilterController::class, 'update'])->name('filters.update');
    Route::post('/filters/{school_id}/voucher/generate', [FilterController::class, 'generateVoucher'])
    ->name('filters.voucher.generate');
    Route::get('filters/{school_id}/edit', [FilterController::class, 'edit'])
    ->name('filters.edit');
    
});
