{{-- pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Deductions Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #1F4E78; color: white; }
        h1 { text-align: center; color: #1F4E78; }
    </style>
</head>
<body>
    <h1>Staff Deduction Report</h1>
    <p>Generated: {{ $generatedAt->format('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Staff</th>
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
                    <td>{{ $d->deduction_date?->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>