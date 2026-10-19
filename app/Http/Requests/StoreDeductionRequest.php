<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Later: return $this->user()->can('create', Deduction::class);
    }

    public function rules(): array
    {
        return [
            // Staff Information
            'branch_id'         => ['required', 'exists:branches,id'],
            'section_id'         => ['required', 'exists:sections,id'],
            'staff_name'        => ['required', 'string', 'max:255'],
            'salary_number'     => ['required', 'string', 'max:100'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'job_title_id'  => ['nullable', 'exists:job_titles,id'],
            'deduction_type_id' => ['required', 'exists:deduction_types,id'],

            // Deduction Details
            'reason'            => ['required', 'string'],
            'amount'            => ['required', 'numeric', 'min:0.01'],
            'category'          => ['required', Rule::in(['one_time', 'installment'])],
            'deduction_date'    => ['required', 'date'],
            'effective_date'    => ['required', 'date', 'after_or_equal:deduction_date'],
            'remarks'           => ['nullable', 'string'],

            // Supporting Document
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB

            // Installment specific (only required when category = installment)
            'installments'                      => ['required_if:category,installment', 'array', 'min:1'],
            'installments.*.payroll_period'     => ['required_with:installments', 'string', 'max:50'],
            'installments.*.period_start'       => ['nullable', 'date'],
            'installments.*.period_end'         => ['nullable', 'date', 'after_or_equal:installments.*.period_start'],
            'installments.*.amount'             => ['required_with:installments', 'numeric', 'min:0'],
            'installments.*.is_edited'          => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.required'              => 'Please select a branch.',
            'section_id.required'              => 'Please select a section.',
            'staff_name.required'             => 'Staff name is required.',
            'salary_number.required'          => 'Salary number is required.',
            'department_id.required'          => 'Please select a department.',
            'job_title_id.required'           => 'Please select a job title.',
            'deduction_type_id.required'      => 'Please select a deduction type.',
            'reason.required'                 => 'Reason for deduction is required.',
            'amount.required'                 => 'Amount is required.',
            'amount.min'                      => 'Amount must be greater than zero.',
            'category.required'               => 'Please choose One-time or Installment.',
            'category.in'                     => 'Invalid deduction category.',
            'deduction_date.required'         => 'Deduction date is required.',
            'effective_date.required'         => 'Effective date is required.',
            'effective_date.after_or_equal'   => 'Effective date cannot be before deduction date.',
            'supporting_document.mimes'       => 'Supporting document must be PDF, JPG or PNG.',
            'supporting_document.max'         => 'Supporting document must not exceed 5MB.',

            // Installment messages
            'installments.required_if'                => 'Please add at least one installment period.',
            'installments.*.payroll_period.required_with' => 'Payroll period is required for each installment.',
            'installments.*.amount.required_with'     => 'Amount is required for each installment.',
            'installments.*.amount.min'               => 'Installment amount cannot be negative.',
        ];
    }

    public function attributes(): array
    {
        return [
            'branch_id'         => 'branch',
            'section_id'         => 'section',
            'department_id'     => 'department',
            'job_title_id'      => 'job title',
            'deduction_type_id' => 'deduction type',
            'staff_name'        => 'staff name',
            'salary_number'     => 'salary number',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure category is clean
        if ($this->has('category')) {
            $this->merge([
                'category' => strtolower($this->category),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Extra validation: When installment, the sum of installments should roughly match the total amount
            if ($this->category === 'installment' && $this->has('installments')) {
                $totalInstallments = collect($this->installments)->sum('amount');
                $mainAmount = (float) $this->amount;

                // Allow a small difference of 1 naira (for rounding)
                if (abs($totalInstallments - $mainAmount) > 1) {
                    $validator->errors()->add(
                        'amount',
                        'The total of installment amounts ('.number_format($totalInstallments, 2).') must match the main amount ('.number_format($mainAmount, 2).').'
                    );
                }
            }
        });
    }
}