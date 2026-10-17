<div class="row g-3">

    {{-- Branch --}}
    <div class="col-md-6">
        <label class="form-label">Branch <span class="text-danger">*</span></label>
        <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
            <option value="">Select Branch</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}"
                    @selected(old('branch_id', $deduction->branch_id ?? '') == $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Section --}}
    <div class="col-md-6">
        <label class="form-label">Section <span class="text-danger">*</span></label>
        <select name="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
            <option value="">Select Section</option>
            @foreach($sections as $section)
                <option value="{{ $section->id }}"
                    @selected(old('section_id', $deduction->section_id ?? '') == $section->id)>
                    {{ $section->name }}
                </option>
            @endforeach
        </select>
        @error('section_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Staff Name --}}
    <div class="col-md-6">
        <label class="form-label">Staff Name <span class="text-danger">*</span></label>
        <input type="text" name="staff_name"
               value="{{ old('staff_name', $deduction->staff_name ?? '') }}"
               class="form-control @error('staff_name') is-invalid @enderror" required>
        @error('staff_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Salary Number --}}
    <div class="col-md-6">
        <label class="form-label">Salary Number <span class="text-danger">*</span></label>
        <input type="text" name="salary_number"
               value="{{ old('salary_number', $deduction->salary_number ?? '') }}"
               class="form-control @error('salary_number') is-invalid @enderror" required>
        @error('salary_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Department --}}
    <div class="col-md-6">
        <label class="form-label">Department <span class="text-danger">*</span></label>
        <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
            <option value="">Select Department</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $deduction->department_id ?? '') == $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Job Title --}}
    <div class="col-md-6">
        <label class="form-label">Job Title <span class="text-danger">*</span></label>
        <select name="job_title_id" class="form-select @error('job_title_id') is-invalid @enderror" required>
            <option value="">Select Job Title</option>
            @foreach($jobTitles as $title)
                <option value="{{ $title->id }}"
                    @selected(old('job_title_id', $deduction->job_title_id ?? '') == $title->id)>
                    {{ $title->name }}
                </option>
            @endforeach
        </select>
        @error('job_title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Deduction Type --}}
    <div class="col-md-6">
        <label class="form-label">Deduction Type <span class="text-danger">*</span></label>
        <select name="deduction_type_id" class="form-select @error('deduction_type_id') is-invalid @enderror" required>
            <option value="">Select Type</option>
            @foreach($deductionTypes as $type)
                <option value="{{ $type->id }}"
                    @selected(old('deduction_type_id', $deduction->deduction_type_id ?? '') == $type->id)>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>
        @error('deduction_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Amount --}}
    <div class="col-md-4">
        <label class="form-label">Amount <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="amount" id="amount"
               value="{{ old('amount', $deduction->amount ?? '') }}"
               class="form-control @error('amount') is-invalid @enderror" required>
        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Category --}}
    <div class="col-md-4">
        <label class="form-label">Payment Plan <span class="text-danger">*</span></label>
        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
            <option value="one_time" @selected(old('category', $deduction->category ?? 'one_time') == 'one_time')>One Time</option>
            <option value="installment" @selected(old('category', $deduction->category ?? '') == 'installment')>Installment</option>
        </select>
        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Deduction Date --}}
    <div class="col-md-4">
        <label class="form-label">Deduction Date <span class="text-danger">*</span></label>
        <input type="date" name="deduction_date"
               value="{{ old('deduction_date', isset($deduction) ? $deduction->deduction_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="form-control @error('deduction_date') is-invalid @enderror" required>
        @error('deduction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Effective Date --}}
    <div class="col-md-6">
        <label class="form-label">Effective Date <span class="text-danger">*</span></label>
        <input type="date" name="effective_date"
               value="{{ old('effective_date', isset($deduction) ? $deduction->effective_date?->format('Y-m-d') : '') }}"
               class="form-control @error('effective_date') is-invalid @enderror" required>
        @error('effective_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Supporting Document --}}
    <div class="col-md-6">
        <label class="form-label">Supporting Document</label>
        <input type="file" name="supporting_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        @isset($deduction)
    @if($deduction->supporting_document)
        <small class="text-muted d-block mt-1">
            Current:
            <a href="{{ route('deductions.document', $deduction) }}" target="_blank">View file</a>
        </small>
    @endif
@endisset
    </div>

    {{-- Reason --}}
    <div class="col-12">
        <label class="form-label">Reason <span class="text-danger">*</span></label>
        <textarea name="reason" rows="3" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason', $deduction->reason ?? '') }}</textarea>
        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Remarks --}}
    <div class="col-12">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" rows="2" class="form-control">{{ old('remarks', $deduction->remarks ?? '') }}</textarea>
    </div>

