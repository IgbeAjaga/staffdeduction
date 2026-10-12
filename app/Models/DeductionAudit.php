<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeductionAudit extends Model
{
    protected $fillable = [
        'deduction_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function deduction(): BelongsTo
    {
        return $this->belongsTo(Deduction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}