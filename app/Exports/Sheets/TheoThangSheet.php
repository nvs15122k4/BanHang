<?php

namespace App\Exports\Sheets;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TheoThangSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'Theo tháng';
    }

    public function array(): array
    {
        $rows = [];

        // ── Người dùng đăng ký theo tháng ──
        $rows[] = ['NGƯỜI DÙNG ĐĂNG KÝ (12 THÁNG GẦN NHẤT)', '', ''];
        $rows[] = ['Tháng', 'Số người đăng ký', ''];

        $usersPerMonth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        foreach ($usersPerMonth as $row) {
            $rows[] = [$row->month, $row->total, ''];
        }

        $rows[] = [];

        // ── Sản phẩm tạo mới theo tháng ──
        $rows[] = ['SẢN PHẨM TẠO MỚI (12 THÁNG GẦN NHẤT)', '', ''];
        $rows[] = ['Tháng', 'Số sản phẩm tạo mới', ''];

        $productsPerMonth = Product::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        foreach ($productsPerMonth as $row) {
            $rows[] = [$row->month, $row->total, ''];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // Tìm dòng header section và style chúng
        $highestRow = $sheet->getHighestRow();
        for ($i = 1; $i <= $highestRow; $i++) {
            $val = $sheet->getCell("A{$i}")->getValue();
            if (str_starts_with((string)$val, 'NGƯỜI DÙNG') || str_starts_with((string)$val, 'SẢN PHẨM')) {
                $sheet->mergeCells("A{$i}:B{$i}");
                $sheet->getStyle("A{$i}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111111']],
                ]);
            }
            if ($val === 'Tháng') {
                $sheet->getStyle("A{$i}:B{$i}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                ]);
            }
        }

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 22];
    }
}
