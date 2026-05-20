<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function getFilteredProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::query();

        if (! empty($filters['search'])) {
            $query->where('ten_sp', 'like', '%'.$filters['search'].'%');
        }

        if (! empty($filters['loai_filter'])) {
            $query->where('loai', $filters['loai_filter']);
        }

        if (! empty($filters['min_price'])) {
            $query->where('gia', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('gia', '<=', $filters['max_price']);
        }

        if (! empty($filters['trang_thai_filter']) && in_array($filters['trang_thai_filter'], ['con', 'het'])) {
            $query->where('trang_thai', $filters['trang_thai_filter']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('gia', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('gia', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function createProduct(array $data, ?UploadedFile $image = null, ?string $imageUrl = null): Product
    {
        if ($image) {
            $data['anh'] = $this->cloudinary->uploadImage($image);
        } elseif ($imageUrl) {
            $data['anh'] = $imageUrl;
        }

        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null, ?string $imageUrl = null): Product
    {
        if ($image) {
            $this->deleteOldImage($product->anh);
            $data['anh'] = $this->cloudinary->uploadImage($image);
        } elseif ($imageUrl !== null) {
            $this->deleteOldImage($product->anh);
            $data['anh'] = $imageUrl;
        }

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(Product $product): bool
    {
        return (bool) $product->delete();
    }

    /**
     * Xóa ảnh cũ:
     * - Cloudinary URL → xóa trên Cloudinary
     * - File local cũ  → xóa trong storage
     * - URL ngoài      → bỏ qua
     */
    private function deleteOldImage(?string $anh): void
    {
        if (empty($anh)) {
            return;
        }

        if (CloudinaryService::isCloudinaryUrl($anh)) {
            $this->cloudinary->deleteImage($anh);

            return;
        }

        // File local cũ (backward compatibility)
        if (! str_starts_with($anh, 'http://') && ! str_starts_with($anh, 'https://')) {
            Storage::disk('public')->delete('products/'.$anh);
        }
    }

    public function getHomeStatistics(): array
    {
        $products = Product::latest()->take(4)->get();

        return [
            'products' => $products,
            'totalProducts' => Product::count(),
            'productsInStock' => Product::where('trang_thai', 'con')->count(),
            'productsOutOfStock' => Product::where('trang_thai', 'het')->count(),
            'totalValue' => Product::sum('gia'),
        ];
    }
}
