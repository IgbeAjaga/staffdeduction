<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deduction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'deduction_id',
        'branch_id',
        'section_id',
        'staff_name',
        'salary_number',
        'department_id',
        'job_title_id',
        'deduction_type_id',
        'reason',
        'amount',
        'category',
        'deduction_date',
        'effective_date',
        'status',
        'supporting_document',
        'employee_notified',
        'employee_acknowledged',
        'created_by',
        'approved_by',
        'approval_date',
        'applied_date',
        'remarks',
    ];

    protected $casts = [
        'amount'                 => 'decimal:2',
        'deduction_date'         => 'date',
        'effective_date'         => 'date',
        'approval_date'          => 'date',
        'applied_date'           => 'date',
        'employee_notified'      => 'boolean',
        'employee_acknowledged'  => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Deduction $deduction) {
            if (empty($deduction->deduction_id)) {
                $deduction->deduction_id = self::generateDeductionId();
            }
        });
    }

    public static function generateDeductionId(): string
    {
        $date = now()->format('Ymd');
        $count = self::withTrashed()
            ->whereDate('created_at', today())
            ->count() + 1;

        return 'DED-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function deductionType(): BelongsTo
    {
        return $this->belongsTo(DeductionType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(DeductionInstallment::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(DeductionAudit::class);
    }
}