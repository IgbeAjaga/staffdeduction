<?php

namespace App\Http\Controllers;

use App\Models\Deduction;
use App\Models\Branch;
use App\Models\Department;
use App\Models\JobTitle;
use App\Models\DeductionType;
use App\Models\SoftDeleteLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Main dashboard – redirects based on user role
     */
    public function index()
    {
        $user = Auth::user();

        // Super Super Admin
        if (method_exists($user, 'isSuperSuperAdmin') && $user->isSuperSuperAdmin()) {
            return $this->superSuperadmin();
        }

        // Super Admin
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $this->superadmin();
        }

        // Admin
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $this->admin();
        }

        // Default (normal user / staff)
        return $this->admin(); // or create a simple user dashboard
    }

    /**
     * Admin Dashboard
     */
    public function admin()
    {
        $stats = [
            'total'        => Deduction::count(),
            'pending'      => Deduction::where('status', 'pending')->count(),
            'approved'     => Deduction::where('status', 'approved')->count(),
            'applied'      => Deduction::where('status', 'applied')->count(),
            'rejected'     => Deduction::where('status', 'rejected')->count(),
            'total_amount' => Deduction::sum('amount'),
            'branches'     => Branch::count(),
            'departments'  => Department::count(),
        ];

        $recentDeductions = Deduction::with('branch')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentDeductions'));
    }

    /**
     * Superadmin Dashboard
     */
    public function superadmin()
    {
        $stats = [
            'total'         => Deduction::count(),
            'pending'       => Deduction::where('status', 'pending')->count(),
            'approved'      => Deduction::where('status', 'approved')->count(),
            'applied'       => Deduction::where('status', 'applied')->count(),
            'total_amount'  => Deduction::sum('amount'),
            'soft_deleted'  => SoftDeleteLog::count(),
            'users'         => User::count(),
            'branches'      => Branch::count(),
            'departments'   => Department::count(),
            'job_titles'    => JobTitle::count(),
            'deduction_types' => DeductionType::count(),
        ];

        $recentDeductions = Deduction::with('branch')
            ->latest()
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentDeductions'));
    }

    /**
     * Super Super Admin Dashboard
     */
    public function superSuperadmin()
    {
        $stats = [
            'total'           => Deduction::count(),
            'pending'         => Deduction::where('status', 'pending')->count(),
            'approved'        => Deduction::where('status', 'approved')->count(),
            'applied'         => Deduction::where('status', 'applied')->count(),
            'total_amount'    => Deduction::sum('amount'),
            'soft_deleted'    => SoftDeleteLog::count(),
            'users'           => User::count(),
            'pending_users'   => User::where('is_approved', false)->count(),
            'branches'        => Branch::count(),
            'departments'     => Department::count(),
            'job_titles'      => JobTitle::count(),
            'deduction_types' => DeductionType::count(),
        ];

        $recentDeductions = Deduction::with('branch')
            ->latest()
            ->take(10)
            ->get();

        return view('supersuperadmin.dashboard', compact('stats', 'recentDeductions'));
    }
}