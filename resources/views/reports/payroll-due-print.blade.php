<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Due Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { text-align: center; color: #1F4E78; margin-bottom: 4px; }
        .meta { text-align: center; color: #666; margin-bottom: 16px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; text-align: left; }
        th { background: #1F4E78; color: #fff; }
        .text-end { text-align: right; }
        .totals { font-weight: bold; background: #f0f0f0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:12px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <h1>Payroll Due – Staff Deductions</h1>
    <p class="meta">
        Generated: {{ $generatedAt->format('d M Y h:i A') }}
        @if(!empty($filters['month']))
            · Month: {{ \Carbon\Carbon::createFromFormat('Y-m', $filters['month'])->format('F Y') }}
        @endif
    </p>

    <p>
        <strong>Total due:</strong> ₦{{ number_format($stats['total_due'], 2) }}
        · <strong>Staff:</strong> {{ $stats['staff_count'] }}
        · <strong>Lines:</strong> {{ $stats['line_count'] }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Staff</th>
                <th>Salary No</th>
                <th>Branch</th>
                <th>Section</th>
                <th>Period</th>
                <th class="text-end">Amount Due</th>
                <th>ID</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row['staff_name'] }}</td>
                    <td>{{ $row['salary_number'] }}</td>
                    <td>{{ $row['branch'] ?? '—' }}</td>
                    <td>{{ $row['section'] ?? '—' }}</td>
                    <td>{{ $row['payroll_period'] ?? '—' }}</td>
                    <td class="text-end">{{ number_format($row['amount_due'], 2) }}</td>
                    <td>{{ $row['deduction_id'] }}</td>
                    <td>{{ $row['type'] ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="6" class="text-end">TOTAL</td>
                <td class="text-end">{{ number_format($stats['total_due'], 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>