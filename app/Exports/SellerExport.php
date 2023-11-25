<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class SellerExport implements FromCollection,WithHeadings,ShouldAutoSize,WithEvents
{
    protected $seller_data;

    public function __construct($data) {
        $this->seller_data = $data;
    }

    public function collection() {
        return $this->seller_data;
    }

    public function headings(): array {
        return [
            '#',
            'BUSSINESS NAME',
            'NAME',
            'EMAIL',
            'MOBILE',
            'ABOUT',
            'BUSSINESS TYPE',
            'TRADE LICENSE',
            'EXPIRY DATE',
            'STATUS',
            'APPROVAL',
            'REMARKS',
            'CREATED DATE',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:M1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)
                            ->getFont()->setBold(true)->setSize(12);
            },
        ];
    }
}
