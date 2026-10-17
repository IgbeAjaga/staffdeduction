<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Deduction Report - Print</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 10px;
        }
        h2 {
            text-align: center;
            color: #1F4E78;
            font-size: 22px;
            margin: 0 0 4px;
            letter-spacing: 0.5px;
        }
        .meta {
            text-align: center;
            color: #333;
            font-size: 12px;
            margin: 0 0 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #555;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }
        th {
            background: #1F4E78;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        tbody tr:nth-child(even) td {
            background: #f2f5f9;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .text-end { text-align: right; }
        .total td {
            font-weight: bold;
            background: #D9EAD3 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .no-print { margin-bottom: 15px; }
        .no-print button {
            padding: 6px 16px;
            font-size: 13px;
            cursor: pointer;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <h2>Staff Deduction Report</h2>
    <p class="meta">Generated: {{ now()->format('d M Y h:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th style="width:2.5%">#</th>
                <th style="width:5%">ID</th>
                <th style="width:8%">Staff</th>
                <th style="width:7%">Branch</th>
                <th style="width:7%">Section</th>
                <th style="width:8.5%">Department</th>
                <th style="width:8%">Deduction Type</th>
                <th style="width:11.5%">Reason</th>
                <th style="width:7%">Amount (₦)</th>
                <th style="width:7%">Payment Plan</th>                
                <th style="width:7.5%">Effective Date</th>
                <th style="width:8%">Approved By</th>
                <th style="width:6%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deductions as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->deduction_id }}</td>
                    <td>{{ $d->staff_name }}</td>
                    <td>{{ $d->branch?->name ?? '—' }}</td>
                    <td>{{ $d->section?->name ?? '—' }}</td>
                    <td>{{ $d->department?->name ?? '—' }}</td>
                    <td>{{ $d->deductionType?->name ?? '—' }}</td>
                    <td>{{ $d->reason }}</td>
                    <td class="text-end">{{ number_format($d->amount, 2) }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $d->category)) }}</td>                    
                    <td>{{ $d->effective_date?->format('d/m/Y') }}</td>
                    <td>{{ $d->approved_by ?? '—' }}</td>
                    <td>{{ ucfirst($d->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" style="text-align:center;">No records found.</td>
                </tr>
            @endforelse
        </tbody>
        @if($deductions->count())
            <tfoot>
                <tr class="total">
                    <td colspan="8" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format($deductions->sum('amount'), 2) }}</td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>