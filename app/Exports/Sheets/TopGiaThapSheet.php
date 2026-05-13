<?php

namespace App\Exports\Sheets;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TopGiaThapSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'SP giá thấp nhất';
    }

    public function collection()
    {
        return Product::orderBy('gia', 'asc')->take(10)->get();
    }

    public function headings(): array
    {
        return ['#', 'Tên sản phẩm', 'Loại', 'Giá (VNĐ)', 'Số lượng', 'Trạng thái'];
    }

    public function map($product): array
    {
        static $i = 0;
        $i++;
        return [
            $i,
            $product->ten_sp,
            $product->loai ?: '—',
            number_format($product->gia, 0, ',', '.'),
            $product->so_luong,
            $product->trang_thai === 'con' ? 'Còn hàng' : 'Hết hàng',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111111']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 40, 'C' => 15, 'D' => 18, 'E' => 12, 'F' => 14];
    }
}
