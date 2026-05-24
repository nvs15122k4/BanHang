<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Illuminate\Support\Str;

class ImportProducts extends Command
{
    protected $signature = 'import:products
                            {--limit= : Giới hạn số dòng import (dùng để test)}';

    protected $description = 'Import sản phẩm từ storage/app/data/products.csv lên Cloudinary';

    /**
     * Cấu trúc CSV (index bắt đầu từ 0):
     * 0:id | 1:gender | 2:masterCategory | 3:subCategory | 4:articleType
     * 5:baseColour | 6:season | 7:year | 8:usage | 9:productDisplayName
     * 10:filename | 11:link
     */
    public function handle(): void
    {
        $path = storage_path('app/data/products.csv');

        if (! file_exists($path)) {
            $this->error("Không tìm thấy file: $path");

            return;
        }

        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $limit     = $this->option('limit') ? (int) $this->option('limit') : null;
        $batchSize = 500;
        $batch     = [];
        $total     = 0;
        $skipped   = 0;
        $now       = now();
        $usedSlugs = [];

        $file = fopen($path, 'r');
        fgetcsv($file); // bỏ header

        $this->info('Bắt đầu import...');
        $bar = $this->output->createProgressBar($limit ?? 44422);
        $bar->start();

        while (($row = fgetcsv($file, 0, ',')) !== false) {
            // Bỏ qua dòng thiếu cột
            if (count($row) < 11) {
                $skipped++;
                continue;
            }

            $filename = trim($row[10]);
            $tenSp    = trim($row[9]);

            // Bỏ qua dòng thiếu tên hoặc filename
            if (empty($filename) || empty($tenSp)) {
                $skipped++;
                continue;
            }

            // Tạo URL Cloudinary từ filename (ảnh đã upload sẵn vào folder banhang/products)
            // Bỏ phần mở rộng để dùng auto format của Cloudinary
            $publicId  = pathinfo($filename, PATHINFO_FILENAME);
            $anhUrl    = "https://res.cloudinary.com/{$cloudName}/image/upload/q_auto/f_auto/banhang/products/{$filename}";

            // Sinh giá ngẫu nhiên hợp lý theo loại sản phẩm
            $gia = $this->generatePrice($row[2] ?? '', $row[4] ?? '');

            $masterCategory = trim($row[2]); // masterCategory

            // Đảm bảo danh mục masterCategory tồn tại trong bảng categories
            if (!empty($masterCategory)) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($masterCategory)],
                    [
                        'name' => $masterCategory,
                        'icon' => 'fas fa-tag',
                        'description' => null,
                    ]
                );
            }

            $batch[] = [
                'ten_sp'     => $tenSp,
                'slug'       => $this->generateUniqueSlug($tenSp, $usedSlugs),
                'loai'       => $masterCategory,
                'mo_ta'      => trim($row[8]),   // usage (Casual, Formal, Sports...)
                'anh'        => $anhUrl,
                'trang_thai' => 'con',
                'so_luong'   => rand(5, 100),
                'gia'        => $gia,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $total++;
            $bar->advance();

            if (count($batch) >= $batchSize) {
                DB::table('products')->insert($batch);
                $batch = [];
            }

            if ($limit && $total >= $limit) {
                break;
            }
        }

        // Insert phần còn lại
        if (! empty($batch)) {
            DB::table('products')->insert($batch);
        }

        fclose($file);
        $bar->finish();

        $this->newLine(2);
        $this->info("✓ Import hoàn thành!");
        $this->table(
            ['Chỉ số', 'Giá trị'],
            [
                ['Đã import', number_format($total)],
                ['Bỏ qua (thiếu dữ liệu)', number_format($skipped)],
                ['Tổng trong DB', number_format(DB::table('products')->count())],
            ]
        );
    }

    /**
     * Sinh slug duy nhất trong đợt import để khớp route public theo slug.
     *
     * @param  array<string, true>  $usedSlugs
     */
    private function generateUniqueSlug(string $name, array &$usedSlugs): string
    {
        $baseSlug = Str::slug($name) ?: 'san-pham';
        $slug = $baseSlug;
        $suffix = 2;

        while (isset($usedSlugs[$slug])) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $usedSlugs[$slug] = true;

        return $slug;
    }

    /**
     * Sinh giá hợp lý dựa theo loại và loại phụ sản phẩm.
     */
    private function generatePrice(string $category, string $articleType): int
    {
        $ranges = [
            'Footwear'     => [200000, 2000000],
            'Apparel'      => [150000, 1500000],
            'Accessories'  => [100000, 1000000],
            'Personal Care' => [50000, 500000],
            'Free Items'   => [0, 0],
        ];

        [$min, $max] = $ranges[$category] ?? [100000, 800000];

        if ($min === 0) {
            return 0;
        }

        // Làm tròn đến 1000đ
        return (int) (round(rand($min, $max) / 1000) * 1000);
    }
}
