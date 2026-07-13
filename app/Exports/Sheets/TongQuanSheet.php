<?php

namespace App\Exports\Sheets;

use App\Models\Product;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TongQuanSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'Tổng quan';
    }

    public function array(): array
    {
        $rows = [];

        // ── Tiêu đề báo cáo ──
        $rows[] = ['BÁO CÁO THỐNG KÊ HỆ THỐNG'];
        $rows[] = ['Ngày xuất: ' . now()->format('d/m/Y H:i:s')];
        $rows[] = [];

        // ── Thống kê người dùng ──
        $rows[] = ['THỐNG KÊ NGƯỜI DÙNG', ''];
        $rows[] = ['Chỉ số', 'Giá trị'];
        $rows[] = ['Tổng người dùng',          User::count()];
        $rows[] = ['Admins',                    User::where('role', 'admin')->count()];
        $rows[] = ['Customers',                 User::where('role', 'user')->count()];
        $rows[] = ['Có địa chỉ',               User::has('addresses')->count()];
        $rows[] = ['Chưa có địa chỉ',          User::doesntHave('addresses')->count()];
        $rows[] = [];

        // ── Thống kê sản phẩm ──
        $rows[] = ['THỐNG KÊ SẢN PHẨM', ''];
        $rows[] = ['Chỉ số', 'Giá trị'];
        $rows[] = ['Tổng sản phẩm',            Product::count()];
        $rows[] = ['Đang bán (còn hàng)',       Product::where('trang_thai', 'con')->count()];
        $rows[] = ['Hết hàng',                  Product::where('trang_thai', 'het')->count()];
        $rows[] = ['Tổng tồn kho',              (int) Product::sum('so_luong')];
        $rows[] = ['Giá trung bình (VNĐ)',      (int) Product::avg('gia')];
        $rows[] = ['Giá cao nhất (VNĐ)',        (int) Product::max('gia')];
        $rows[] = ['Giá thấp nhất (VNĐ)',       (int) Product::min('gia')];
        $rows[] = ['Sắp hết hàng (< 10)',       Product::where('so_luong', '<', 10)->where('so_luong', '>', 0)->count()];
        $rows[] = ['Hết hàng (= 0)',            Product::where('so_luong', 0)->count()];

        // ── Thống kê theo loại ──
        $rows[] = [];
        $rows[] = ['THỐNG KÊ THEO LOẠI SẢN PHẨM', ''];
        $rows[] = ['Loại', 'Số lượng SP', 'Tổng tồn kho', 'Giá TB (VNĐ)'];

        $byLoai = Product::select(
                'loai',
                \DB::raw('COUNT(*) as so_sp'),
                \DB::raw('SUM(so_luong) as tong_ton'),
                \DB::raw('AVG(gia) as gia_tb')
            )
            ->groupBy('loai')
            ->orderBy('so_sp', 'desc')
            ->get();

        foreach ($byLoai as $item) {
            $rows[] = [
                $item->loai ?: '(Chưa phân loại)',
                (int) $item->so_sp,
                (int) $item->tong_ton,
                (int) $item->gia_tb,
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // Tiêu đề lớn
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Các dòng header section
        foreach ([4, 11, 20, 29] as $row) {
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->getStyle("A{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '111111']],
            ]);
        }

        // Header cột
        foreach ([5, 12, 21] as $row) {
            $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
            ]);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 35, 'B' => 20, 'C' => 20, 'D' => 20];
    }
}
