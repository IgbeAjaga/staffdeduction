<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UpdateDeductionRequest extends FormRequest
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
            'status'            => ['sometimes', Rule::in(['pending', 'approved', 'rejected', 'applied', 'cancelled'])],

            'installments'                      => ['required_if:category,installment', 'array', 'min:1'],
            'installments.*.id'                 => ['nullable', 'exists:deduction_installments,id'],
            'installments.*.payroll_month'      => ['required_with:installments', 'date_format:Y-m'],
            'installments.*.payroll_period'     => ['nullable', 'string', 'max:50'],
            'installments.*.period_start'       => ['nullable', 'date'],
            'installments.*.period_end'         => ['nullable', 'date'],
            'installments.*.amount'             => ['required_with:installments', 'numeric', 'min:0'],
            'installments.*.is_edited'          => ['sometimes', 'boolean'],
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
            'amount.min'                      => 'Amount must be greater than zero.',
            'category.required'               => 'Please choose One-time or Installment.',
            'effective_date.after_or_equal'   => 'Effective date cannot be before deduction date.',
            'installments.required_if'        => 'Please add at least one installment period.',
            'installments.*.payroll_month.required_with' => 'Select a payroll month for each installment.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('category')) {
            $this->merge(['category' => strtolower($this->category)]);
        }

        if ($this->has('installments') && is_array($this->installments)) {
            $normalized = [];
            foreach ($this->installments as $i => $row) {
                $ym = $row['payroll_month'] ?? null;
                if ($ym) {
                    try {
                        $start = Carbon::createFromFormat('Y-m', $ym)->startOfMonth();
                        $row['payroll_period'] = $row['payroll_period'] ?? $start->format('F Y');
                        $row['period_start']   = $row['period_start'] ?? $start->toDateString();
                        $row['period_end']     = $row['period_end'] ?? $start->copy()->endOfMonth()->toDateString();
                    } catch (\Throwable $e) {
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
                $totalInstallments = collect($this->installments)->sum('amount');
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
}