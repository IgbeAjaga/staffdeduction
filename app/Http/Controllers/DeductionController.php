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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeductionController extends Controller
{
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
                  ->orWhere('amount', $search);
            });
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
            $data['status']     = 'pending';

            if ($request->hasFile('supporting_document')) {
                $data['supporting_document'] = $request->file('supporting_document')
                    ->store('deductions', 'public');
            }

            $deduction = Deduction::create($data);

            // Handle installments
            if ($request->category === 'installment' && $request->has('installments')) {
                foreach ($request->installments as $item) {
                    DeductionInstallment::create([
                        'deduction_id'   => $deduction->id,
                        'payroll_period' => $item['payroll_period'],
                        'period_start'   => $item['period_start'] ?? null,
                        'period_end'     => $item['period_end'] ?? null,
                        'amount'         => $item['amount'],
                        'is_edited'      => $item['is_edited'] ?? false,
                    ]);
                }
            }

            // Audit Trail
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
            'audits.user'
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

            if ($request->hasFile('supporting_document')) {
                // Delete old file if exists
                if ($deduction->supporting_document) {
                    Storage::disk('public')->delete($deduction->supporting_document);
                }
                $data['supporting_document'] = $request->file('supporting_document')
                    ->store('deductions', 'public');
            }

            $deduction->update($data);

            // Sync installments
            if ($request->category === 'installment' && $request->has('installments')) {
                $deduction->installments()->delete(); // simple approach

                foreach ($request->installments as $item) {
                    DeductionInstallment::create([
                        'deduction_id'   => $deduction->id,
                        'payroll_period' => $item['payroll_period'],
                        'period_start'   => $item['period_start'] ?? null,
                        'period_end'     => $item['period_end'] ?? null,
                        'amount'         => $item['amount'],
                        'is_edited'      => $item['is_edited'] ?? false,
                    ]);
                }
            } else {
                $deduction->installments()->delete();
            }

            // Audit
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

    // ==================== Workflow Actions ====================

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