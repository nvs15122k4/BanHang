<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SanPhamExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::orderBy('created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tên sản phẩm',
            'Loại',
            'Mô tả',
            'Giá (VNĐ)',
            'Số lượng',
            'Trạng thái',
            'Ảnh',
            'Ngày tạo',
            'Ngày cập nhật',
        ];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->ten_sp,
            $product->loai ?: '—',
            $product->mo_ta,
            (int) $product->gia,
            (int) $product->so_luong,
            $product->trang_thai === 'con' ? 'Còn hàng' : 'Hết hàng',
            $product->anh ?? 'Không có ảnh',
            $product->created_at?->format('d/m/Y H:i:s') ?? '—',
            $product->updated_at?->format('d/m/Y H:i:s') ?? '—',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 35,
            'C' => 15,
            'D' => 40,
            'E' => 15,
            'F' => 12,
            'G' => 14,
            'H' => 30,
            'I' => 20,
            'J' => 20,
        ];
    }
}
