<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at'       => 'datetime',
        'is_approved'       => 'boolean',
        'password'          => 'hashed',
    ];

    /* =========================
     |  ROLE HELPERS
     |========================= */

    public function isSuperSuperAdmin(): bool
    {
        return $this->role === 'supersuperadmin';
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'supersuperadmin']);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /* =========================
     |  APPROVAL RELATIONSHIPS
     |========================= */

    // Superadmin/Admin who approved this user
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Users approved by this superadmin/admin
    public function approvedUsers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    /* =========================
     |  DEDUCTION RELATIONSHIPS
     |========================= */

    // Deductions created by this user
    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'created_by');
    }

    // Deduction audit logs performed by this user
    public function deductionAudits()
    {
        return $this->hasMany(DeductionAudit::class, 'user_id');
    }

    // Soft delete logs performed by this user
    public function softDeleteLogs()
    {
        return $this->hasMany(SoftDeleteLog::class, 'deleted_by');
    }
}