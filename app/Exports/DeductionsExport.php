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

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download(string $filename = null): StreamedResponse
    {
        $filename = $filename ?? 'deductions_report_' . now()->format('Y_m_d_His') . '.xlsx';

        $spreadsheet = $this->build();

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Deductions');

        // ===================== Title =====================
        $sheet->setCellValue('A1', 'STAFF DEDUCTION SYSTEM');
        $sheet->mergeCells('A1:S1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A2', 'DEDUCTIONS REPORT');
        $sheet->mergeCells('A2:S2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 13,
                'color' => ['rgb' => '2E74B5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // ===================== Headings =====================
        $headings = [
            'Deduction ID',
            'Staff Name',
            'Salary Number',
            'Branch',
            'Department',
            'Job Title',
            'Deduction Type',
            'Reason',
            'Amount',
            'Category',
            'Deduction Date',
            'Effective Date',
            'Status',
            'Approved By',
            'Approval Date',
            'Applied Date',
            'Created By',
            'Created At',
            'Remarks',
        ];

        $col = 'A';
        foreach ($headings as $heading) {
            $sheet->setCellValue($col . '3', $heading);
            $col++;
        }

        // Style header row
        $sheet->getStyle('A3:S3')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
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
            $sheet->setCellValue('A' . $row, $deduction->deduction_id);
            $sheet->setCellValue('B' . $row, $deduction->staff_name);
            $sheet->setCellValue('C' . $row, $deduction->salary_number);
            $sheet->setCellValue('D' . $row, $deduction->branch?->name);
            $sheet->setCellValue('E' . $row, $deduction->department?->name);
            $sheet->setCellValue('F' . $row, $deduction->jobTitle?->name);
            $sheet->setCellValue('G' . $row, $deduction->deductionType?->name);
            $sheet->setCellValue('H' . $row, $deduction->reason);
            $sheet->setCellValue('I' . $row, (float) $deduction->amount);
            $sheet->setCellValue('J' . $row, ucfirst($deduction->category));
            $sheet->setCellValue('K' . $row, $deduction->deduction_date?->format('Y-m-d'));
            $sheet->setCellValue('L' . $row, $deduction->effective_date?->format('Y-m-d'));
            $sheet->setCellValue('M' . $row, ucfirst($deduction->status));
            $sheet->setCellValue('N' . $row, $deduction->approved_by);
            $sheet->setCellValue('O' . $row, $deduction->approval_date?->format('Y-m-d'));
            $sheet->setCellValue('P' . $row, $deduction->applied_date?->format('Y-m-d'));
            $sheet->setCellValue('Q' . $row, $deduction->creator?->name);
            $sheet->setCellValue('R' . $row, $deduction->created_at?->format('Y-m-d H:i'));
            $sheet->setCellValue('S' . $row, $deduction->remarks);

            $row++;
        }

        $lastDataRow = $row - 1;

        // Format Amount column
        $sheet->getStyle('I4:I' . $lastDataRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // ===================== Totals Row =====================
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'TOTAL');
        $sheet->setCellValue('I' . $row, $deductions->sum('amount'));

        $sheet->getStyle('A' . $row . ':S' . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3'],
            ],
        ]);

        $sheet->getStyle('I' . $row)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // ===================== Styling =====================
        // Borders
        $sheet->getStyle('A1:S' . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Auto size columns
        foreach (range('A', 'S') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Freeze panes (below title + header)
        $sheet->freezePane('A4');

        // Auto filter
        $sheet->setAutoFilter('A3:S' . $lastDataRow);

        return $spreadsheet;
    }

    protected function getData()
    {
        $query = Deduction::with([
            'branch',
            'department',
            'jobTitle',
            'deductionType',
            'creator',
        ])->latest();

        if (!empty($this->filters['branch_id'])) {
            $query->where('branch_id', $this->filters['branch_id']);
        }
        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }
        if (!empty($this->filters['deduction_type_id'])) {
            $query->where('deduction_type_id', $this->filters['deduction_type_id']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
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