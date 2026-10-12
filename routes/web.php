<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::resource('deductions', DeductionController::class);
    Route::post('deductions/{deduction}/approve', [DeductionController::class, 'approve'])->name('deductions.approve');
    Route::post('deductions/{deduction}/reject', [DeductionController::class, 'reject'])->name('deductions.reject');
    Route::post('deductions/{deduction}/apply', [DeductionController::class, 'apply'])->name('deductions.apply');

    // Master data
    Route::resource('branches', BranchController::class);
    Route::resource('departments', DepartmentController::class);
    // ...

    // Reports & Export
    Route::get('reports/deductions', [ReportController::class, 'index'])->name('reports.deductions');
    Route::get('export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');

    // Soft delete logs (superadmin only)
    Route::get('soft-delete-logs', [SoftDeleteLogController::class, 'index'])
        ->middleware('role:superadmin')
        ->name('soft-delete-logs.index');
});

require __DIR__.'/settings.php';
