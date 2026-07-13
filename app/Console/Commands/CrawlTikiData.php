<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\CloudinaryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class CrawlTikiData extends Command
{
    protected $signature = 'crawl:tiki {keyword} {--limit=10}';
    protected $description = 'Crawl data from Tiki.vn and upload images to Cloudinary';

    public function handle(CloudinaryService $cloudinaryService)
    {
        $keyword = $this->argument('keyword');
        $limit = (int) $this->option('limit');

        $this->info("Bắt đầu crawl $limit sản phẩm cho từ khóa: $keyword");

        $url = "https://tiki.vn/api/v2/products";
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => 'application/json',
        ])->get($url, [
            'limit' => $limit,
            'q' => $keyword,
        ]);

        if (!$response->successful()) {
            $this->error("Không thể kết nối đến Tiki API.");
            return;
        }

        $products = $response->json('data', []);

        if (empty($products)) {
            $this->warn("Không tìm thấy sản phẩm nào.");
            return;
        }

        foreach ($products as $item) {
            $this->info("Đang xử lý: " . $item['name']);

            // 1. Xử lý Brand
            $brandName = $item['brand_name'] ?? 'No Brand';
            $brand = Brand::firstOrCreate(
                ['name' => $brandName],
                ['slug' => Str::slug($brandName), 'logo' => '']
            );

            // 2. Xử lý Category (Lấy category gốc hoặc tạo mới theo keyword để đơn giản)
            $categoryName = ucfirst($keyword);
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'is_active' => true]
            );

            // 3. Tải và upload ảnh lên Cloudinary
            $imageUrl = $item['thumbnail_url'] ?? null;
            $secureUrl = '';
            if ($imageUrl) {
                // Thay thế size trong url nếu có để lấy ảnh nét hơn
                $imageUrl = str_replace('280x280', '750x750', $imageUrl);
                
                try {
                    $imageContents = Http::get($imageUrl)->body();
                    $tempPath = sys_get_temp_dir() . '/' . Str::random(10) . '.jpg';
                    File::put($tempPath, $imageContents);
                    
                    // Tạo UploadedFile instance
                    $uploadedFile = new UploadedFile($tempPath, 'image.jpg', 'image/jpeg', null, true);
                    $secureUrl = $cloudinaryService->uploadImage($uploadedFile);
                    
                    File::delete($tempPath);
                } catch (\Exception $e) {
                    $this->warn("Lỗi upload ảnh cho: " . $item['name'] . " - " . $e->getMessage());
                }
            }

            // 4. Lưu vào Database
            $mo_ta = $item['short_description'] ?? 'Không có mô tả';
            if (empty($mo_ta)) {
                $mo_ta = 'Sản phẩm chính hãng từ Tiki';
            }

            Product::create([
                'ten_sp' => Str::limit($item['name'], 250, ''),
                'loai' => $category->slug,
                'brand_id' => $brand->id,
                'mo_ta' => $mo_ta,
                'anh' => $secureUrl ?: ($item['thumbnail_url'] ?? ''),
                'trang_thai' => 'con',
                'so_luong' => 100, // giả định
                'gia' => $item['price'] ?? 0,
                'sizes' => [],
            ]);

            $this->info("Đã lưu: " . $item['name']);
        }

        $this->info("Hoàn tất crawl dữ liệu.");
    }
}
