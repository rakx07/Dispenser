<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    StudentController, CollegeController, DepartmentController,
    CourseController, VoucherController, SatpController, HomeController,
    TransactionController, SchoologyCredentialController, KumosoftController,
    CredentialDisplayController, EmailController, FilterController
};

// Home and welcome
Route::get('/', [StudentController::class, 'welcomeview'])->name('welcome');

/**
 * Colleges
 */
Route::prefix('college')->group(function () {
    Route::get('/import', [CollegeController::class, 'index']);
    Route::post('/import', [CollegeController::class, 'importExcelData']);
    Route::get('/edit/{id}', [CollegeController::class, 'edit']);
    Route::post('/update/{id}', [CollegeController::class, 'update']);
    Route::delete('/delete/{id}', [CollegeController::class, 'destroy']);
});

/**
 * Departments
 */
Route::prefix('department')->group(function () {
    Route::get('/import', [DepartmentController::class, 'index']);
    Route::post('/import', [DepartmentController::class, 'importExcelData']);
    Route::get('/edit/{id}', [DepartmentController::class, 'edit'])->name('department.edit');
    Route::put('/update/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/delete/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');
});

/**
 * Courses
 */
Route::prefix('course')->group(function () {
    Route::get('/import', [CourseController::class, 'index']);
    Route::post('/import', [CourseController::class, 'importExcelData']);
    Route::get('/edit/{id}', [CourseController::class, 'edit'])->name('course.edit');
    Route::put('/update/{id}', [CourseController::class, 'update'])->name('course.update');
    Route::delete('/delete/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
});

/**
 * Students (public endpoints used by the kiosk/portal)
 */
Route::prefix('students')->group(function () {
    // Record a credential view (ALWAYS inserts a row)
    Route::post('/transactions/record', [TransactionController::class, 'recordShowPassword'])
        ->name('transactions.record');

    Route::post('/check-student', [StudentController::class, 'checkStudent'])->name('check.student');
    Route::post('/voucher-and-satp', [StudentController::class, 'handleVoucherAndSatp'])
        ->name('students.voucherAndSatp');
});

/**
 * Voucher
 */
Route::prefix('voucher')->group(function () {
    Route::get('/import', [VoucherController::class, 'index']);
    Route::post('/import', [VoucherController::class, 'importExcelData']);
    Route::post('/show', [VoucherController::class, 'show'])->name('voucher.show');
    Route::get('/', [StudentController::class, 'showVoucher'])->name('voucher');
    // FIXED: avoid /voucher/voucher/remove
    Route::get('/remove/{id}', [VoucherController::class, 'removeVoucherCode'])->name('voucher.remove');
});

// Auth
Auth::routes(['register' => false, 'reset' => false]);

// Home
Route::get('/home', [HomeController::class, 'index'])->name('home');

/**
 * Auth-only admin/office features
 */
Route::middleware(['auth'])->group(function () {
    // SATP account management (keep ONE store route)
    Route::get('/assign/{studentId}', [SatpController::class, 'assign'])->name('satpaccount.assign');
    Route::post('/store/{studentId}', [SatpController::class, 'store'])->name('satpaccount.store');

    // Student import & CRUD
    Route::get('students/import', [StudentController::class, 'index']);
    Route::post('students/import', [StudentController::class, 'import']);
    Route::get('/edit/{id}', [StudentController::class, 'edit'])->name('student.edit');
    Route::put('/update/{id}', [StudentController::class, 'update'])->name('student.update');
    Route::delete('/delete/{id}', [StudentController::class, 'destroy'])->name('student.destroy');
    Route::post('/create-student-account', [StudentController::class, 'createStudentAccount'])->name('create.student.account');
    Route::get('/student/create', [StudentController::class, 'create'])->name('student.create');
    Route::post('/student/store', [StudentController::class, 'store'])->name('student.store');
    Route::get('/student/search', [StudentController::class, 'search'])->name('student.search');
    Route::get('/students/search-ajax', [StudentController::class, 'searchAjax'])->name('students.search.ajax');

    // SATP bulk
    Route::get('satpaccount/create', [SatpController::class, 'create'])->name('satpaccount.create');
    Route::get('satpaccount/import', [SatpController::class, 'index']);
    Route::post('satpaccount/import', [SatpController::class, 'importExcelData']);

    // Transactions (audit)
    Route::get('/audit/transactions', [TransactionController::class, 'index'])->name('audit.transactions');
    Route::get('/audit/transactions/export', [TransactionController::class, 'export'])->name('audit.transactions.export');
    Route::get('/transactions/search', [TransactionController::class, 'search'])->name('transactions.search');

    // Emails
    Route::get('/emails/import', [EmailController::class, 'index'])->name('emails.index');
    Route::post('/emails/import-emails', [EmailController::class, 'importExcelData'])->name('emails.import');
    Route::get('/emails/create', [EmailController::class, 'create'])->name('emails.create');
    Route::post('/emails/store', [EmailController::class, 'store'])->name('emails.store');

    // Schoology
    Route::get('/schoology-credentials', [SchoologyCredentialController::class, 'index'])->name('schoology-credentials.index');
    Route::get('/schoology-credentials/create', [SchoologyCredentialController::class, 'create'])->name('schoology-credentials.create');
    Route::post('/schoology-credentials', [SchoologyCredentialController::class, 'store'])->name('schoology-credentials.store');
    Route::get('/schoology-credentials/import', [SchoologyCredentialController::class, 'index'])->name('schoology-credentials.import');
    Route::post('/schoology-credentials/import', [SchoologyCredentialController::class, 'importExcelData']);

    // Kumosoft
    Route::get('/kumosoft/import', [KumosoftController::class, 'index'])->name('kumosoft.import');
    Route::post('/kumosoft/import', [KumosoftController::class, 'importExcelData']);
    Route::get('/kumosoft/create', [KumosoftController::class, 'create'])->name('kumosoft.create');
    Route::post('/kumosoft/store', [KumosoftController::class, 'store'])->name('kumosoft.store');

    // Students export
    Route::get('/students/export', [StudentController::class, 'exportExcel'])->name('students.export');

    // Controls
    Route::get('/controls', [CredentialDisplayController::class, 'index'])->name('controls.index');
    Route::post('/controls/toggle', [CredentialDisplayController::class, 'toggle'])->name('controls.toggle');

    // Filters
    Route::get('/filters', [FilterController::class, 'index'])->name('filters.index');
    Route::post('/filters/{school_id}', [FilterController::class, 'update'])->name('filters.update');
    Route::post('/filters/{school_id}/voucher/generate', [FilterController::class, 'generateVoucher'])->name('filters.voucher.generate');
    Route::get('filters/{school_id}/edit', [FilterController::class, 'edit'])->name('filters.edit');
});
