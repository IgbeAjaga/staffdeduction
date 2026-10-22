@extends('layouts.app')

@section('title', 'Bulk / Group Deduction')
@section('page-title', 'Bulk / Group Deduction')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <h2 class="content-header-title float-start mb-0">Bulk / Group Deduction</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('deductions.index') }}">Deductions</a></li>
                        <li class="breadcrumb-item active">Bulk Create</li>
                    </ol>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('deductions.bulk.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card mb-2">
                <div class="card-header">
                    <h4 class="card-title mb-0">Shared Details (applies to all staff)</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" @selected(old('branch_id') == $b->id)>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" class="form-select" required>
                                <option value="">Select Section</option>
                                @foreach($sections as $s)
                                    <option value="{{ $s->id }}" @selected(old('section_id') == $s->id)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Deduction Type <span class="text-danger">*</span></label>
                            <select name="deduction_type_id" class="form-select" required>
                                <option value="">Select Type</option>
                                @foreach($deductionTypes as $t)
                                    <option value="{{ $t->id }}" @selected(old('deduction_type_id') == $t->id)>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select" required>
                                <option value="one_time" @selected(old('category', 'one_time') == 'one_time')>One Time</option>
                                <option value="installment" @selected(old('category') == 'installment')>Installment</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Deduction Date <span class="text-danger">*</span></label>
                            <input type="date" name="deduction_date"
                                   value="{{ old('deduction_date', now()->format('Y-m-d')) }}"
                                   class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Effective Date <span class="text-danger">*</span></label>
                            <input type="date" name="effective_date"
                                   value="{{ old('effective_date') }}"
                                   class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Total Amount</label>
                            <input type="number" step="0.01" min="0.01"
                                   id="total-amount"
                                   class="form-control"
                                   placeholder="e.g. 1000000"
                                   value="{{ old('total_amount') }}">
                            <small class="text-muted">Shared equally across staff (optional helper)</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Supporting Document</label>
                            <input type="file" name="supporting_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" rows="2" class="form-control" required>{{ old('reason') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" rows="2" class="form-control">{{ old('remarks') }}</textarea>
                        </div>
                    </div>

                    {{-- INSTALLMENT PERIODS (shared for all staff) --}}
<div id="bulk-installment-section" class="mt-3" style="display: none;">
    <hr>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Installment Periods (same for all staff) <span class="text-danger">*</span></h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-bulk-period">
            Add Period
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="bulk-periods-table">
            <thead class="table-light">
                <tr>
                    <th width="220">Payroll Month *</th>
                    <th>Period Start</th>
                    <th>Period End</th>
                    <th width="60"></th>
                </tr>
            </thead>
            <tbody id="bulk-periods-body"></tbody>
        </table>
    </div>
    <small class="text-muted">
        Select month/year. Start and end dates fill automatically for Payroll Due filtering.
        Each staff amount is split equally across these periods.
    </small>
</div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-1">
                    <h4 class="card-title mb-0">
                        Staff Members
                        <span class="badge bg-primary" id="staff-count">0</span>
                    </h4>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <span class="text-muted small">
                            Each share: <strong id="each-share">0.00</strong>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-staff-row">
                            Add Staff
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="staff-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff Name *</th>
                                    <th>Salary No *</th>
                                    <th>Department</th>
                                    <th>Job Title</th>
                                    <th width="140">Amount *</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody id="staff-body"></tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="4" class="text-end">Total</td>
                                    <td id="running-total">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('deductions.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create All Deductions</button>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.getElementById('staff-body');
    const addBtn = document.getElementById('add-staff-row');
    const totalAmountInput = document.getElementById('total-amount');
    const staffCountEl = document.getElementById('staff-count');
    const eachShareEl = document.getElementById('each-share');
    const runningTotalEl = document.getElementById('running-total');
    const categorySelect = document.getElementById('category');
    const installmentSection = document.getElementById('bulk-installment-section');
    const periodsBody = document.getElementById('bulk-periods-body');
    const addPeriodBtn = document.getElementById('add-bulk-period');

    let index = 0;
    let periodIndex = 0;

    const departments = @json($departments->map(fn($d) => ['id' => $d->id, 'name' => $d->name]));
    const jobTitles   = @json($jobTitles->map(fn($j) => ['id' => $j->id, 'name' => $j->name]));

    function options(list, selected = '') {
        return list.map(i =>
            `<option value="${i.id}" ${i.id == selected ? 'selected' : ''}>${i.name}</option>`
        ).join('');
    }

    function staffCount() {
        return body.querySelectorAll('tr').length;
    }

    function redistribute() {
        const total = parseFloat(totalAmountInput.value) || 0;
        const count = staffCount();
        staffCountEl.textContent = count;

        if (count === 0 || total <= 0) {
            eachShareEl.textContent = '0.00';
            runningTotalEl.textContent = '0.00';
            document.querySelectorAll('.staff-amount').forEach(el => el.value = '');
            return;
        }

        const base = Math.floor((total / count) * 100) / 100;
        let allocated = 0;
        const amounts = body.querySelectorAll('.staff-amount');
        amounts.forEach((el, i) => {
            if (i === amounts.length - 1) {
                el.value = (total - allocated).toFixed(2);
            } else {
                el.value = base.toFixed(2);
                allocated += base;
            }
        });
        eachShareEl.textContent = base.toFixed(2);
        updateRunningTotal();
    }

    function updateRunningTotal() {
        let sum = 0;
        document.querySelectorAll('.staff-amount').forEach(el => {
            sum += parseFloat(el.value) || 0;
        });
        runningTotalEl.textContent = sum.toFixed(2);
    }

    function addRow(data = {}) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="staff[${index}][staff_name]" class="form-control" value="${data.staff_name || ''}" required></td>
            <td><input type="text" name="staff[${index}][salary_number]" class="form-control" value="${data.salary_number || ''}" required></td>
            <td>
                <select name="staff[${index}][department_id]" class="form-select">
                    <option value="">— Optional —</option>
                    ${options(departments, data.department_id)}
                </select>
            </td>
            <td>
                <select name="staff[${index}][job_title_id]" class="form-select">
                    <option value="">— Optional —</option>
                    ${options(jobTitles, data.job_title_id)}
                </select>
            </td>
            <td><input type="number" step="0.01" name="staff[${index}][amount]" class="form-control staff-amount" value="${data.amount || ''}" required></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">×</button></td>
        `;
        body.appendChild(tr);
        index++;
        redistribute();
    }

    function monthLabel(ym) {
    // ym = "2026-10" → "October 2026"
    if (!ym || ym.length < 7) return '';
    const [y, m] = ym.split('-').map(Number);
    const d = new Date(y, m - 1, 1);
    return d.toLocaleString('en-US', { month: 'long', year: 'numeric' });
}

function monthBounds(ym) {
    // returns { start: 'YYYY-MM-DD', end: 'YYYY-MM-DD' }
    if (!ym || ym.length < 7) return { start: '', end: '' };
    const [y, m] = ym.split('-').map(Number);
    const start = `${y}-${String(m).padStart(2, '0')}-01`;
    const lastDay = new Date(y, m, 0).getDate();
    const end = `${y}-${String(m).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
    return { start, end };
}

function addPeriodRow(data = {}) {
    const tr = document.createElement('tr');
    const idx = periodIndex;
    const ym = data.payroll_month || data.period_start?.slice(0, 7) || '';
    const bounds = monthBounds(ym);

    tr.innerHTML = `
        <td>
            <input type="month"
                   name="installments[${idx}][payroll_month]"
                   class="form-control payroll-month"
                   value="${ym}"
                   required>
            <input type="hidden"
                   name="installments[${idx}][payroll_period]"
                   class="payroll-period-label"
                   value="${ym ? monthLabel(ym) : ''}">
        </td>
        <td>
            <input type="date"
                   name="installments[${idx}][period_start]"
                   class="form-control period-start"
                   value="${data.period_start || bounds.start || ''}"
                   readonly>
        </td>
        <td>
            <input type="date"
                   name="installments[${idx}][period_end]"
                   class="form-control period-end"
                   value="${data.period_end || bounds.end || ''}"
                   readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-period">×</button>
        </td>
    `;
    periodsBody.appendChild(tr);
    periodIndex++;
}

// Auto-fill start/end + label when month changes
periodsBody.addEventListener('change', function (e) {
    if (!e.target.classList.contains('payroll-month')) return;
    const tr = e.target.closest('tr');
    const ym = e.target.value;
    const bounds = monthBounds(ym);
    tr.querySelector('.period-start').value = bounds.start;
    tr.querySelector('.period-end').value = bounds.end;
    tr.querySelector('.payroll-period-label').value = monthLabel(ym);
});

    function toggleInstallments() {
        if (categorySelect.value === 'installment') {
            installmentSection.style.display = 'block';
            if (periodsBody.children.length === 0) {
                addPeriodRow();
            }
        } else {
            installmentSection.style.display = 'none';
        }
    }

    addBtn.addEventListener('click', () => addRow());
    addPeriodBtn.addEventListener('click', () => addPeriodRow());
    totalAmountInput.addEventListener('input', redistribute);
    categorySelect.addEventListener('change', toggleInstallments);

    body.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            if (staffCount() > 1) {
                e.target.closest('tr').remove();
                redistribute();
            } else {
                alert('At least one staff member is required.');
            }
        }
    });

    periodsBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-period')) {
            if (periodsBody.children.length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('At least one period is required for installment.');
            }
        }
    });

    body.addEventListener('input', function (e) {
        if (e.target.classList.contains('staff-amount')) {
            updateRunningTotal();
        }
    });

    addRow();
    toggleInstallments();
});
</script>
@endsection