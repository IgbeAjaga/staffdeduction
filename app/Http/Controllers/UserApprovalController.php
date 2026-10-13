<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $users = User::whereNotIn('role', ['superadmin', 'supersuperadmin'])
            ->latest()
            ->paginate(20);

        return view('users.approvals.index', compact('users'));
    }

    public function approve(User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'User approved successfully.');
    }

    public function disapprove(User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $user->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', 'User disapproved successfully.');
    }
}