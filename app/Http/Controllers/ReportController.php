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
use App\Models\DeductionInstallment;
use Carbon\Carbon;



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

    /**
 * Build flat "due lines" for payroll.
 * One row = one staff + one period + one amount.
 */
protected function buildPayrollDueQuery(Request $request)
{
    $month = $request->input('month'); // e.g. 2026-10
    $branchId = $request->input('branch_id');
    $sectionId = $request->input('section_id');
    $search = $request->input('search'); // staff name / salary no

    // Statuses that are allowed for payroll
    $statuses = $request->input('include_applied')
        ? ['approved', 'applied']
        : ['approved'];

    $lines = collect();

    // ---------- INSTALLMENTS ----------
    $installmentQuery = DeductionInstallment::query()
        ->with(['deduction.branch', 'deduction.section', 'deduction.department', 'deduction.deductionType'])
        ->whereHas('deduction', function ($q) use ($statuses, $branchId, $sectionId, $search) {
            $q->whereIn('status', $statuses);

            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
            if ($sectionId) {
                $q->where('section_id', $sectionId);
            }
            if ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('staff_name', 'like', "%{$search}%")
                       ->orWhere('salary_number', 'like', "%{$search}%")
                       ->orWhere('deduction_id', 'like', "%{$search}%");
                });
            }
        });

    if ($month) {
        // Match by period_start month OR payroll_period text containing month name/year
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $installmentQuery->where(function ($q) use ($start, $end, $month) {
            $q->whereBetween('period_start', [$start, $end])
              ->orWhere(function ($qq) use ($start, $end) {
                  $qq->whereNotNull('period_start')
                     ->whereNotNull('period_end')
                     ->where('period_start', '<=', $end)
                     ->where('period_end', '>=', $start);
              })
              ->orWhere('payroll_period', 'like', '%' . $start->format('F Y') . '%')
              ->orWhere('payroll_period', 'like', '%' . $start->format('M Y') . '%')
              ->orWhere('payroll_period', 'like', '%' . $start->format('Y-m') . '%');
        });
    }

    foreach ($installmentQuery->get() as $inst) {
        $d = $inst->deduction;
        $lines->push([
            'deduction_id'    => $d->deduction_id,
            'group_id'        => $d->group_id,
            'staff_name'      => $d->staff_name,
            'salary_number'   => $d->salary_number,
            'branch'          => $d->branch?->name,
            'section'         => $d->section?->name,
            'department'      => $d->department?->name,
            'type'            => $d->deductionType?->name,
            'category'        => 'installment',
            'payroll_period'  => $inst->payroll_period,
            'period_start'    => $inst->period_start,
            'period_end'      => $inst->period_end,
            'amount_due'      => (float) $inst->amount,
            'status'          => $d->status,
            'reason'          => $d->reason,
        ]);
    }

    // ---------- ONE-TIME ----------
    $oneTimeQuery = Deduction::query()
        ->with(['branch', 'section', 'department', 'deductionType'])
        ->where('category', 'one_time')
        ->whereIn('status', $statuses);

    if ($branchId) {
        $oneTimeQuery->where('branch_id', $branchId);
    }
    if ($sectionId) {
        $oneTimeQuery->where('section_id', $sectionId);
    }
    if ($search) {
        $oneTimeQuery->where(function ($q) use ($search) {
            $q->where('staff_name', 'like', "%{$search}%")
              ->orWhere('salary_number', 'like', "%{$search}%")
              ->orWhere('deduction_id', 'like', "%{$search}%");
        });
    }
    if ($month) {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = (clone $start)->endOfMonth();
        // Due in the month of effective_date (fallback deduction_date)
        $oneTimeQuery->where(function ($q) use ($start, $end) {
            $q->whereBetween('effective_date', [$start, $end])
              ->orWhere(function ($qq) use ($start, $end) {
                  $qq->whereNull('effective_date')
                     ->whereBetween('deduction_date', [$start, $end]);
              });
        });
    }

    foreach ($oneTimeQuery->get() as $d) {
        $dueDate = $d->effective_date ?? $d->deduction_date;
        $lines->push([
            'deduction_id'    => $d->deduction_id,
            'group_id'        => $d->group_id,
            'staff_name'      => $d->staff_name,
            'salary_number'   => $d->salary_number,
            'branch'          => $d->branch?->name,
            'section'         => $d->section?->name,
            'department'      => $d->department?->name,
            'type'            => $d->deductionType?->name,
            'category'        => 'one_time',
            'payroll_period'  => $dueDate?->format('F Y'),
            'period_start'    => $dueDate,
            'period_end'      => $dueDate,
            'amount_due'      => (float) $d->amount,
            'status'          => $d->status,
            'reason'          => $d->reason,
        ]);
    }

    // Sort for accountant: branch → staff → period
    return $lines->sortBy([
        ['branch', 'asc'],
        ['staff_name', 'asc'],
        ['period_start', 'asc'],
    ])->values();
}

public function payrollDue(Request $request)
{
    $lines = $this->buildPayrollDueQuery($request);

    $stats = [
        'total_due'   => $lines->sum('amount_due'),
        'staff_count' => $lines->pluck('salary_number')->unique()->count(),
        'line_count'  => $lines->count(),
    ];

    return view('reports.payroll-due', [
        'lines'    => $lines,
        'stats'    => $stats,
        'branches' => Branch::where('is_active', true)->orderBy('name')->get(),
        'sections' => Section::where('is_active', true)->orderBy('name')->get(),
        'filters'  => $request->only(['month', 'branch_id', 'section_id', 'search', 'include_applied']),
    ]);
}

