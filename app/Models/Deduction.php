<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class Deduction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'deduction_id', 'branch_id', 'staff_name', 'salary_number',
        'department_id', 'job_title_id', 'deduction_type_id', 'reason',
        'amount', 'category', 'deduction_date', 'effective_date', 'status',
        'supporting_document', 'employee_notified', 'employee_acknowledged',
        'created_by', 'approved_by', 'approval_date', 'applied_date', 'remarks'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deduction_date' => 'date',
        'effective_date' => 'date',
        'approval_date' => 'date',
        'applied_date' => 'date',
        'employee_notified' => 'boolean',
        'employee_acknowledged' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($deduction) {
            $deduction->deduction_id = self::generateDeductionId();
        });

        static::updated(function ($deduction) {
            // Audit trail is handled in controller / observer
        });
    }

    public static function generateDeductionId(): string
    {
        $date = now()->format('Ymd');
        $last = self::withTrashed()->whereDate('created_at', today())->count() + 1;
        return 'DED-' . $date . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function jobTitle(): BelongsTo { return $this->belongsTo(JobTitle::class); }
    public function deductionType(): BelongsTo { return $this->belongsTo(DeductionType::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function installments(): HasMany { return $this->hasMany(DeductionInstallment::class); }
    public function audits(): HasMany { return $this->hasMany(DeductionAudit::class); }
}