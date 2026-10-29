<div class="row g-3">

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

    <div class="col-md-6">
        <label class="form-label">Staff Name <span class="text-danger">*</span></label>
        <input type="text" name="staff_name"
               value="{{ old('staff_name', $deduction->staff_name ?? '') }}"
               class="form-control @error('staff_name') is-invalid @enderror" required>
        @error('staff_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Salary Number <span class="text-danger">*</span></label>
        <input type="text" name="salary_number"
               value="{{ old('salary_number', $deduction->salary_number ?? '') }}"
               class="form-control @error('salary_number') is-invalid @enderror" required>
        @error('salary_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Department</label>
        <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
            <option value="">— Optional —</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old('department_id', $deduction->department_id ?? '') == $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Job Title</label>
        <select name="job_title_id" class="form-select @error('job_title_id') is-invalid @enderror">
            <option value="">— Optional —</option>
            @foreach($jobTitles as $title)
                <option value="{{ $title->id }}"
                    @selected(old('job_title_id', $deduction->job_title_id ?? '') == $title->id)>
                    {{ $title->name }}
                </option>
            @endforeach
        </select>
        @error('job_title_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

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

    <div class="col-md-4">
        <label class="form-label">Amount <span class="text-danger">*</span></label>
        @php
            $amountVal = old('amount', $deduction->amount ?? '');
            $amountDisplay = $amountVal !== '' && $amountVal !== null
                ? number_format((float) $amountVal, 2, '.', ',')
                : '';
        @endphp
        <input type="text"
               name="amount"
               id="amount"
               inputmode="decimal"
               autocomplete="off"
               value="{{ $amountDisplay }}"
               class="form-control money-input @error('amount') is-invalid @enderror"
               required>
        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">Example: 1,250,000.00</small>
    </div>

    <div class="col-md-4">
        <label class="form-label">Payment Plan <span class="text-danger">*</span></label>
        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
            <option value="one_time" @selected(old('category', $deduction->category ?? 'one_time') == 'one_time')>One Time</option>
            <option value="installment" @selected(old('category', $deduction->category ?? '') == 'installment')>Installment</option>
        </select>
        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Deduction Date <span class="text-danger">*</span></label>
        <input type="date" name="deduction_date"
               value="{{ old('deduction_date', isset($deduction) ? $deduction->deduction_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="form-control @error('deduction_date') is-invalid @enderror" required>
        @error('deduction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Effective Date <span class="text-danger">*</span></label>
        <input type="date" name="effective_date"
               value="{{ old('effective_date', isset($deduction) ? $deduction->effective_date?->format('Y-m-d') : '') }}"
               class="form-control @error('effective_date') is-invalid @enderror" required>
        @error('effective_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

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

    <div class="col-12">
        <label class="form-label">Reason <span class="text-danger">*</span></label>
        <textarea name="reason" rows="3" class="form-control @error('reason') is-invalid @enderror" required>{{ old('reason', $deduction->reason ?? '') }}</textarea>
        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" rows="2" class="form-control">{{ old('remarks', $deduction->remarks ?? '') }}</textarea>
    </div>

</div>

{{-- INSTALLMENTS --}}
<div id="installment-section" class="mt-3" style="display: none;">
    <hr>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Installment Periods <span class="text-danger">*</span></h5>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-installment">
            Add Period
        </button>
    </div>

    @error('installments')
        <div class="alert alert-danger py-1">{{ $message }}</div>
    @enderror
    @error('installments.*.payroll_month')
        <div class="alert alert-danger py-1">{{ $message }}</div>
    @enderror

    <div class="table-responsive">
        <table class="table table-bordered" id="installments-table">
            <thead class="table-light">
                <tr>
                    <th width="200">Payroll Month *</th>
                    <th>Period Start</th>
                    <th>Period End</th>
                    <th width="180">Amount *</th>
                    <th width="70">Action</th>
                </tr>
            </thead>
            <tbody id="installments-body">
                @php
                    $oldInstallments = old('installments', isset($deduction) ? $deduction->installments->toArray() : []);
                @endphp

                @foreach($oldInstallments as $i => $inst)
                    @php
                        $ym = old("installments.$i.payroll_month");
                        if (!$ym && !empty($inst['period_start'])) {
                            $ym = \Illuminate\Support\Carbon::parse($inst['period_start'])->format('Y-m');
                        }
                        if (!$ym && !empty($inst['payroll_period'])) {
                            try {
                                $ym = \Illuminate\Support\Carbon::parse('01 ' . $inst['payroll_period'])->format('Y-m');
                            } catch (\Throwable $e) {
                                if (preg_match('/^\d{4}-\d{2}$/', $inst['payroll_period'])) {
                                    $ym = $inst['payroll_period'];
                                }
                            }
                        }
                        $startVal = old("installments.$i.period_start", isset($inst['period_start']) ? \Illuminate\Support\Carbon::parse($inst['period_start'])->format('Y-m-d') : '');
                        $endVal = old("installments.$i.period_end", isset($inst['period_end']) ? \Illuminate\Support\Carbon::parse($inst['period_end'])->format('Y-m-d') : '');
                        $instAmt = old("installments.$i.amount", $inst['amount'] ?? '');
                        $instAmtDisplay = $instAmt !== '' && $instAmt !== null
                            ? number_format((float) $instAmt, 2, '.', ',')
                            : '';
                    @endphp
                    <tr class="installment-row">
                        <td>
                            <input type="month"
                                   name="installments[{{ $i }}][payroll_month]"
                                   value="{{ $ym }}"
                                   class="form-control payroll-month"
                                   required>
                            <input type="hidden"
                                   name="installments[{{ $i }}][payroll_period]"
                                   class="payroll-period-label"
                                   value="{{ $inst['payroll_period'] ?? '' }}">
                        </td>
                        <td>
                            <input type="date"
                                   name="installments[{{ $i }}][period_start]"
                                   value="{{ $startVal }}"
                                   class="form-control period-start"
                                   readonly>
                        </td>
                        <td>
                            <input type="date"
                                   name="installments[{{ $i }}][period_end]"
                                   value="{{ $endVal }}"
                                   class="form-control period-end"
                                   readonly>
                        </td>
                        <td>
                            <input type="text"
                                   name="installments[{{ $i }}][amount]"
                                   value="{{ $instAmtDisplay }}"
                                   inputmode="decimal"
                                   autocomplete="off"
                                   class="form-control installment-amount money-input"
                                   required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-installment">×</button>
                        </td>
                    </tr>
                @endforeach
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
    <small class="text-muted">Select month/year. Start/end fill automatically. Total should match main Amount.</small>
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
    const form = document.querySelector('form');
    let rowIndex = document.querySelectorAll('.installment-row').length;

    /* ---------- Money formatting ---------- */
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
        // Keep trailing dot while typing decimals
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

            // Rough caret restore
            const diff = formatted.length - before.length;
            const pos = Math.max(0, (start || 0) + diff);
            try { this.setSelectionRange(pos, pos); } catch (e) {}
        });

        el.addEventListener('blur', function () {
            const n = parseMoney(this.value);
            if (this.value !== '' && !isNaN(n)) {
                this.value = formatMoney(n.toFixed(2));
            }
        });
    }

    document.querySelectorAll('.money-input').forEach(bindMoneyInput);

    /* Strip commas before submit so Laravel gets plain numbers */
    if (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('.money-input').forEach(el => {
                if (!el.disabled) {
                    el.value = stripMoney(el.value);
                }
            });
        });
    }

    /* ---------- Month helpers ---------- */
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

    function applyMonthToRow(tr, ym) {
        const bounds = monthBounds(ym);
        const startEl = tr.querySelector('.period-start');
        const endEl = tr.querySelector('.period-end');
        const labelEl = tr.querySelector('.payroll-period-label');
        if (startEl) startEl.value = bounds.start;
        if (endEl) endEl.value = bounds.end;
        if (labelEl) labelEl.value = monthLabel(ym);
    }

    function toggleInstallments() {
        const on = categorySelect.value === 'installment';
        installmentSection.style.display = on ? 'block' : 'none';
        installmentSection.querySelectorAll('input').forEach(el => { el.disabled = !on; });
        if (on && installmentsBody.children.length === 0) {
            addInstallmentRow();
        }
        updateTotal();
    }

    function addInstallmentRow() {
        const tr = document.createElement('tr');
        tr.className = 'installment-row';
        tr.innerHTML = `
            <td>
                <input type="month" name="installments[${rowIndex}][payroll_month]"
                       class="form-control payroll-month" required>
                <input type="hidden" name="installments[${rowIndex}][payroll_period]"
                       class="payroll-period-label" value="">
            </td>
            <td>
                <input type="date" name="installments[${rowIndex}][period_start]"
                       class="form-control period-start" readonly>
            </td>
            <td>
                <input type="date" name="installments[${rowIndex}][period_end]"
                       class="form-control period-end" readonly>
            </td>
            <td>
                <input type="text" name="installments[${rowIndex}][amount]"
                       inputmode="decimal" autocomplete="off"
                       class="form-control installment-amount money-input" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-installment">×</button>
            </td>
        `;
        installmentsBody.appendChild(tr);
        bindMoneyInput(tr.querySelector('.money-input'));
        rowIndex++;
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.installment-amount').forEach(input => {
            if (!input.disabled) total += parseMoney(input.value);
        });
        document.getElementById('installment-total').textContent = formatMoney(total.toFixed(2));
    }

    categorySelect.addEventListener('change', toggleInstallments);
    addBtn.addEventListener('click', addInstallmentRow);

    installmentsBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-installment')) {
            e.target.closest('tr').remove();
            updateTotal();
        }
    });

    installmentsBody.addEventListener('change', function (e) {
        if (e.target.classList.contains('payroll-month')) {
            applyMonthToRow(e.target.closest('tr'), e.target.value);
        }
    });

    installmentsBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('installment-amount')) {
            updateTotal();
        }
    });

    installmentsBody.querySelectorAll('.installment-row').forEach(tr => {
        const monthEl = tr.querySelector('.payroll-month');
        if (monthEl && monthEl.value) {
            applyMonthToRow(tr, monthEl.value);
        }
    });

    toggleInstallments();
    updateTotal();
});
</script>