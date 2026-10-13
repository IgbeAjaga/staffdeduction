<?php

namespace App\Http\Controllers;

use App\Models\SoftDeleteLog;
use Illuminate\Http\Request;

class SoftDeleteLogController extends Controller
{
    /**
     * Display all soft-deleted records log.
     * Restricted to superadmin only.
     */
    public function index(Request $request)
    {
        // Optional: Gate / Policy check
        // $this->authorize('viewAny', SoftDeleteLog::class);

        $query = SoftDeleteLog::with('deleter')
            ->latest();

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by who deleted
        if ($request->filled('deleted_by')) {
            $query->where('deleted_by', $request->deleted_by);
        }

        // Date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('soft-delete-logs.index', compact('logs'));
    }

    /**
     * Show a single soft-delete log entry with full data.
     */
    public function show(SoftDeleteLog $softDeleteLog)
    {
        $softDeleteLog->load('deleter');

        return view('soft-delete-logs.show', compact('softDeleteLog'));
    }

    /**
     * Optional: Force delete a log entry (rarely needed).
     */
    public function destroy(SoftDeleteLog $softDeleteLog)
    {
        // Only superadmin
        $softDeleteLog->delete();

        return redirect()
            ->route('soft-delete-logs.index')
            ->with('success', 'Soft delete log entry removed.');
    }
}