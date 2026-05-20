<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneExpiredTrash extends Command
{
    protected $signature = 'trash:prune-expired {--days=60 : Number of days to keep soft-deleted records}';

    protected $description = 'Permanently delete soft-deleted products after the retention period.';

    public function handle(CloudinaryService $cloudinary): int
    {
        $days = max(1, (int) $this->option('days'));
        $expiredBefore = now()->subDays($days);

        $deletedProducts = 0;
        Product::onlyTrashed()
            ->where('deleted_at', '<=', $expiredBefore)
            ->chunkById(100, function ($products) use ($cloudinary, &$deletedProducts): void {
                foreach ($products as $product) {
                    $this->deleteProductImage($product->anh, $cloudinary);
                    $product->forceDelete();
                    $deletedProducts++;
                }
            });

        $this->info("Permanently deleted {$deletedProducts} products.");

        return self::SUCCESS;
    }

    private function deleteProductImage(?string $image, CloudinaryService $cloudinary): void
    {
        if (empty($image)) {
            return;
        }

        if (CloudinaryService::isCloudinaryUrl($image)) {
            $cloudinary->deleteImage($image);

            return;
        }

        if (! str_starts_with($image, 'http://') && ! str_starts_with($image, 'https://')) {
            Storage::disk('public')->delete('products/'.$image);
        }
    }
}
