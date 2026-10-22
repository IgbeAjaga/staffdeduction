<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Branch;
use App\Models\Section;
use App\Models\Department;
use App\Models\DeductionType;
use App\Models\DeductionInstallment;
use App\Models\DeductionAudit;
use App\Exports\DeductionsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
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

        $stats = [
            'total_amount'   => (clone $query)->sum('amount'),
            'total_count'    => (clone $query)->count(),
            'pending_count'  => (clone $query)->where('status', 'pending')->count(),
            'approved_count' => (clone $query)->where('status', 'approved')->count(),
            'applied_count'  => (clone $query)->where('status', 'applied')->count(),
            'rejected_count' => (clone $query)->where('status', 'rejected')->count(),
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

    public function exportExcel(Request $request)
    {
        $filters = $request->only([
            'branch_id', 'section_id', 'department_id', 'deduction_type_id',
            'status', 'category', 'date_from', 'date_to',
        ]);

        $fileName = 'deductions_report_' . now()->format('Y_m_d_His') . '.xlsx';

        return (new DeductionsExport($filters))->download($fileName);
    }

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
            'deductions'  => $deductions,
            'generatedAt' => now(),
            'filters'     => $request->all(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('deductions_report_' . now()->format('Y_m_d_His') . '.pdf');
    }

    public function print(Request $request)
    {
        $query = Deduction::with(['branch', 'section', 'department', 'jobTitle', 'deductionType']);

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
     * Strict month match: period_start year+month, OR payroll_period text when period_start is null.
     * No overlapping date-range OR (that caused Oct+Nov bleed).
     */
    protected function applyMonthFilter($query, string $month): void
    {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $year = (int) $start->year;
        $monthNum = (int) $start->month;

        $fullName = $start->format('F Y');      // October 2026
        $shortName = $start->format('M Y');     // Oct 2026
        $numeric = $month;                      // 2026-10

        $query->where(function ($q) use ($year, $monthNum, $fullName, $shortName, $numeric) {
            $q->where(function ($qq) use ($year, $monthNum) {
                $qq->whereNotNull('period_start')
                    ->whereYear('period_start', $year)
                    ->whereMonth('period_start', $monthNum);
            })->orWhere(function ($qq) use ($fullName, $shortName, $numeric) {
                $qq->whereNull('period_start')
                    ->where(function ($q2) use ($fullName, $shortName, $numeric) {
                        $q2->where('payroll_period', $fullName)
                            ->orWhere('payroll_period', $shortName)
                            ->orWhere('payroll_period', $numeric);
                    });
            });
        });
    }

    protected function buildPayrollDueQuery(Request $request)
    {
        $month = $request->input('month');
        $branchId = $request->input('branch_id');
        $sectionId = $request->input('section_id');
        $search = $request->input('search');
        $includeApplied = $request->boolean('include_applied');

        $lines = collect();

        // ---------- INSTALLMENTS (use installment.status) ----------
        $installmentQuery = DeductionInstallment::query()
            ->with(['deduction.branch', 'deduction.section', 'deduction.department', 'deduction.deductionType'])
            ->when(
                $includeApplied,
                fn ($q) => $q->whereIn('status', ['approved', 'applied']),
                fn ($q) => $q->where('status', 'approved')
            )
            ->whereHas('deduction', function ($q) use ($branchId, $sectionId, $search) {
                $q->whereIn('status', ['approved', 'applied']);

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
            $this->applyMonthFilter($installmentQuery, $month);
        }

        foreach ($installmentQuery->get() as $inst) {
            $d = $inst->deduction;
            if (! $d) {
                continue;
            }

            $lines->push([
                'installment_id'  => $inst->id,
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
                'status'          => $inst->status,
                'reason'          => $d->reason,
            ]);
        }

        // ---------- ONE-TIME ----------
        $oneTimeStatuses = $includeApplied ? ['approved', 'applied'] : ['approved'];

        $oneTimeQuery = Deduction::query()
            ->with(['branch', 'section', 'department', 'deductionType'])
            ->where('category', 'one_time')
            ->whereIn('status', $oneTimeStatuses);

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
            $year = (int) $start->year;
            $monthNum = (int) $start->month;

            $oneTimeQuery->where(function ($q) use ($year, $monthNum) {
                $q->where(function ($qq) use ($year, $monthNum) {
                    $qq->whereNotNull('effective_date')
                        ->whereYear('effective_date', $year)
                        ->whereMonth('effective_date', $monthNum);
                })->orWhere(function ($qq) use ($year, $monthNum) {
                    $qq->whereNull('effective_date')
                        ->whereYear('deduction_date', $year)
                        ->whereMonth('deduction_date', $monthNum);
                });
            });
        }

        foreach ($oneTimeQuery->get() as $d) {
            $dueDate = $d->effective_date ?? $d->deduction_date;
            $lines->push([
                'installment_id'  => null,
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

        // Staff remaining by salary_number
        $salaryNumbers = $lines->pluck('salary_number')->unique()->filter()->values();
        $remainingBySalary = [];

        foreach ($salaryNumbers as $sn) {
            $instRemaining = DeductionInstallment::query()
                ->whereIn('status', ['pending', 'approved'])
                ->whereHas('deduction', fn ($q) => $q->where('salary_number', $sn))
                ->sum('amount');

            $oneTimeRemaining = Deduction::query()
                ->where('salary_number', $sn)
                ->where('category', 'one_time')
                ->whereIn('status', ['pending', 'approved'])
                ->sum('amount');

            $remainingBySalary[$sn] = (float) $instRemaining + (float) $oneTimeRemaining;
        }

        $lines = $lines->map(function ($row) use ($remainingBySalary) {
            $row['staff_remaining'] = $remainingBySalary[$row['salary_number']] ?? 0;

            return $row;
        });

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
                'Amount Due', 'Staff Remaining', 'Status', 'Reason',
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
                    number_format($row['staff_remaining'] ?? 0, 2, '.', ''),
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

        $month = $request->month;
        $branchId = $request->branch_id;
        $sectionId = $request->section_id;
        $search = $request->search;

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $appliedInstallments = 0;
        $appliedOneTime = 0;

        DB::transaction(function () use (
            $month, $branchId, $sectionId, $search, $start,
            &$appliedInstallments, &$appliedOneTime
        ) {
            $installmentQuery = DeductionInstallment::query()
                ->where('status', 'approved')
                ->whereHas('deduction', function ($q) use ($branchId, $sectionId, $search) {
                    $q->whereIn('status', ['approved', 'applied']);
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

            $this->applyMonthFilter($installmentQuery, $month);

            foreach ($installmentQuery->get() as $inst) {
                $inst->update([
                    'status'     => 'applied',
                    'applied_at' => now(),
                ]);

                DeductionAudit::create([
                    'deduction_id' => $inst->deduction_id,
                    'user_id'      => Auth::id(),
                    'action'       => 'applied',
                    'description'  => "Installment period applied ({$inst->payroll_period}) via Payroll Due",
                ]);

                $appliedInstallments++;

                $parent = $inst->deduction;
                if ($parent) {
                    $stillOpen = $parent->installments()->where('status', '!=', 'applied')->exists();
                    if (! $stillOpen && $parent->status !== 'applied') {
                        $parent->update([
                            'status'       => 'applied',
                            'applied_date' => now(),
                        ]);
                    }
                }
            }

            $year = (int) $start->year;
            $monthNum = (int) $start->month;

            $oneTimers = Deduction::query()
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
                ->where(function ($q) use ($year, $monthNum) {
                    $q->where(function ($qq) use ($year, $monthNum) {
                        $qq->whereNotNull('effective_date')
                            ->whereYear('effective_date', $year)
                            ->whereMonth('effective_date', $monthNum);
                    })->orWhere(function ($qq) use ($year, $monthNum) {
                        $qq->whereNull('effective_date')
                            ->whereYear('deduction_date', $year)
                            ->whereMonth('deduction_date', $monthNum);
                    });
                })
                ->get();

            foreach ($oneTimers as $d) {
                $d->update([
                    'status'       => 'applied',
                    'applied_date' => now(),
                ]);

                DeductionAudit::create([
                    'deduction_id' => $d->id,
                    'user_id'      => Auth::id(),
                    'action'       => 'applied',
                    'description'  => 'One-time applied via Payroll Due (month batch)',
                ]);

                $appliedOneTime++;
            }
        });

        $total = $appliedInstallments + $appliedOneTime;

        if ($total === 0) {
            return back()->with(
                'warning',
                'No approved amounts due for this month to apply. Ensure the deduction is approved and each installment status is approved (re-approve if needed).'
            );
        }

        return redirect()
            ->route('reports.payroll-due', $request->only(['month', 'branch_id', 'section_id', 'search']))
            ->with(
                'success',
                "Applied {$appliedInstallments} installment(s) and {$appliedOneTime} one-time for {$start->format('F Y')} only. Other months unchanged."
            );
    }
}