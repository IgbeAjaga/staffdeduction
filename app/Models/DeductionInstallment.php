<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeductionInstallment extends Model
{
    protected $fillable = [
        'deduction_id',
        'payroll_period',
        'period_start',
        'period_end',
        'amount',
        'is_edited',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'period_start' => 'date',
        'period_end'   => 'date',
        'is_edited'    => 'boolean',
    ];

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(Deduction::class);
    }
}