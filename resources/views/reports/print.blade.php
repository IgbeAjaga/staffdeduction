{{-- print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Print Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f0f0f0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <h2>Staff Deductions Report</h2>
    <table>
        <thead>
            <tr>
                <th>Deduction ID</th>
                <th>Staff Name</th>
                <th>Branch</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deductions as $d)
                <tr>
                    <td>{{ $d->deduction_id }}</td>
                    <td>{{ $d->staff_name }}</td>
                    <td>{{ $d->branch?->name }}</td>
                    <td>{{ number_format($d->amount, 2) }}</td>
                    <td>{{ ucfirst($d->status) }}</td>
                    <td>{{ $d->deduction_date?->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>