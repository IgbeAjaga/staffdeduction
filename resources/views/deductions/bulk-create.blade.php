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

        <form method="POST" action="{{ route('deductions.bulk.store') }}" enctype="multipart/form-data" id="bulk-form">
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
                            <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                            @php
                                $oldTotal = old('total_amount');
                                $totalDisplay = $oldTotal !== null && $oldTotal !== ''
                                    ? number_format((float) str_replace(',', '', $oldTotal), 2, '.', ',')
                                    : '';
                            @endphp
                            <input type="text"
                                   name="total_amount"
                                   id="total-amount"
                                   inputmode="decimal"
                                   autocomplete="off"
                                   class="form-control money-input"
                                   required
                                   placeholder="e.g. 1,000,000"
                                   value="{{ $totalDisplay }}">
                            <small class="text-muted">Separators appear as you type</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Allocation Method <span class="text-danger">*</span></label>
                            <select name="allocation_method" id="allocation-method" class="form-select" required>
                                <option value="equal" @selected(old('allocation_method', 'equal') == 'equal')>
                                    Equal share
                                </option>
                                <option value="months_worked" @selected(old('allocation_method') == 'months_worked')>
                                    By months worked
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 months-worked-only" style="display: none;">
                            <label class="form-label">Incident Months <span class="text-danger">*</span></label>
                            <input type="number" min="1" step="1" name="incident_months"
                                   id="incident-months" class="form-control"
                                   value="{{ old('incident_months', 1) }}">
                            <small class="text-muted">Months the incident/loss covered</small>
                        </div>

                        <div class="col-md-4 months-worked-only" style="display: none;">
                            <label class="form-label">Monthly Amount (auto)</label>
                            <input type="text" id="monthly-amount" class="form-control" readonly value="0.00">
                            <small class="text-muted">Total ÷ Incident months</small>
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

                    <div class="alert alert-secondary mt-2 mb-0" id="allocation-hint">
                        <strong>Equal share:</strong> Total is split evenly across all staff.
                    </div>

                    <div id="bulk-installment-section" class="mt-3" style="display: none;">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Installment Periods (same for all staff) <span class="text-danger">*</span></h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-bulk-period">Add Period</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                        <small class="text-muted">Each staff amount is split equally across these periods.</small>
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
                        <span class="text-muted small equal-only">
                            Each share: <strong id="each-share">0.00</strong>
                        </span>
                        <span class="text-muted small months-worked-only" style="display: none;">
                            Total staff-months: <strong id="total-staff-months">0</strong>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-staff-row">Add Staff</button>
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
                                    <th class="months-worked-col" width="120" style="display: none;">Months Worked *</th>
                                    <th width="160">Amount *</th>
                                    <th width="60"></th>
                                </tr>
                            </thead>
                            <tbody id="staff-body"></tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="4" class="text-end" id="total-label-cell">Total</td>
                                    <td class="months-worked-col" style="display: none;"></td>
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
    const allocationSelect = document.getElementById('allocation-method');
    const incidentMonthsInput = document.getElementById('incident-months');
    const monthlyAmountEl = document.getElementById('monthly-amount');
    const staffCountEl = document.getElementById('staff-count');
    const eachShareEl = document.getElementById('each-share');
    const totalStaffMonthsEl = document.getElementById('total-staff-months');
    const runningTotalEl = document.getElementById('running-total');
    const categorySelect = document.getElementById('category');
    const installmentSection = document.getElementById('bulk-installment-section');
    const periodsBody = document.getElementById('bulk-periods-body');
    const addPeriodBtn = document.getElementById('add-bulk-period');
    const allocationHint = document.getElementById('allocation-hint');
    const form = document.getElementById('bulk-form');

    let index = 0;
    let periodIndex = 0;

    const departments = @json($departments->map(fn($d) => ['id' => $d->id, 'name' => $d->name]));
    const jobTitles   = @json($jobTitles->map(fn($j) => ['id' => $j->id, 'name' => $j->name]));

    /* ---------- Money helpers ---------- */
    function stripMoney(val) {
        if (val === null || val === undefined) return '';
        return String(val).replace(/,/g, '').replace(/[^\d.]/g, '');
    }

    function formatMoney(val) {
        let raw = stripMoney(val);
        if (raw === '' || raw === '.') return '';

        const parts = raw.split('.');
        let intPart = parts[0] || '0';
        let decPart = parts.length > 1 ? parts[1].slice(0, 2) : null;

        intPart = intPart.replace(/^0+(?=\d)/, '');
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        if (decPart !== null) {
            return intPart + '.' + decPart;
        }
        if (String(val).endsWith('.') && !String(val).includes('..')) {
            return intPart + '.';
        }
        return intPart;
    }

    function parseMoney(val) {
        const n = parseFloat(stripMoney(val));
        return isNaN(n) ? 0 : n;
    }

    function bindMoneyInput(el) {
        if (!el || el.dataset.moneyBound) return;
        el.dataset.moneyBound = '1';

        el.addEventListener('input', function () {
            const start = this.selectionStart;
            const before = this.value;
            const formatted = formatMoney(this.value);
            this.value = formatted;
            const diff = formatted.length - before.length;
            const pos = Math.max(0, (start || 0) + diff);
            try { this.setSelectionRange(pos, pos); } catch (e) {}

            if (this.id === 'total-amount') {
                redistribute();
            } else if (this.classList.contains('staff-amount')) {
                updateRunningTotal();
            }
        });

        el.addEventListener('blur', function () {
            const n = parseMoney(this.value);
            if (this.value !== '' && !isNaN(n)) {
                this.value = formatMoney(n.toFixed(2));
            }
            if (this.id === 'total-amount') {
                redistribute();
            } else if (this.classList.contains('staff-amount')) {
                updateRunningTotal();
            }
        });
    }

    bindMoneyInput(totalAmountInput);

    if (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('.money-input').forEach(el => {
                if (!el.disabled) {
                    el.value = stripMoney(el.value);
                }
            });
        });
    }

    function options(list, selected = '') {
        return list.map(i =>
            `<option value="${i.id}" ${i.id == selected ? 'selected' : ''}>${i.name}</option>`
        ).join('');
    }

    function isMonthsMode() {
        return allocationSelect.value === 'months_worked';
    }

    function staffCount() {
        return body.querySelectorAll('tr').length;
    }

    function toggleAllocationUI() {
        const months = isMonthsMode();
        document.querySelectorAll('.months-worked-only').forEach(el => {
            el.style.display = months ? '' : 'none';
        });
        document.querySelectorAll('.equal-only').forEach(el => {
            el.style.display = months ? 'none' : '';
        });
        document.querySelectorAll('.months-worked-col').forEach(el => {
            el.style.display = months ? '' : 'none';
        });

        body.querySelectorAll('.months-worked').forEach(el => {
            el.disabled = !months;
            el.required = months;
        });

        if (incidentMonthsInput) {
            incidentMonthsInput.required = months;
        }

        allocationHint.innerHTML = months
            ? '<strong>By months worked:</strong> Staff amount = Total × (months worked ÷ sum of all months worked). Monthly amount = Total ÷ Incident months.'
            : '<strong>Equal share:</strong> Total is split evenly across all staff.';

        redistribute();
    }

    function updateMonthlyDisplay() {
        const total = parseMoney(totalAmountInput.value);
        const months = parseInt(incidentMonthsInput.value, 10) || 0;
        monthlyAmountEl.value = (months > 0 && total > 0)
            ? formatMoney((total / months).toFixed(2))
            : '0.00';
    }

    function redistribute() {
        const total = parseMoney(totalAmountInput.value);
        const count = staffCount();
        staffCountEl.textContent = count;
        updateMonthlyDisplay();

        const amountInputs = body.querySelectorAll('.staff-amount');

        if (count === 0 || total <= 0) {
            amountInputs.forEach(el => el.value = '');
            eachShareEl.textContent = '0.00';
            totalStaffMonthsEl.textContent = '0';
            runningTotalEl.textContent = '0.00';
            return;
        }

        if (isMonthsMode()) {
            let sumMonths = 0;
            body.querySelectorAll('.months-worked').forEach(el => {
                if (!el.disabled) sumMonths += parseInt(el.value, 10) || 0;
            });
            totalStaffMonthsEl.textContent = sumMonths;

            if (sumMonths <= 0) {
                amountInputs.forEach(el => el.value = '');
                runningTotalEl.textContent = '0.00';
                return;
            }

            let allocated = 0;
            amountInputs.forEach((el, i) => {
                const tr = el.closest('tr');
                const m = parseInt(tr.querySelector('.months-worked').value, 10) || 0;
                if (i === amountInputs.length - 1) {
                    el.value = formatMoney((total - allocated).toFixed(2));
                } else {
                    const share = Math.floor((total * m / sumMonths) * 100) / 100;
                    el.value = formatMoney(share.toFixed(2));
                    allocated += share;
                }
            });
            eachShareEl.textContent = '—';
        } else {
            const base = Math.floor((total / count) * 100) / 100;
            let allocated = 0;
            amountInputs.forEach((el, i) => {
                if (i === amountInputs.length - 1) {
                    el.value = formatMoney((total - allocated).toFixed(2));
                } else {
                    el.value = formatMoney(base.toFixed(2));
                    allocated += base;
                }
            });
            eachShareEl.textContent = formatMoney(base.toFixed(2));
            totalStaffMonthsEl.textContent = '0';
        }

        updateRunningTotal();
    }

    function updateRunningTotal() {
        let sum = 0;
        document.querySelectorAll('.staff-amount').forEach(el => {
            sum += parseMoney(el.value);
        });
        runningTotalEl.textContent = formatMoney(sum.toFixed(2));
    }

    function addRow(data = {}) {
        const tr = document.createElement('tr');
        const incidentMax = parseInt(incidentMonthsInput.value, 10) || 12;
        const monthsMode = isMonthsMode();
        const amountVal = data.amount != null && data.amount !== ''
            ? formatMoney(Number(data.amount).toFixed(2))
            : '';

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
            <td class="months-worked-col" style="display: ${monthsMode ? '' : 'none'};">
                <input type="number" min="1" max="${incidentMax}" step="1"
                       name="staff[${index}][months_worked]"
                       class="form-control months-worked"
                       value="${data.months_worked || incidentMax}"
                       ${monthsMode ? 'required' : 'disabled'}>
            </td>
            <td>
                <input type="text" step="0.01"
                       name="staff[${index}][amount]"
                       inputmode="decimal"
                       autocomplete="off"
                       class="form-control staff-amount money-input"
                       value="${amountVal}" required>
            </td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row">×</button></td>
        `;
        body.appendChild(tr);
        bindMoneyInput(tr.querySelector('.staff-amount'));
        index++;
        redistribute();
    }

    function monthLabel(ym) {
        if (!ym || ym.length < 7) return '';
        const [y, m] = ym.split('-').map(Number);
        return new Date(y, m - 1, 1).toLocaleString('en-US', { month: 'long', year: 'numeric' });
    }

    function monthBounds(ym) {
        if (!ym || ym.length < 7) return { start: '', end: '' };
        const [y, m] = ym.split('-').map(Number);
        const start = y + '-' + String(m).padStart(2, '0') + '-01';
        const lastDay = new Date(y, m, 0).getDate();
        const end = y + '-' + String(m).padStart(2, '0') + '-' + String(lastDay).padStart(2, '0');
        return { start, end };
    }

    function addPeriodRow(data = {}) {
        const tr = document.createElement('tr');
        const idx = periodIndex;
        const ym = data.payroll_month || '';
        const bounds = monthBounds(ym);
        tr.innerHTML = `
            <td>
                <input type="month" name="installments[${idx}][payroll_month]" class="form-control payroll-month" value="${ym}" required>
                <input type="hidden" name="installments[${idx}][payroll_period]" class="payroll-period-label" value="${ym ? monthLabel(ym) : ''}">
            </td>
            <td><input type="date" name="installments[${idx}][period_start]" class="form-control period-start" value="${bounds.start}" readonly></td>
            <td><input type="date" name="installments[${idx}][period_end]" class="form-control period-end" value="${bounds.end}" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-period">×</button></td>
        `;
        periodsBody.appendChild(tr);
        periodIndex++;
    }

    periodsBody.addEventListener('change', function (e) {
        if (!e.target.classList.contains('payroll-month')) return;
        const tr = e.target.closest('tr');
        const bounds = monthBounds(e.target.value);
        tr.querySelector('.period-start').value = bounds.start;
        tr.querySelector('.period-end').value = bounds.end;
        tr.querySelector('.payroll-period-label').value = monthLabel(e.target.value);
    });

    function toggleInstallments() {
        if (categorySelect.value === 'installment') {
            installmentSection.style.display = 'block';
            if (periodsBody.children.length === 0) addPeriodRow();
        } else {
            installmentSection.style.display = 'none';
        }
    }

    addBtn.addEventListener('click', () => addRow());
    addPeriodBtn.addEventListener('click', () => addPeriodRow());
    allocationSelect.addEventListener('change', toggleAllocationUI);
    incidentMonthsInput.addEventListener('input', function () {
        const max = parseInt(this.value, 10) || 1;
        body.querySelectorAll('.months-worked').forEach(el => {
            el.max = max;
            if (parseInt(el.value, 10) > max) el.value = max;
        });
        redistribute();
    });
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

    body.addEventListener('input', function (e) {
        if (e.target.classList.contains('months-worked')) redistribute();
    });

    periodsBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-period')) {
            if (periodsBody.children.length > 1) e.target.closest('tr').remove();
            else alert('At least one period is required for installment.');
        }
    });

    addRow();
    toggleInstallments();
    toggleAllocationUI();
});
</script>
@endsection