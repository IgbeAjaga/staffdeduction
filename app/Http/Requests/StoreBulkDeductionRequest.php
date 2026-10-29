<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBulkDeductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id'           => ['required', 'exists:branches,id'],
            'section_id'          => ['required', 'exists:sections,id'],
            'deduction_type_id'   => ['required', 'exists:deduction_types,id'],
            'category'            => ['required', 'in:one_time,installment'],
            'deduction_date'      => ['required', 'date'],
            'effective_date'      => ['required', 'date'],
            'reason'              => ['required', 'string', 'max:1000'],
            'remarks'             => ['nullable', 'string', 'max:1000'],
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],

            'total_amount'        => ['required', 'numeric', 'min:0.01'],
            'allocation_method'   => ['required', 'in:equal,months_worked'],
            'incident_months'     => ['required_if:allocation_method,months_worked', 'nullable', 'integer', 'min:1', 'max:120'],

            'staff'                      => ['required', 'array', 'min:1'],
            'staff.*.staff_name'         => ['required', 'string', 'max:255'],
            'staff.*.salary_number'      => ['required', 'string', 'max:100'],
            'staff.*.department_id'      => ['nullable', 'exists:departments,id'],
            'staff.*.job_title_id'       => ['nullable', 'exists:job_titles,id'],
            'staff.*.amount'             => ['required', 'numeric', 'min:0.01'],
            'staff.*.months_worked'      => ['required_if:allocation_method,months_worked', 'nullable', 'integer', 'min:1'],

            'installments'                    => ['required_if:category,installment', 'array', 'min:1'],
            'installments.*.payroll_month'    => ['required_with:installments', 'date_format:Y-m'],
            'installments.*.payroll_period'   => ['nullable', 'string', 'max:50'],
            'installments.*.period_start'     => ['nullable', 'date'],
            'installments.*.period_end'       => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'total_amount.required'                       => 'Total amount is required.',
            'total_amount.numeric'                        => 'Total amount must be a valid number.',
            'staff.required'                              => 'Add at least one staff member.',
            'staff.*.staff_name.required'                 => 'Staff name is required for every row.',
            'staff.*.amount.required'                     => 'Amount is required for every staff member.',
            'staff.*.amount.numeric'                      => 'Each staff amount must be a valid number.',
            'installments.required_if'                    => 'Add at least one installment period for installment deductions.',
            'installments.*.payroll_month.required_with'  => 'Select a payroll month for each period.',
            'incident_months.required_if'                 => 'Incident months is required when allocating by months worked.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Strip thousand separators: 1,250,000.50 → 1250000.50
        if ($this->has('total_amount')) {
            $this->merge([
                'total_amount' => $this->cleanMoney($this->total_amount),
            ]);
        }

        if ($this->has('staff') && is_array($this->staff)) {
            $staff = [];
            foreach ($this->staff as $i => $row) {
                if (isset($row['amount'])) {
                    $row['amount'] = $this->cleanMoney($row['amount']);
                }
                $staff[$i] = $row;
            }
            $this->merge(['staff' => $staff]);
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
                        // leave as-is; validation will catch bad format
                    }
                }
                $normalized[$i] = $row;
            }
            $this->merge(['installments' => $normalized]);
        }
    }

    private function cleanMoney($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Keep digits and decimal point only
        $cleaned = preg_replace('/[^\d.]/', '', (string) $value);

        return $cleaned ?? '';
    }
}