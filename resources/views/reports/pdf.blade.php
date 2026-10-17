<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Deduction Report</title>
    <style>
        @page { margin: 22px 18px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        h1 { text-align: center; color: #1F4E78; margin: 0 0 6px; font-size: 24px; font-weight: bold; }
        .meta { text-align: center; color: #222; margin-bottom: 12px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td {
            border: 1px solid #555;
            padding: 6px 5px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background: #12365a;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 5px;
        }
        tr:nth-child(even) td { background: #eef2f7; }
        .text-end { text-align: right; }
        .total td { font-weight: bold; font-size: 11px; background: #D9EAD3 !important; }
    </style>
</head>
<body>
    <h1>Staff Deduction Report</h1>
    <p class="meta">Generated: {{ $generatedAt->format('d M Y h:i A') }}</p>

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