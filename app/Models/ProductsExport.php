<?php

namespace App\Models;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;


class ProductsExport implements FromCollection, WithColumnFormatting, WithMapping, WithHeadings, WithEvents
{
    public function collection()
    {
        return Product::orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'UniqueId',
            'ItemName',
            'Quantity',
            'Price',
            'FarmLocation',
            'Variety',
            'MinOrderQty',
            'StartDate (MM/DD/YYYY)',
            'EndDate (MM/DD/YYYY)',
            'ShippingDays',
        ];
    }

    /**
    * @var Product $product
    */
    public function map($product): array
    {
        $price = 0;
        foreach ($product->stocks as $key => $stock) {
            $price = $stock->price;
        }

        $qty_unit_main = $product->min_qty;
        if (floatval($product->min_qty) < 1) {
            $qty_unit_main = (1000 * floatval($product->min_qty));
        }

        return [
            $product->id,
            $product->name,
            number_format($qty_unit_main, 0). ' ' .$product->secondary_unit,
            $price,
            $product->manufacturer_location,
            $product->variation,
            '',
            Date::dateTimeToExcel(new \DateTime()),
            Date::dateTimeToExcel(new \DateTime()),
            1,
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $event->sheet->getDelegate()->getStyle('A1:J1')
                    ->getFont()
                    ->setBold(true);

            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_XLSX22,
            'I' => NumberFormat::FORMAT_DATE_XLSX22,
        ];
    }
}
