<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function getFilteredProducts(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::query()->with(['productImages', 'variants', 'brand']);

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

    public function createProduct(
        array $data,
        ?UploadedFile $image = null,
        ?string $imageUrl = null,
        array $variants = [],
        array $galleryFiles = [],
        array $galleryUrls = []
    ): Product {
        if ($image) {
            $data['anh'] = $this->cloudinary->uploadImage($image);
        } elseif ($imageUrl) {
            $data['anh'] = $imageUrl;
        }

        $product = $this->createWithUniqueSlug($data);
        $this->syncVariants($product, $variants);
        $this->syncLegacyPrimaryImage($product);
        $this->addGalleryImages($product, $galleryFiles, $galleryUrls);

        return $product->fresh(['variants', 'productImages', 'brand']);
    }

    private function createWithUniqueSlug(array $data): Product
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $data['slug'] = Product::generateUniqueSlug($data['ten_sp']);

            try {
                return Product::create($data);
            } catch (UniqueConstraintViolationException $exception) {
                $isSlugCollision = str_contains($exception->getMessage(), 'products_slug_unique');

                if (! $isSlugCollision || $attempt === 2) {
                    throw $exception;
                }
            }
        }

        throw new \LogicException('Unable to generate a unique product slug.');
    }

    public function updateProduct(
        Product $product,
        array $data,
        ?UploadedFile $image = null,
        ?string $imageUrl = null,
        array $variants = [],
        array $galleryFiles = [],
        array $galleryUrls = []
    ): Product {
        if ($image) {
            $data['anh'] = $this->cloudinary->uploadImage($image);
        } elseif ($imageUrl !== null) {
            $data['anh'] = $imageUrl;
        }

        $product->update($data);
        $this->syncVariants($product, $variants);
        $this->syncLegacyPrimaryImage($product, $image !== null || $imageUrl !== null);
        $this->addGalleryImages($product, $galleryFiles, $galleryUrls);

        return $product->fresh(['variants', 'productImages', 'brand']);
    }

    public function deleteProduct(Product $product): bool
    {
        return (bool) $product->delete();
    }

    private function syncVariants(Product $product, array $variants): void
    {
        $names = collect($variants)
            ->filter(fn ($variant) => is_string($variant) && trim($variant) !== '')
            ->map(fn ($variant) => trim($variant))
            ->unique()
            ->values();

        $product->variants()->whereNotIn('name', $names->all())->delete();

        foreach ($names as $name) {
            $product->variants()->firstOrCreate(['name' => $name]);
        }

        // Keep the legacy JSON readable while existing cart/tests finish migrating.
        $product->update(['sizes' => $names->all()]);
    }

    private function syncLegacyPrimaryImage(Product $product, bool $replacePrimary = false): void
    {
        if (empty($product->anh)) {
            return;
        }

        if ($replacePrimary) {
            $product->productImages()->update(['is_primary' => false]);
        }

        $image = $product->productImages()->firstOrCreate(
            ['image_url' => $product->anh],
            ['sort_order' => 0]
        );

        if ($replacePrimary || ! $product->productImages()->where('is_primary', true)->exists()) {
            $product->productImages()->where('id', '!=', $image->id)->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        }
    }

    private function addGalleryImages(Product $product, array $files, array $urls): void
    {
        $position = (int) $product->productImages()->max('sort_order');

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $this->addGalleryImage($product, $this->cloudinary->uploadImage($file), ++$position);
            }
        }

        foreach ($urls as $url) {
            if (is_string($url) && trim($url) !== '') {
                $this->addGalleryImage($product, trim($url), ++$position);
            }
        }
    }

    private function addGalleryImage(Product $product, string $url, int $position): void
    {
        $image = $product->productImages()->firstOrCreate(
            ['image_url' => $url],
            ['is_primary' => false, 'sort_order' => $position]
        );

        if (! $product->productImages()->where('is_primary', true)->exists()) {
            $image->update(['is_primary' => true]);
            $product->update(['anh' => $url]);
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