</div>

{{-- ===================== INSTALLMENT SECTION ===================== --}}
<div id="installment-section" class="mt-3" style="display: none;">
    <hr>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Installment Periods <span class="text-danger">*</span></h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-installment">
            <i data-feather="plus"></i> Add Period
        </button>
    </div>

    @error('installments')
        <div class="alert alert-danger py-1">{{ $message }}</div>
    @enderror

    <div class="table-responsive">
        <table class="table table-bordered" id="installments-table">
            <thead class="table-light">
                <tr>
                    <th>Payroll Period <span class="text-danger">*</span></th>
                    <th>Period Start</th>
                    <th>Period End</th>
                    <th width="160">Amount <span class="text-danger">*</span></th>
                    <th width="70">Action</th>
                </tr>
            </thead>
            <tbody id="installments-body">
                @php
                    $oldInstallments = old('installments', isset($deduction) ? $deduction->installments->toArray() : []);
                @endphp

                @if(count($oldInstallments))
                    @foreach($oldInstallments as $i => $inst)
                        <tr class="installment-row">
                            <td>
                                <input type="text"
                                       name="installments[{{ $i }}][payroll_period]"
                                       value="{{ $inst['payroll_period'] ?? '' }}"
                                       class="form-control"
                                       placeholder="e.g. January 2026"
                                       required>
                            </td>
                            <td>
                                <input type="date"
                                       name="installments[{{ $i }}][period_start]"
                                       value="{{ isset($inst['period_start']) ? \Illuminate\Support\Carbon::parse($inst['period_start'])->format('Y-m-d') : ($inst['period_start'] ?? '') }}"
                                       class="form-control">
                            </td>
                            <td>
                                <input type="date"
                                       name="installments[{{ $i }}][period_end]"
                                       value="{{ isset($inst['period_end']) ? \Illuminate\Support\Carbon::parse($inst['period_end'])->format('Y-m-d') : ($inst['period_end'] ?? '') }}"
                                       class="form-control">
                            </td>
                            <td>
                                <input type="number" step="0.01"
                                       name="installments[{{ $i }}][amount]"
                                       value="{{ $inst['amount'] ?? '' }}"
                                       class="form-control installment-amount"
                                       required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-installment">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Total Installments</td>
                    <td class="fw-bold" id="installment-total">0.00</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <small class="text-muted">
        Total of installment amounts should equal the main Amount field.
    </small>
</div>

<div class="mt-4 d-flex gap-2">
    <a href="{{ route('deductions.index') }}" class="btn btn-secondary">Back</a>
    <button type="submit" class="btn btn-primary">
        {{ isset($deduction) ? 'Update Deduction' : 'Create Deduction' }}
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category');
    const installmentSection = document.getElementById('installment-section');
    const installmentsBody = document.getElementById('installments-body');
    const addBtn = document.getElementById('add-installment');
    let rowIndex = document.querySelectorAll('.installment-row').length;

    function toggleInstallments() {
        if (categorySelect.value === 'installment') {
            installmentSection.style.display = 'block';
            if (installmentsBody.children.length === 0) {
                addInstallmentRow();
            }
        } else {
            installmentSection.style.display = 'none';
        }
    }

    function addInstallmentRow() {
        const tr = document.createElement('tr');
        tr.className = 'installment-row';
        tr.innerHTML = `
            <td>
                <input type="text" name="installments[${rowIndex}][payroll_period]"
                       class="form-control" placeholder="e.g. January 2026" required>
            </td>
            <td>
                <input type="date" name="installments[${rowIndex}][period_start]" class="form-control">
            </td>
            <td>
                <input type="date" name="installments[${rowIndex}][period_end]" class="form-control">
            </td>
            <td>
                <input type="number" step="0.01" name="installments[${rowIndex}][amount]"
                       class="form-control installment-amount" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-installment">
                    ×
                </button>
            </td>
        `;
        installmentsBody.appendChild(tr);
        rowIndex++;
        updateTotal();
        if (window.feather) feather.replace();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.installment-amount').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('installment-total').textContent = total.toFixed(2);
    }

    categorySelect.addEventListener('change', toggleInstallments);
    addBtn.addEventListener('click', addInstallmentRow);

    installmentsBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-installment')) {
            e.target.closest('tr').remove();
            updateTotal();
        }
    });

    installmentsBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('installment-amount')) {
            updateTotal();
        }
    });

    // Init
    toggleInstallments();
    updateTotal();
});
</script>