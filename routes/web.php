<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\DeductionTypeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DeductionAuditController;
use App\Http\Controllers\SoftDeleteLogController;
use App\Http\Controllers\UserApprovalController;
use App\Http\Controllers\SectionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth', 'can:view-logs'])->group(function () {
    Route::get('audits', [DeductionAuditController::class, 'index'])->name('audits.index');
    Route::get('audits/{deduction}', [DeductionAuditController::class, 'show'])->name('audits.show');

    Route::get('soft-delete-logs', [SoftDeleteLogController::class, 'index'])->name('soft-delete-logs.index');
    Route::get('soft-delete-logs/{softDeleteLog}', [SoftDeleteLogController::class, 'show'])->name('soft-delete-logs.show');
});

Route::middleware(['auth', 'can:clear-logs'])->group(function () {
    Route::delete('audits/clear', [DeductionAuditController::class, 'clearAll'])->name('audits.clear');
    Route::delete('audits/{audit}', [DeductionAuditController::class, 'destroy'])->name('audits.destroy');

    Route::delete('soft-delete-logs/clear', [SoftDeleteLogController::class, 'clearAll'])->name('soft-delete-logs.clear');
    Route::delete('soft-delete-logs/{softDeleteLog}', [SoftDeleteLogController::class, 'destroy'])->name('soft-delete-logs.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // ==================== DASHBOARD ====================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==================== DEDUCTIONS ====================
    Route::resource('deductions', DeductionController::class);

    // Deduction workflow actions
    Route::post('deductions/{deduction}/approve', [DeductionController::class, 'approve'])
        ->name('deductions.approve');
    Route::post('deductions/{deduction}/reject', [DeductionController::class, 'reject'])
        ->name('deductions.reject');
    Route::post('deductions/{deduction}/apply', [DeductionController::class, 'apply'])
        ->name('deductions.apply');
    Route::post('deductions/{deduction}/cancel', [DeductionController::class, 'cancel'])
        ->name('deductions.cancel');

    // ==================== BRANCHES ====================
    Route::resource('branches', BranchController::class);
    Route::post('branches/{id}/restore', [BranchController::class, 'restore'])
        ->name('branches.restore');

        // Section
Route::resource('sections', SectionController::class);
Route::post('sections/{id}/restore', [SectionController::class, 'restore'])
    ->name('sections.restore');

    // Supporting document
    Route::get('deductions/{deduction}/document', [DeductionController::class, 'document'])
    ->name('deductions.document');

    // ==================== DEPARTMENTS ====================
    Route::resource('departments', DepartmentController::class);
    Route::post('departments/{id}/restore', [DepartmentController::class, 'restore'])
        ->name('departments.restore');

    // ==================== JOB TITLES ====================
    Route::resource('job-titles', JobTitleController::class);
    Route::post('job-titles/{id}/restore', [JobTitleController::class, 'restore'])
        ->name('job-titles.restore');

    // ==================== DEDUCTION TYPES ====================
    Route::resource('deduction-types', DeductionTypeController::class);
    Route::post('deduction-types/{id}/restore', [DeductionTypeController::class, 'restore'])
        ->name('deduction-types.restore');

    // ==================== REPORTS ====================
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('reports/print', [ReportController::class, 'print'])->name('reports.print');

    // ==================== AUDIT TRAILS ====================
    Route::get('audits', [DeductionAuditController::class, 'index'])->name('audits.index');
    Route::get('audits/deduction/{deduction}', [DeductionAuditController::class, 'show'])->name('audits.show');

    // ==================== SOFT DELETE LOGS (Superadmin only recommended) ====================
    Route::get('soft-delete-logs', [SoftDeleteLogController::class, 'index'])->name('soft-delete-logs.index');
    Route::get('soft-delete-logs/{softDeleteLog}', [SoftDeleteLogController::class, 'show'])->name('soft-delete-logs.show');
    Route::delete('soft-delete-logs/{softDeleteLog}', [SoftDeleteLogController::class, 'destroy'])->name('soft-delete-logs.destroy');

    // bulk deduction
    Route::get('deductions/bulk/create', [DeductionController::class, 'bulkCreate'])
    ->name('deductions.bulk.create');
Route::post('deductions/bulk', [DeductionController::class, 'bulkStore'])
    ->name('deductions.bulk.store');

Route::post('deductions/group/{groupId}/approve', [DeductionController::class, 'groupApprove'])
    ->name('deductions.group.approve');
Route::post('deductions/group/{groupId}/reject', [DeductionController::class, 'groupReject'])
    ->name('deductions.group.reject');
Route::post('deductions/group/{groupId}/apply', [DeductionController::class, 'groupApply'])
    ->name('deductions.group.apply');

    //Due Report
    Route::get('reports/payroll-due', [ReportController::class, 'payrollDue'])
    ->name('reports.payroll-due');
Route::get('reports/payroll-due/export/excel', [ReportController::class, 'payrollDueExcel'])
    ->name('reports.payroll-due.excel');
Route::get('reports/payroll-due/export/pdf', [ReportController::class, 'payrollDuePdf'])
    ->name('reports.payroll-due.pdf');
Route::get('reports/payroll-due/print', [ReportController::class, 'payrollDuePrint'])
    ->name('reports.payroll-due.print');
    Route::post('reports/payroll-due/apply', [ReportController::class, 'payrollDueApply'])
    ->name('reports.payroll-due.apply');

});

/*
|--------------------------------------------------------------------------
| SUPERADMIN ONLY
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'superadmin'
])->prefix('superadmin')->name('superadmin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SUPERADMIN  DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'superAdmin']
    )->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/users',
        [UserApprovalController::class, 'index']
    )->name('users.index');

    Route::patch(
        '/users/{user}/approve',
        [UserApprovalController::class, 'approve']
    )->name('users.approve');

    Route::patch(
        '/users/{user}/disapprove',
        [UserApprovalController::class, 'disapprove']
    )->name('users.disapprove');

});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'admin'
])->prefix('admin')->name('admin.')->group(function () {

    Route::get(
        '/dashboard',
        [DashboardController::class, 'admin']
    )->name('dashboard');

});



/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze / Fortify)
|--------------------------------------------------------------------------
| These are usually auto-loaded by Breeze or Fortify.
| Keep them if you are using Breeze.
*/

require __DIR__.'/auth.php';