public function payrollDueExcel(Request $request)
{
    $lines = $this->buildPayrollDueQuery($request);

    // Simple CSV (works without maatwebsite/excel)
    $filename = 'payroll-due-' . ($request->month ?? 'all') . '-' . now()->format('Ymd-His') . '.csv';

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function () use ($lines) {
        $out = fopen('php://output', 'w');
        fputcsv($out, [
            'Deduction ID', 'Group ID', 'Staff Name', 'Salary Number',
            'Branch', 'Section', 'Department', 'Type', 'Category',
            'Payroll Period', 'Period Start', 'Period End',
            'Amount Due', 'Status', 'Reason',
        ]);

        foreach ($lines as $row) {
            fputcsv($out, [
                $row['deduction_id'],
                $row['group_id'],
                $row['staff_name'],
                $row['salary_number'],
                $row['branch'],
                $row['section'],
                $row['department'],
                $row['type'],
                $row['category'],
                $row['payroll_period'],
                optional($row['period_start'])->format('Y-m-d'),
                optional($row['period_end'])->format('Y-m-d'),
                number_format($row['amount_due'], 2, '.', ''),
                $row['status'],
                $row['reason'],
            ]);
        }
        fclose($out);
    };

    return response()->stream($callback, 200, $headers);
}

public function payrollDuePdf(Request $request)
{
    $lines = $this->buildPayrollDueQuery($request);
    $stats = [
        'total_due'   => $lines->sum('amount_due'),
        'staff_count' => $lines->pluck('salary_number')->unique()->count(),
        'line_count'  => $lines->count(),
    ];

    // If you use barryvdh/laravel-dompdf:
    // $pdf = \PDF::loadView('reports.payroll-due-pdf', compact('lines', 'stats', 'request'));
    // return $pdf->download('payroll-due.pdf');

    // Fallback: print-friendly HTML
    return view('reports.payroll-due-print', [
        'lines'       => $lines,
        'stats'       => $stats,
        'filters'     => $request->only(['month', 'branch_id', 'section_id', 'search']),
        'generatedAt' => now(),
    ]);
}

public function payrollDuePrint(Request $request)
{
    return $this->payrollDuePdf($request);
}

public function payrollDueApply(Request $request)
{
    $request->validate([
        'month' => ['required', 'date_format:Y-m'],
    ]);

    $month     = $request->month;
    $branchId  = $request->branch_id;
    $sectionId = $request->section_id;
    $search    = $request->search;

    $start = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $end   = (clone $start)->endOfMonth();

    $deductionIds = collect();

    // ---- Installments due this month ----
    $installmentDeductionIds = \App\Models\DeductionInstallment::query()
        ->where(function ($q) use ($start, $end, $month) {
            $q->whereBetween('period_start', [$start, $end])
              ->orWhere(function ($qq) use ($start, $end) {
                  $qq->whereNotNull('period_start')
                     ->whereNotNull('period_end')
                     ->where('period_start', '<=', $end)
                     ->where('period_end', '>=', $start);
              })
              ->orWhere('payroll_period', 'like', '%' . $start->format('F Y') . '%')
              ->orWhere('payroll_period', 'like', '%' . $start->format('M Y') . '%')
              ->orWhere('payroll_period', 'like', '%' . $month . '%');
        })
        ->whereHas('deduction', function ($q) use ($branchId, $sectionId, $search) {
            $q->where('status', 'approved');
            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
            if ($sectionId) {
                $q->where('section_id', $sectionId);
            }
            if ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('staff_name', 'like', "%{$search}%")
                       ->orWhere('salary_number', 'like', "%{$search}%")
                       ->orWhere('deduction_id', 'like', "%{$search}%");
                });
            }
        })
        ->pluck('deduction_id');

    $deductionIds = $deductionIds->merge($installmentDeductionIds);

    // ---- One-time due this month ----
    $oneTimeIds = \App\Models\Deduction::query()
        ->where('category', 'one_time')
        ->where('status', 'approved')
        ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
        ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
        ->when($search, function ($q) use ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('staff_name', 'like', "%{$search}%")
                   ->orWhere('salary_number', 'like', "%{$search}%")
                   ->orWhere('deduction_id', 'like', "%{$search}%");
            });
        })
        ->where(function ($q) use ($start, $end) {
            $q->whereBetween('effective_date', [$start, $end])
              ->orWhere(function ($qq) use ($start, $end) {
                  $qq->whereNull('effective_date')
                     ->whereBetween('deduction_date', [$start, $end]);
              });
        })
        ->pluck('id');

    $deductionIds = $deductionIds->merge($oneTimeIds)->unique()->values();

    if ($deductionIds->isEmpty()) {
        return back()->with('warning', 'No approved deductions due for this month to apply.');
    }

    $count = 0;

    \Illuminate\Support\Facades\DB::transaction(function () use ($deductionIds, &$count) {
        $deductions = \App\Models\Deduction::whereIn('id', $deductionIds)
            ->where('status', 'approved')
            ->get();

        foreach ($deductions as $deduction) {
            $deduction->update([
                'status'       => 'applied',
                'applied_date' => now(),
            ]);

            \App\Models\DeductionAudit::create([
                'deduction_id' => $deduction->id,
                'user_id'      => auth()->id(),
                'action'       => 'applied',
                'description'  => 'Applied via Payroll Due report (month batch)',
            ]);

            $count++;
        }
    });

    return redirect()
        ->route('reports.payroll-due', $request->only(['month', 'branch_id', 'section_id', 'search']))
        ->with('success', "{$count} deduction(s) marked as applied for {$start->format('F Y')}.");
}
}