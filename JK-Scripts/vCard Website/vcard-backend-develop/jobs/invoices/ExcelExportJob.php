<?php

namespace app\jobs\invoices;

use app\models\InvoiceQuery;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

/**
 *
 */
class ExcelExportJob extends BaseExportJob
{
    /**
     * @return string
     * @throws Exception
     */
    public function getTitle(): string
    {
        return 'Export as Excel. ' . $this->getIntervalTitle();
    }

    /**
     * @param InvoiceQuery $query
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws Exception
     * @throws Throwable
     */
    protected function onExecute(InvoiceQuery $query): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue([1, 1], $this->getIntervalTitle());
        $initialDataOffset = 1; // it is just to indicate the initial row of date

        $header = ['id', 'name', 'address', 'moneyIn', 'moneyOut', 'revenue', 'profit', 'since', 'until', 'notes'];
        foreach ($header as $index => $column) {
            // rows and columns are indexed since 1 and the first row is dates
            $sheet->setCellValue([$index + 1, 1 + $initialDataOffset], $column);
        }

        $initialDataOffset++;
        $total = $query->count();

        foreach ($query->all() as $i => $invoice) {
            $row = [
                $invoice->id,
                $invoice->location->name,
                $invoice->location->address,
                round($invoice->getInvoiceItems()->sum('[[totalIn]]') ?: 0, 2),
                round($invoice->getInvoiceItems()->sum('[[totalOut]]') ?: 0, 2),
                round($invoice->getInvoiceItems()->sum('[[revenue]]') ?: 0, 2),
                round($invoice->getInvoiceItems()->sum('[[balance]]') ?: 0, 2),
                $invoice->since,
                $invoice->until,
                $invoice->notes,
            ];

            foreach ($row as $j => $value) {
                $sheet->setCellValue([$j + 1, $i + 1 + $initialDataOffset], $value);
            }

            $this->onProgressChanged(round(($i + 1) / $total * 100, 1));
        }

        (new Xlsx($spreadsheet))->save($this->getFilePath());
        $this->onProgressChanged(100, true);
    }

    /**
     * @return string
     */
    protected function getOutputType(): string
    {
        return 'xlsx';
    }
}