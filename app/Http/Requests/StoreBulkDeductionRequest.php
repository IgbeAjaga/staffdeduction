<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

        'staff'                      => ['required', 'array', 'min:1'],
        'staff.*.staff_name'         => ['required', 'string', 'max:255'],
        'staff.*.salary_number'      => ['required', 'string', 'max:100'],
        'staff.*.department_id' => ['nullable', 'exists:departments,id'],
        'staff.*.job_title_id'  => ['nullable', 'exists:job_titles,id'],
        'staff.*.amount'             => ['required', 'numeric', 'min:0.01'],
    ];
}

    public function messages(): array
    {
        return [
            'staff.required'              => 'Add at least one staff member.',
            'staff.*.staff_name.required' => 'Staff name is required for every row.',
            'staff.*.amount.required'     => 'Amount is required for every staff member.',
        ];
    }
}