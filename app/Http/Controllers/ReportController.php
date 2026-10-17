<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Branch;
use App\Models\Section;
use App\Models\Department;
use App\Models\DeductionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\DeductionsExport;          // You will create this later
use Barryvdh\DomPDF\Facade\Pdf;



class ReportController extends Controller
{
    /**
     * Reports dashboard / filter page
     */
    public function index(Request $request)
    {
        $query = Deduction::with(['branch', 'section', 'department', 'jobTitle', 'deductionType', 'creator']);

        // Filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
            if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('deduction_type_id')) {
            $query->where('deduction_type_id', $request->deduction_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('deduction_date', [$request->date_from, $request->date_to]);
        }

        // Summary statistics
        $stats = [
            'total_amount'      => (clone $query)->sum('amount'),
            'total_count'       => (clone $query)->count(),
            'pending_count'     => (clone $query)->where('status', 'pending')->count(),
            'approved_count'    => (clone $query)->where('status', 'approved')->count(),
            'applied_count'     => (clone $query)->where('status', 'applied')->count(),
            'rejected_count'    => (clone $query)->where('status', 'rejected')->count(),
        ];

        $deductions = $query->latest()->paginate(30)->withQueryString();

        return view('reports.index', [
            'deductions'     => $deductions,
            'stats'          => $stats,
            'branches'       => Branch::where('is_active', true)->orderBy('name')->get(),
            'sections'       => Section::where('is_active', true)->orderBy('name')->get(),
            'departments'    => Department::where('is_active', true)->orderBy('name')->get(),
            'deductionTypes' => DeductionType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
{
    $filters = $request->only([
        'branch_id', 'section_id', 'department_id', 'deduction_type_id',
        'status', 'category', 'date_from', 'date_to',
    ]);

    $fileName = 'deductions_report_' . now()->format('Y_m_d_His') . '.xlsx';

    return (new DeductionsExport($filters))->download($fileName);
}

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Deduction::with(['branch', 'section', 'department', 'jobTitle', 'deductionType', 'creator']);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
         if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('deduction_date', [$request->date_from, $request->date_to]);
        }

        $deductions = $query->latest()->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'deductions' => $deductions,
            'generatedAt' => now(),
            'filters' => $request->all(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('deductions_report_' . now()->format('Y_m_d_His') . '.pdf');
    }

    /**
     * Print view (browser print)
     */
    public function print(Request $request)
    {
        $query = Deduction::with(['branch', 'section', 'department', 'jobTitle', 'deductionType']);

        // Apply same filters as index
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('deduction_date', [$request->date_from, $request->date_to]);
        }

        $deductions = $query->latest()->get();

        return view('reports.print', compact('deductions'));
    }
}