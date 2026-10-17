<?php

namespace App\Policies;

use App\Models\Deduction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeductionPolicy
{
    /**
     * Roles allowed to manage deductions.
     */
    protected array $allowedRoles = ['admin', 'superadmin', 'supersuperadmin'];

    protected function isAllowed(User $user): bool
    {
        return in_array($user->role, $this->allowedRoles, true);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAllowed($user);
    }

    public function view(User $user, Deduction $deduction): bool
    {
        return $this->isAllowed($user);
    }

    public function create(User $user): bool
    {
        return $this->isAllowed($user);
    }

    public function update(User $user, Deduction $deduction): bool
    {
        return $this->isAllowed($user);
    }

    public function delete(User $user, Deduction $deduction): bool
    {
        return $this->isAllowed($user);
    }

    public function restore(User $user, Deduction $deduction): bool
    {
        return false;
    }

    public function forceDelete(User $user, Deduction $deduction): bool
    {
        return false;
    }

    public function approve(User $user, Deduction $deduction): bool
{
    return $this->isAllowed($user) && $deduction->status === 'pending';
}

public function apply(User $user, Deduction $deduction): bool
{
    return $this->isAllowed($user) && $deduction->status === 'approved';
}
}