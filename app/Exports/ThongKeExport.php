<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ThongKeExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Tổng quan'          => new Sheets\TongQuanSheet(),
            'Sản phẩm giá cao'   => new Sheets\TopGiaCaoSheet(),
            'Sản phẩm giá thấp'  => new Sheets\TopGiaThapSheet(),
            'Tồn kho cao nhất'   => new Sheets\TopTonKhoSheet(),
            'Theo tháng'         => new Sheets\TheoThangSheet(),
        ];
    }
}
