<?php

namespace App\Http\Controllers;

use App\Models\SoftDeleteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SoftDeleteLogController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-logs');

        $query = SoftDeleteLog::with('deleter')->latest();

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('deleted_by')) {
            $query->where('deleted_by', $request->deleted_by);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59',
            ]);
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('soft-delete-logs.index', compact('logs'));
    }

    public function show(SoftDeleteLog $softDeleteLog)
    {
        Gate::authorize('view-logs');

        $softDeleteLog->load('deleter');

        return view('soft-delete-logs.show', compact('softDeleteLog'));
    }

    /**
     * Clear a single log entry. Supersuperadmin only.
     */
    public function destroy(SoftDeleteLog $softDeleteLog)
    {
        Gate::authorize('clear-logs');

        Log::warning('Soft delete log cleared', [
            'user_id' => Auth::id(),
            'log_id'  => $softDeleteLog->id,
        ]);

        $softDeleteLog->delete();

        return redirect()
            ->route('soft-delete-logs.index')
            ->with('success', 'Soft delete log entry cleared.');
    }

    /**
     * Clear all log entries. Supersuperadmin only.
     */
    public function clearAll()
    {
        Gate::authorize('clear-logs');

        $count = SoftDeleteLog::count();
        SoftDeleteLog::query()->delete();

        Log::warning('All soft delete logs cleared', [
            'user_id' => Auth::id(),
            'count'   => $count,
        ]);

        return redirect()
            ->route('soft-delete-logs.index')
            ->with('success', "{$count} log(s) cleared.");
    }
}