<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Branch;
use App\Models\Section;
use App\Models\Department;
use App\Models\JobTitle;
use App\Models\DeductionType;
use App\Models\DeductionInstallment;
use App\Models\DeductionAudit;
use App\Models\SoftDeleteLog;
use App\Http\Requests\StoreDeductionRequest;
use App\Http\Requests\UpdateDeductionRequest;
use App\Http\Requests\StoreBulkDeductionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DeductionController extends Controller
{
    /**
     * Normalize one installment row from form (payroll_month → label + dates).
     */
    protected function resolveInstallmentPeriod(array $item): array
    {
        $ym = $item['payroll_month'] ?? null;

        if ($ym) {
            $start = Carbon::createFromFormat('Y-m', $ym)->startOfMonth();
            $end   = (clone $start)->endOfMonth();

            return [
                'payroll_period' => $item['payroll_period'] ?? $start->format('F Y'),
                'period_start'   => $item['period_start'] ?? $start->toDateString(),
                'period_end'     => $item['period_end'] ?? $end->toDateString(),
            ];
        }

        return [
            'payroll_period' => $item['payroll_period'] ?? '',
            'period_start'   => $item['period_start'] ?? null,
            'period_end'     => $item['period_end'] ?? null,
        ];
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Deduction::class);

        $query = Deduction::with(['branch', 'section', 'department', 'jobTitle', 'deductionType', 'creator'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deduction_id', 'like', "%{$search}%")
                  ->orWhere('staff_name', 'like', "%{$search}%")
                  ->orWhere('approved_by', 'like', "%{$search}%")
                  ->orWhere('salary_number', 'like', "%{$search}%")
                  ->orWhere('group_id', 'like', "%{$search}%")
                  ->orWhere('amount', $search);
            });
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

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

        $deductions = $query->paginate(20)->withQueryString();

        return view('deductions.index', [
            'deductions'  => $deductions,
            'branches'    => Branch::where('is_active', true)->orderBy('name')->get(),
            'sections'    => Section::where('is_active', true)->orderBy('name')->get(),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Deduction::class);

        return view('deductions.create', [
            'branches'       => Branch::where('is_active', true)->orderBy('name')->get(),
            'sections'       => Section::where('is_active', true)->orderBy('name')->get(),
            'departments'    => Department::where('is_active', true)->orderBy('name')->get(),
            'jobTitles'      => JobTitle::where('is_active', true)->orderBy('name')->get(),
            'deductionTypes' => DeductionType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreDeductionRequest $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['status'] = 'pending';

            // Not mass-assigned on deduction
            unset($data['installments'], $data['supporting_document']);

            if ($request->hasFile('supporting_document')) {
                $data['supporting_document'] = $request->file('supporting_document')
                    ->store('deductions', 'public');
            }

            $deduction = Deduction::create($data);

            if ($request->category === 'installment' && $request->has('installments')) {
                foreach ($request->installments as $item) {
                    $period = $this->resolveInstallmentPeriod($item);

                    DeductionInstallment::create([
                        'deduction_id'   => $deduction->id,
                        'payroll_period' => $period['payroll_period'],
                        'period_start'   => $period['period_start'],
                        'period_end'     => $period['period_end'],
                        'amount'         => $item['amount'],
                        'is_edited'      => $item['is_edited'] ?? false,
                        'status'         => 'pending',
                    ]);
                }
            }

            DeductionAudit::create([
                'deduction_id' => $deduction->id,
                'user_id'      => Auth::id(),
                'action'       => 'created',
                'new_values'   => $deduction->toArray(),
                'description'  => 'Deduction created',
            ]);

            DB::commit();

            return redirect()
                ->route('deductions.show', $deduction)
                ->with('success', 'Deduction created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to create deduction: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function bulkCreate()
    {
        $this->authorize('create', Deduction::class);

        return view('deductions.bulk-create', [
            'branches'       => Branch::where('is_active', true)->orderBy('name')->get(),
            'sections'       => Section::where('is_active', true)->orderBy('name')->get(),
            'departments'    => Department::where('is_active', true)->orderBy('name')->get(),
            'jobTitles'      => JobTitle::where('is_active', true)->orderBy('name')->get(),
            'deductionTypes' => DeductionType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function bulkStore(StoreBulkDeductionRequest $request)
{
    $this->authorize('create', Deduction::class);

    DB::beginTransaction();

    try {
        $data = $request->validated();
        $groupId = 'GRP-' . strtoupper(Str::random(8));
        $isInstallment = $data['category'] === 'installment';
        $periods = $isInstallment ? ($data['installments'] ?? []) : [];
        $method = $data['allocation_method'] ?? 'equal';
        $totalAmount = (float) $data['total_amount'];
        $incidentMonths = (int) ($data['incident_months'] ?? 1);
        $staffList = $data['staff'];
        $nStaff = count($staffList);

        if ($isInstallment && count($periods) < 1) {
            return back()->withInput()->with('error', 'Add at least one installment period.');
        }

        // --- Allocate amounts server-side ---
        $allocated = 0;

        if ($method === 'months_worked') {
            $sumMonths = collect($staffList)->sum(fn ($s) => (int) ($s['months_worked'] ?? 0));
            if ($sumMonths < 1) {
                return back()->withInput()->with('error', 'Total months worked must be at least 1.');
            }
            foreach ($staffList as $i => &$s) {
                $m = (int) ($s['months_worked'] ?? 0);
                if ($i === $nStaff - 1) {
                    $s['amount'] = round($totalAmount - $allocated, 2);
                } else {
                    $share = floor(($totalAmount * $m / $sumMonths) * 100) / 100;
                    $s['amount'] = $share;
                    $allocated += $share;
                }
            }
            unset($s);
        } else {
            // Equal share
            $base = floor(($totalAmount / $nStaff) * 100) / 100;
            foreach ($staffList as $i => &$s) {
                if ($i === $nStaff - 1) {
                    $s['amount'] = round($totalAmount - $allocated, 2);
                } else {
                    $s['amount'] = $base;
                    $allocated += $base;
                }
            }
            unset($s);
        }

        $documentPath = null;
        if ($request->hasFile('supporting_document')) {
            $documentPath = $request->file('supporting_document')->store('deductions', 'public');
        }

        $created = 0;

        foreach ($staffList as $staff) {
            $remarks = $data['remarks'] ?? null;
            if ($method === 'months_worked') {
                $note = 'Allocation: months worked; Incident months: ' . $incidentMonths
                    . '; Months worked: ' . ($staff['months_worked'] ?? '-')
                    . '; Monthly: ' . number_format($totalAmount / max($incidentMonths, 1), 2);
                $remarks = trim(($remarks ? $remarks . ' | ' : '') . $note);
            } else {
                $note = 'Allocation: equal share among ' . $nStaff . ' staff';
                $remarks = trim(($remarks ? $remarks . ' | ' : '') . $note);
            }

            $deduction = Deduction::create([
                'group_id'            => $groupId,
                'branch_id'           => $data['branch_id'],
                'section_id'          => $data['section_id'],
                'department_id'       => $staff['department_id'] ?? null,
                'job_title_id'        => $staff['job_title_id'] ?? null,
                'deduction_type_id'   => $data['deduction_type_id'],
                'staff_name'          => $staff['staff_name'],
                'salary_number'       => $staff['salary_number'],
                'amount'              => $staff['amount'],
                'category'            => $data['category'],
                'deduction_date'      => $data['deduction_date'],
                'effective_date'      => $data['effective_date'],
                'reason'              => $data['reason'],
                'remarks'             => $remarks,
                'supporting_document' => $documentPath,
                'status'              => 'pending',
                'created_by'          => Auth::id(),
            ]);

            if ($isInstallment) {
                $staffAmount = (float) $staff['amount'];
                $n = count($periods);
                $base = floor(($staffAmount / $n) * 100) / 100;
                $periodAllocated = 0;

                foreach ($periods as $i => $period) {
                    $resolved = $this->resolveInstallmentPeriod($period);
                    $amt = ($i === $n - 1)
                        ? round($staffAmount - $periodAllocated, 2)
                        : $base;
                    $periodAllocated += $amt;

                    DeductionInstallment::create([
                        'deduction_id'   => $deduction->id,
                        'payroll_period' => $resolved['payroll_period'],
                        'period_start'   => $resolved['period_start'],
                        'period_end'     => $resolved['period_end'],
                        'amount'         => $amt,
                        'status'         => 'pending',
                    ]);
                }
            }

            DeductionAudit::create([
                'deduction_id' => $deduction->id,
                'user_id'      => Auth::id(),
                'action'       => 'created',
                'new_values'   => $deduction->toArray(),
                'description'  => 'Created as part of group ' . $groupId . ' (' . $method . ')',
            ]);

            $created++;
        }

        DB::commit();

        $label = $method === 'months_worked' ? 'by months worked' : 'equal share';

        return redirect()
            ->route('deductions.index', ['group_id' => $groupId])
            ->with('success', "{$created} deductions created under group {$groupId} ({$label}).");
    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->with('error', 'Failed to create bulk deductions: ' . $e->getMessage())
            ->withInput();
    }
}

    public function groupApprove(Request $request, string $groupId)
    {
        $request->validate([
            'approved_by' => ['required', 'string', 'max:255'],
        ]);

        $deductions = Deduction::where('group_id', $groupId)
            ->where('status', 'pending')
            ->get();

        if ($deductions->isEmpty()) {
            return back()->with('warning', 'No pending deductions found in this group.');
        }

        foreach ($deductions as $deduction) {
            $this->authorize('approve', $deduction);

            $deduction->update([
                'status'        => 'approved',
                'approved_by'   => $request->approved_by,
                'approval_date' => now(),
            ]);

            $deduction->installments()->update(['status' => 'approved']);

            DeductionAudit::create([
                'deduction_id' => $deduction->id,
                'user_id'      => Auth::id(),
                'action'       => 'approved',
                'description'  => 'Group approved by ' . $request->approved_by,
            ]);
        }

        return back()->with('success', $deductions->count() . ' deductions in group approved.');
    }

    public function groupReject(Request $request, string $groupId)
    {
        $deductions = Deduction::where('group_id', $groupId)
            ->where('status', 'pending')
            ->get();

        if ($deductions->isEmpty()) {
            return back()->with('warning', 'No pending deductions found in this group.');
        }

        foreach ($deductions as $deduction) {
            $this->authorize('approve', $deduction);

            $deduction->update(['status' => 'rejected']);

            DeductionAudit::create([
                'deduction_id' => $deduction->id,
                'user_id'      => Auth::id(),
                'action'       => 'rejected',
                'description'  => $request->reason ?? 'Group rejected',
            ]);
        }

        return back()->with('warning', $deductions->count() . ' deductions in group rejected.');
    }

    public function groupApply(string $groupId)
    {
        $deductions = Deduction::where('group_id', $groupId)
            ->where('status', 'approved')
            ->get();

        if ($deductions->isEmpty()) {
            return back()->with('warning', 'No approved deductions found in this group.');
        }

        foreach ($deductions as $deduction) {
            $this->authorize('apply', $deduction);

            $deduction->update([
                'status'       => 'applied',
                'applied_date' => now(),
            ]);

            $deduction->installments()->update([
                'status'     => 'applied',
                'applied_at' => now(),
            ]);

            DeductionAudit::create([
                'deduction_id' => $deduction->id,
                'user_id'      => Auth::id(),
                'action'       => 'applied',
                'description'  => 'Group applied to payroll',
            ]);
        }

        return back()->with('success', $deductions->count() . ' deductions in group applied to payroll.');
    }

    public function show(Deduction $deduction)
    {
        $this->authorize('view', $deduction);

        $deduction->load([
            'branch',
            'section',
            'department',
            'jobTitle',
            'deductionType',
            'creator',
            'installments',
            'audits.user',
        ]);

        return view('deductions.show', compact('deduction'));
    }

    public function edit(Deduction $deduction)
    {
        $this->authorize('update', $deduction);

        return view('deductions.edit', [
            'deduction'      => $deduction->load('installments'),
            'branches'       => Branch::where('is_active', true)->orderBy('name')->get(),
            'sections'       => Section::where('is_active', true)->orderBy('name')->get(),
            'departments'    => Department::where('is_active', true)->orderBy('name')->get(),
            'jobTitles'      => JobTitle::where('is_active', true)->orderBy('name')->get(),
            'deductionTypes' => DeductionType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateDeductionRequest $request, Deduction $deduction)
    {
        $this->authorize('update', $deduction);

        DB::beginTransaction();

        try {
            $oldValues = $deduction->toArray();
            $data = $request->validated();
            unset($data['installments'], $data['supporting_document']);

            if ($request->hasFile('supporting_document')) {
                if ($deduction->supporting_document) {
                    Storage::disk('public')->delete($deduction->supporting_document);
                }
                $data['supporting_document'] = $request->file('supporting_document')
                    ->store('deductions', 'public');
            }

            $deduction->update($data);

            if ($request->category === 'installment' && $request->has('installments')) {
                $deduction->installments()->delete();

                $instStatus = in_array($deduction->status, ['approved', 'applied'], true)
                    ? 'approved'
                    : 'pending';

                foreach ($request->installments as $item) {
                    $period = $this->resolveInstallmentPeriod($item);

                    DeductionInstallment::create([
                        'deduction_id'   => $deduction->id,
                        'payroll_period' => $period['payroll_period'],
                        'period_start'   => $period['period_start'],
                        'period_end'     => $period['period_end'],
                        'amount'         => $item['amount'],
                        'is_edited'      => $item['is_edited'] ?? false,
                        'status'         => $instStatus,
                    ]);
                }
            } else {
                $deduction->installments()->delete();
            }

            DeductionAudit::create([
                'deduction_id' => $deduction->id,
                'user_id'      => Auth::id(),
                'action'       => 'updated',
                'old_values'   => $oldValues,
                'new_values'   => $deduction->fresh()->toArray(),
                'description'  => 'Deduction updated',
            ]);

            DB::commit();

            return redirect()
                ->route('deductions.show', $deduction)
                ->with('success', 'Deduction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to update deduction: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Deduction $deduction)
    {
        $this->authorize('delete', $deduction);

        SoftDeleteLog::create([
            'model_type' => Deduction::class,
            'model_id'   => $deduction->id,
            'data'       => $deduction->load('installments')->toArray(),
            'deleted_by' => Auth::id(),
            'reason'     => request('reason'),
        ]);

        $deduction->delete();

        return redirect()
            ->route('deductions.index')
            ->with('success', 'Deduction soft-deleted and logged.');
    }

    public function approve(Request $request, Deduction $deduction)
    {
        $this->authorize('approve', $deduction);

        $request->validate([
            'approved_by' => ['required', 'string', 'max:255'],
        ]);

        $deduction->update([
            'status'        => 'approved',
            'approved_by'   => $request->approved_by,
            'approval_date' => now(),
        ]);

        $deduction->installments()->update(['status' => 'approved']);

        DeductionAudit::create([
            'deduction_id' => $deduction->id,
            'user_id'      => Auth::id(),
            'action'       => 'approved',
            'description'  => 'Approved by ' . $request->approved_by,
        ]);

        return back()->with('success', 'Deduction approved successfully.');
    }

    public function reject(Request $request, Deduction $deduction)
    {
        $this->authorize('approve', $deduction);

        $request->validate([
            'reason' => ['nullable', 'string'],
        ]);

        $deduction->update(['status' => 'rejected']);

        DeductionAudit::create([
            'deduction_id' => $deduction->id,
            'user_id'      => Auth::id(),
            'action'       => 'rejected',
            'description'  => $request->reason ?? 'Deduction rejected',
        ]);

        return back()->with('warning', 'Deduction rejected.');
    }

    public function apply(Deduction $deduction)
    {
        $this->authorize('apply', $deduction);

        $deduction->update([
            'status'       => 'applied',
            'applied_date' => now(),
        ]);

        $deduction->installments()->update([
            'status'     => 'applied',
            'applied_at' => now(),
        ]);

        DeductionAudit::create([
            'deduction_id' => $deduction->id,
            'user_id'      => Auth::id(),
            'action'       => 'applied',
            'description'  => 'Applied to payroll',
        ]);

        return back()->with('success', 'Deduction applied to payroll successfully.');
    }

    public function cancel(Deduction $deduction)
    {
        $this->authorize('update', $deduction);

        $deduction->update(['status' => 'cancelled']);

        DeductionAudit::create([
            'deduction_id' => $deduction->id,
            'user_id'      => Auth::id(),
            'action'       => 'cancelled',
            'description'  => 'Deduction cancelled',
        ]);

        return back()->with('warning', 'Deduction cancelled.');
    }

    public function document(Deduction $deduction)
    {
        $this->authorize('view', $deduction);

        abort_unless(
            $deduction->supporting_document
                && Storage::disk('public')->exists($deduction->supporting_document),
            404
        );

        return Storage::disk('public')->response($deduction->supporting_document);
    }
}