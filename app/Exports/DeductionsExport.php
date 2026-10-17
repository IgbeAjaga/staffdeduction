<?php

namespace App\Exports;

use App\Models\Deduction;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DeductionsExport
{
    protected array $filters;

    protected const LAST_COL = 'T'; // 20 columns: A to T

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download(?string $filename = null): StreamedResponse
    {
        $filename = $filename ?? 'deductions_report_' . now()->format('Y_m_d_His') . '.xlsx';

        $writer = new Xlsx($this->build());

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function build(): Spreadsheet
    {
        $last = self::LAST_COL;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Deductions');

        // ===================== Title =====================
        $sheet->setCellValue('A1', 'STAFF DEDUCTION SYSTEM');
        $sheet->mergeCells("A1:{$last}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '1F4E78']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'DEDUCTIONS REPORT');
        $sheet->mergeCells("A2:{$last}2");
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2E74B5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // ===================== Headings =====================
        $headings = [
            'Deduction ID', 'Staff Name', 'Salary Number', 'Branch', 'Section',
            'Department', 'Job Title', 'Deduction Type', 'Reason', 'Amount',
            'Category', 'Deduction Date', 'Effective Date', 'Status', 'Approved By',
            'Approval Date', 'Applied Date', 'Created By', 'Created At', 'Remarks',
        ];

        $sheet->fromArray($headings, null, 'A3');

        $sheet->getStyle("A3:{$last}3")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // ===================== Data =====================
        $deductions = $this->getData();

        $row = 4;
        foreach ($deductions as $deduction) {
            $sheet->fromArray([
                $deduction->deduction_id,
                $deduction->staff_name,
                $deduction->salary_number,
                $deduction->branch?->name,
                $deduction->section?->name,
                $deduction->department?->name,
                $deduction->jobTitle?->name,
                $deduction->deductionType?->name,
                $deduction->reason,
                (float) $deduction->amount,
                ucfirst($deduction->category),
                $deduction->deduction_date?->format('Y-m-d'),
                $deduction->effective_date?->format('Y-m-d'),
                ucfirst($deduction->status),
                $deduction->approved_by,
                $deduction->approval_date?->format('Y-m-d'),
                $deduction->applied_date?->format('Y-m-d'),
                $deduction->creator?->name,
                $deduction->created_at?->format('Y-m-d H:i'),
                $deduction->remarks,
            ], null, 'A' . $row);

            $row++;
        }

        $lastDataRow = $row - 1;

        // Amount column is J
        if ($lastDataRow >= 4) {
            $sheet->getStyle('J4:J' . $lastDataRow)
                ->getNumberFormat()->setFormatCode('#,##0.00');
        }

        // ===================== Totals Row =====================
        $sheet->setCellValue('B' . $row, 'TOTAL');
        $sheet->setCellValue('J' . $row, $deductions->sum('amount'));

        $sheet->getStyle("A{$row}:{$last}{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3'],
            ],
        ]);
        $sheet->getStyle('J' . $row)
            ->getNumberFormat()->setFormatCode('#,##0.00');

        // ===================== Styling =====================
        $sheet->getStyle("A1:{$last}{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        foreach (range('A', $last) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->freezePane('A4');
        $sheet->setAutoFilter("A3:{$last}" . max($lastDataRow, 3));

        return $spreadsheet;
    }

    protected function getData()
    {
        $query = Deduction::with([
            'branch', 'section', 'department', 'jobTitle', 'deductionType', 'creator',
        ])->latest();

        foreach (['branch_id', 'section_id', 'department_id', 'deduction_type_id', 'status', 'category'] as $field) {
            if (!empty($this->filters[$field])) {
                $query->where($field, $this->filters[$field]);
            }
        }

        if (!empty($this->filters['date_from']) && !empty($this->filters['date_to'])) {
            $query->whereBetween('deduction_date', [
                $this->filters['date_from'],
                $this->filters['date_to'],
            ]);
        }

        return $query->get();
    }
}