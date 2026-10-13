<?php

namespace App\Http\Controllers;

use App\Models\DeductionAudit;
use App\Models\Deduction;
use Illuminate\Http\Request;

class DeductionAuditController extends Controller
{
    /**
     * Display a listing of audit trails.
     * Only superadmin should access this in production.
     */
    public function index(Request $request)
    {
        $query = DeductionAudit::with(['deduction', 'user'])
            ->latest();

        // Filter by deduction
        if ($request->filled('deduction_id')) {
            $query->where('deduction_id', $request->deduction_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        $audits = $query->paginate(30)->withQueryString();

        return view('audits.index', compact('audits'));
    }

    /**
     * Show audit trail for a specific deduction.
     */
    public function show(Deduction $deduction)
    {
        $audits = $deduction->audits()
            ->with('user')
            ->latest()
            ->get();

        return view('audits.show', compact('deduction', 'audits'));
    }

    /**
     * Soft-delete is not allowed for audits (they are permanent records).
     * We only allow viewing.
     */
}