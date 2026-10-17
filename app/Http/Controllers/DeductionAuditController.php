<?php

namespace App\Http\Controllers;

use App\Models\DeductionAudit;
use App\Models\Deduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class DeductionAuditController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-logs');

        $query = DeductionAudit::with(['deduction', 'user'])->latest();

        if ($request->filled('deduction_id')) {
            $query->where('deduction_id', $request->deduction_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59',
            ]);
        }

        $audits = $query->paginate(30)->withQueryString();

        return view('audits.index', compact('audits'));
    }

    public function show(Deduction $deduction)
    {
        Gate::authorize('view-logs');

        $audits = $deduction->audits()->with('user')->latest()->get();

        return view('audits.show', compact('deduction', 'audits'));
    }

    /**
     * Clear a single audit record. Supersuperadmin only.
     */
    public function destroy(DeductionAudit $audit)
    {
        Gate::authorize('clear-logs');

        Log::warning('Audit log entry cleared', [
            'user_id'  => Auth::id(),
            'audit_id' => $audit->id,
        ]);

        $audit->delete();

        return back()->with('success', 'Audit record cleared.');
    }

    /**
     * Clear all audit records. Supersuperadmin only.
     */
    public function clearAll()
    {
        Gate::authorize('clear-logs');

        $count = DeductionAudit::count();
        DeductionAudit::query()->delete();

        Log::warning('All audit logs cleared', [
            'user_id' => Auth::id(),
            'count'   => $count,
        ]);

        return redirect()
            ->route('audits.index')
            ->with('success', "{$count} audit record(s) cleared.");
    }
}