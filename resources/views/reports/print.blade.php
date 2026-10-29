
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Deduction Report - Print</title>

    
<style>
    @page {
        size: A4 landscape;
        margin: 8mm;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        line-height: 1.4;
        color: #111;
        margin: 0;
        padding: 12px;
        background: #fff;
    }

    h2 {
        text-align: center;
        color: #17365D;
        font-size: 25px;
        font-weight: 800;
        margin: 0 0 6px;
        letter-spacing: 0.7px;
        text-transform: uppercase;
    }

    .meta {
        text-align: center;
        color: #333;
        font-size: 12px;
        margin: 0 0 16px;
    }

    .no-print {
        margin-bottom: 18px;
        display: flex;
        gap: 10px;
    }

    .no-print button {
        padding: 10px 20px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        border: 1px solid #555;
        border-radius: 4px;
        background: #17365D;
        color: #fff;
    }

    .no-print button:last-child {
        background: #fff;
        color: #111;
    }

    .report-info {
        margin-bottom: 12px;
        padding: 9px 10px;
        border: 1px solid #777;
        background: #f1f4f8;
        font-size: 12px;
        line-height: 1.5;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        border: 1.5px solid #333;
    }

    th,
    td {
        border: 1px solid #666;
        padding: 8px 6px;
        text-align: left;
        vertical-align: middle;
        overflow-wrap: break-word;
        word-wrap: break-word;
        line-height: 1.35;
    }

    th {
        background: #17365D;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        text-align: center;
        vertical-align: middle;
        padding: 10px 5px;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    td {
        font-size: 11px;
        color: #111;
    }

    thead {
        display: table-header-group;
    }

    tfoot {
        display: table-row-group;
    }

    tr {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    tbody tr:nth-child(even) td {
        background: #edf1f5;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    tbody tr:nth-child(odd) td {
        background: #fff;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .text-end {
        text-align: right;
        white-space: nowrap;
    }

    .text-center {
        text-align: center;
    }

    .text-info {
        color: #075985;
    }

    .text-danger {
        color: #a61b29;
    }

    .fw-bold {
        font-weight: 800;
    }

    .total td {
        font-weight: 800;
        font-size: 12px;
        background: #d9ead3 !important;
        color: #111;
        border-top: 2px solid #333;
        padding-top: 10px;
        padding-bottom: 10px;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .empty-row td {
        text-align: center;
        padding: 22px 10px;
        font-size: 13px;
        font-weight: bold;
    }

    .footer-note {
        margin-top: 12px;
        font-size: 10px;
        color: #333;
        text-align: right;
        border-top: 1px solid #999;
        padding-top: 6px;
    }

    @media screen {
        body {
            max-width: 1500px;
            margin: 0 auto;
        }
    }

    @media print {
        html,
        body {
            width: 100%;
            margin: 0;
            padding: 0;
            background: #fff;
            font-size: 11px;
        }

        .no-print {
            display: none !important;
        }

        h2 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .meta {
            font-size: 11px;
            margin-bottom: 10px;
        }

        .report-info {
            font-size: 11px;
            padding: 6px 8px;
            margin-bottom: 8px;
            background: #f1f4f8 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        table {
            width: 100%;
            page-break-inside: auto;
            border-collapse: collapse;
        }

        th {
            font-size: 10px;
            padding: 7px 4px;
            color: #fff !important;
            background: #17365D !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        td {
            font-size: 10.5px;
            padding: 7px 4px;
            color: #111;
        }

        .total td {
            font-size: 11px;
            background: #d9ead3 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        tbody tr:nth-child(even) td {
            background: #edf1f5 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        a {
            color: #000 !important;
            text-decoration: none !important;
        }

        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .footer-note {
            font-size: 9px;
            margin-top: 8px;
        }
    }
</style>

</head>

<body>
    <div class="no-print">
        <button type="button" onclick="window.print()">Print Report</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    <h2>Staff Deduction Report</h2>

    <p class="meta">
        Generated: {{ now()->format('d M Y, h:i A') }}
    </p>

    <div class="report-info">
        <strong>Staff in report:</strong>
        {{ $staffRows->total() ?? $staffRows->count() }}
        &nbsp; | &nbsp;
        <strong>Currency:</strong> Nigerian Naira (₦)
    </div>

    <table>
        <colgroup>
            <col style="width: 4%">
            <col style="width: 10%">
            <col style="width: 17%">
            <col style="width: 12%">
            <col style="width: 10%">
            <col style="width: 13%">
            <col style="width: 12%">
            <col style="width: 12%">
            <col style="width: 12%">
            <col style="width: 8%">
        </colgroup>

        <thead>
            <tr>
                <th>#</th>
                <th>Salary No</th>
                <th>Staff Name</th>
                <th>Branch</th>
                <th>Section</th>
                <th>Department</th>
                <th class="text-end">Deducted</th>
                <th class="text-end">To be Deducted</th>
                <th class="text-end">Pending Approval</th>
                <th class="text-center">Records</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($staffRows as $row)
                <tr>
                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        <strong>{{ $row['salary_number'] ?? '—' }}</strong>
                    </td>

                    <td>
                        {{ $row['staff_name'] ?? '—' }}
                    </td>

                    <td>
                        {{ $row['branch'] ?? '—' }}
                    </td>

                    <td>
                        {{ $row['section'] ?? '—' }}
                    </td>

                    <td>
                        {{ $row['department'] ?? '—' }}
                    </td>

                    <td class="text-end text-info fw-bold">
                        ₦{{ number_format((float) ($row['applied'] ?? 0), 2) }}
                    </td>

                    <td class="text-end text-danger fw-bold">
                        ₦{{ number_format((float) ($row['remaining'] ?? 0), 2) }}
                    </td>

                    <td class="text-end text-danger fw-bold">
                        ₦{{ number_format((float) ($row['pending'] ?? 0), 2) }}
                    </td>

                    <td class="text-center">
                        {{ number_format((int) ($row['deduction_count'] ?? 0)) }}
                    </td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="10">
                        No staff found for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if ($staffRows->count())
            @php
                $tApplied = $totals['applied']
                    ?? $staffRows->getCollection()->sum('applied');

                $tRemaining = $totals['remaining']
                    ?? $staffRows->getCollection()->sum('remaining');

                $tPending = $totals['pending']
                    ?? $staffRows->getCollection()->sum('pending');

                $tRecords = $totals['deduction_count']
                    ?? $staffRows->getCollection()->sum('deduction_count');
            @endphp

            <tfoot>
                <tr class="total">
                    <td colspan="6" class="text-end">
                        TOTAL
                        @if (isset($totals))
                            (All {{ $staffRows->total() }} Staff)
                        @else
                            (Displayed Rows)
                        @endif
                    </td>

                    <td class="text-end">
                        ₦{{ number_format((float) $tApplied, 2) }}
                    </td>

                    <td class="text-end">
                        ₦{{ number_format((float) $tRemaining, 2) }}
                    </td>

                    <td class="text-end">
                        ₦{{ number_format((float) $tPending, 2) }}
                    </td>

                    <td class="text-center">
                        {{ number_format((int) $tRecords) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer-note">
        Staff Deduction Report
    </div>
</body>
</html>
