<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TransactionExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $exceldata;

    public function __construct($data) {
        $this->exceldata = $data;
    }

    public function collection() {
        return $this->exceldata;
    }

    public function headings(): array {
        return [
            '#',
            'Order Id',
            'Tax Amount',
            'Shipping Charge',
            'Processing Fee',
            'Transaction Fee',
            'Commission',
            'Grand Total',
            'Transaction Id',
            'Payment Status',
            'Order Date',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:K1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)
                            ->getFont()->setBold(true)->setSize(12);
            },
        ];
    }
}
