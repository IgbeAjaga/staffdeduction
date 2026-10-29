<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreDeductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id'         => ['required', 'exists:branches,id'],
            'section_id'        => ['required', 'exists:sections,id'],
            'staff_name'        => ['required', 'string', 'max:255'],
            'salary_number'     => ['required', 'string', 'max:100'],
            'department_id'     => ['nullable', 'exists:departments,id'],
            'job_title_id'      => ['nullable', 'exists:job_titles,id'],
            'deduction_type_id' => ['required', 'exists:deduction_types,id'],

            'reason'            => ['required', 'string'],
            'amount'            => ['required', 'numeric', 'min:0.01'],
            'category'          => ['required', Rule::in(['one_time', 'installment'])],
            'deduction_date'    => ['required', 'date'],
            'effective_date'    => ['required', 'date', 'after_or_equal:deduction_date'],
            'remarks'           => ['nullable', 'string'],

            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

            'installments'                    => ['required_if:category,installment', 'array', 'min:1'],
            'installments.*.payroll_month'    => ['required_with:installments', 'date_format:Y-m'],
            'installments.*.payroll_period'   => ['nullable', 'string', 'max:50'],
            'installments.*.period_start'     => ['nullable', 'date'],
            'installments.*.period_end'       => ['nullable', 'date', 'after_or_equal:installments.*.period_start'],
            'installments.*.amount'           => ['required_with:installments', 'numeric', 'min:0'],
            'installments.*.is_edited'        => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.required'              => 'Please select a branch.',
            'section_id.required'             => 'Please select a section.',
            'staff_name.required'             => 'Staff name is required.',
            'salary_number.required'          => 'Salary number is required.',
            'deduction_type_id.required'      => 'Please select a deduction type.',
            'reason.required'                 => 'Reason for deduction is required.',
            'amount.required'                 => 'Amount is required.',
            'amount.numeric'                  => 'Amount must be a valid number.',
            'amount.min'                      => 'Amount must be greater than zero.',
            'category.required'               => 'Please choose One-time or Installment.',
            'category.in'                     => 'Invalid deduction category.',
            'deduction_date.required'         => 'Deduction date is required.',
            'effective_date.required'         => 'Effective date is required.',
            'effective_date.after_or_equal'   => 'Effective date cannot be before deduction date.',
            'supporting_document.mimes'       => 'Supporting document must be PDF, JPG or PNG.',
            'supporting_document.max'         => 'Supporting document must not exceed 5MB.',
            'installments.required_if'        => 'Please add at least one installment period.',
            'installments.*.payroll_month.required_with' => 'Select a payroll month for each installment.',
            'installments.*.amount.required_with'        => 'Amount is required for each installment.',
            'installments.*.amount.min'                  => 'Installment amount cannot be negative.',
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id'         => 'branch',
            'section_id'        => 'section',
            'department_id'     => 'department',
            'job_title_id'      => 'job title',
            'deduction_type_id' => 'deduction type',
            'staff_name'        => 'staff name',
            'salary_number'     => 'salary number',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('category')) {
            $this->merge([
                'category' => strtolower($this->category),
            ]);
        }

        // Strip thousand separators: 1,250,000.50 → 1250000.50
        if ($this->has('amount')) {
            $this->merge([
                'amount' => $this->cleanMoney($this->amount),
            ]);
        }

        if ($this->has('installments') && is_array($this->installments)) {
            $normalized = [];
            foreach ($this->installments as $i => $row) {
                if (isset($row['amount'])) {
                    $row['amount'] = $this->cleanMoney($row['amount']);
                }

                $ym = $row['payroll_month'] ?? null;
                if ($ym) {
                    try {
                        $start = Carbon::createFromFormat('Y-m', $ym)->startOfMonth();
                        $row['payroll_period'] = $row['payroll_period'] ?? $start->format('F Y');
                        $row['period_start']   = $row['period_start'] ?? $start->toDateString();
                        $row['period_end']     = $row['period_end'] ?? $start->copy()->endOfMonth()->toDateString();
                    } catch (\Throwable $e) {
                        // leave as-is; validation will catch bad format
                    }
                }

                $normalized[$i] = $row;
            }
            $this->merge(['installments' => $normalized]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->category === 'installment' && $this->has('installments')) {
                $totalInstallments = collect($this->installments)->sum(function ($item) {
                    return (float) ($item['amount'] ?? 0);
                });
                $mainAmount = (float) $this->amount;

                if (abs($totalInstallments - $mainAmount) > 1) {
                    $validator->errors()->add(
                        'amount',
                        'The total of installment amounts ('.number_format($totalInstallments, 2).') must match the main amount ('.number_format($mainAmount, 2).').'
                    );
                }
            }
        });
    }

    private function cleanMoney($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Remove commas and spaces; keep digits and one decimal point
        $cleaned = preg_replace('/[^\d.]/', '', (string) $value);

        return $cleaned ?? '';
    }
}