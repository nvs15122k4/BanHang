<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ImportTikiProducts extends Command
{
    protected $signature = 'import:tiki-products
                            {path? : Directory containing crawler CSV files or a products_*.csv file}
                            {--limit= : Maximum number of new products to import from the batch}
                            {--dry-run : Validate the CSV batch and report changes without writing data}';

    protected $description = 'Import a relational Tiki crawler CSV batch into the product catalog';

    private const DEFAULT_DIRECTORY = 'app/public/products';

    /**
     * @var array<string, list<string>>
     */
    private const REQUIRED_COLUMNS = [
        'brands' => ['name', 'slug', 'description'],
        'categories' => ['name', 'slug', 'parent_slug', 'icon', 'description', 'is_new'],
        'products' => [
            'ten_sp',
            'slug',
            'loai',
            'brand_slug',
            'mo_ta',
            'anh',
            'trang_thai',
            'so_luong',
            'gia',
            'sizes',
        ],
        'product_images' => ['product_slug', 'image_url', 'is_primary', 'sort_order'],
        'product_variants' => ['product_slug', 'name'],
    ];

    public function handle(): int
    {
        $limit = $this->parseLimit();

        if ($limit === false) {
            return self::FAILURE;
        }

        try {
            $files = $this->resolveBatchFiles($this->argument('path'));
            $rows = $this->readBatch($files);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $summary = $this->newSummary();

        try {
            if ($dryRun) {
                $this->simulateImport($rows, $limit, $summary);
            } else {
                DB::transaction(function () use ($rows, $limit, &$summary): void {
                    $this->importBatch($rows, $limit, $summary);
                });
            }
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Batch: '.$files['products']);
        $this->info($dryRun ? 'Dry run complete. No data was written.' : 'Tiki product import complete.');
        $this->renderSummary($summary, $dryRun);

        return self::SUCCESS;
    }

    private function makeSafeSlug(string $text, int $maxLength = 180): string
    {
        $slug = Str::slug($text) ?: 'san-pham';

        if (strlen($slug) <= $maxLength) {
            return $slug;
        }

        $hash = substr(md5($slug), 0, 8);

        return rtrim(
            substr($slug, 0, $maxLength - 9),
            '-'
        ) . '-' . $hash;
    }

    private function parseLimit(): int|false|null
    {
        if ($this->option('limit') === null) {
            return null;
        }

        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT);

        if ($limit === false || $limit < 1) {
            $this->error('The --limit option must be a positive integer.');

            return false;
        }

        return $limit;
    }

    /**
     * @return array<string, string>
     */
    private function resolveBatchFiles(?string $path): array
    {
        $resolvedPath = blank($path)
            ? storage_path(self::DEFAULT_DIRECTORY)
            : (Str::startsWith((string) $path, ['/']) ? (string) $path : base_path((string) $path));

        if (is_dir($resolvedPath)) {
            $productsFiles = glob(rtrim($resolvedPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'products_*.csv');

            if ($productsFiles === false || $productsFiles === []) {
                throw new RuntimeException("No products_*.csv file found in directory: {$resolvedPath}");
            }

            natsort($productsFiles);
            $productsPath = (string) end($productsFiles);
        } elseif (is_file($resolvedPath)) {
            $productsPath = $resolvedPath;
        } else {
            throw new RuntimeException("CSV directory or products file not found: {$resolvedPath}");
        }

        $fileName = basename($productsPath);

        if (preg_match('/^products_(.+)\.csv$/', $fileName, $matches) !== 1) {
            throw new RuntimeException("Expected a products_<batch>.csv file, received: {$fileName}");
        }

        $directory = dirname($productsPath);
        $batch = $matches[1];
        $files = [];

        foreach (array_keys(self::REQUIRED_COLUMNS) as $type) {
            $files[$type] = $directory.DIRECTORY_SEPARATOR.$type.'_'.$batch.'.csv';

            if (! is_file($files[$type])) {
                throw new RuntimeException("Required CSV file not found for batch {$batch}: {$files[$type]}");
            }
        }

        return $files;
    }

    /**
     * @param  array<string, string>  $files
     * @return array<string, list<array<string, string>>>
     */
    private function readBatch(array $files): array
    {
        $batch = [];

        foreach ($files as $type => $path) {
            $file = fopen($path, 'r');

            if ($file === false) {
                throw new RuntimeException("Unable to read CSV file: {$path}");
            }

            $header = fgetcsv($file);

            if (! is_array($header)) {
                fclose($file);
                throw new RuntimeException("Invalid CSV header: {$path}");
            }

            $header = array_map(
                static fn ($column): string => trim((string) $column, "\xEF\xBB\xBF \t\n\r\0\x0B"),
                $header
            );

            if (count(array_diff(self::REQUIRED_COLUMNS[$type], $header)) > 0) {
                fclose($file);
                throw new RuntimeException(
                    "Invalid {$type} CSV header. Required columns: ".implode(', ', self::REQUIRED_COLUMNS[$type])
                );
            }

            $rows = [];

            while (($values = fgetcsv($file)) !== false) {
                $row = [];

                foreach ($header as $index => $column) {
                    $row[$column] = trim((string) ($values[$index] ?? ''));
                }

                $rows[] = $row;
            }

            fclose($file);
            $batch[$type] = $rows;
        }

        return $batch;
    }

    /**
     * @return array<string, int>
     */
    private function newSummary(): array
    {
        return [
            'brands_created' => 0,
            'brands_existing' => 0,
            'categories_created' => 0,
            'categories_existing' => 0,
            'products_read' => 0,
            'products_created' => 0,
            'products_existing' => 0,
            'images_created' => 0,
            'images_existing' => 0,
            'variants_created' => 0,
            'variants_existing' => 0,
            'invalid' => 0,
        ];
    }

    /**
     * @param  array<string, list<array<string, string>>>  $rows
     * @param  array<string, int>  $summary
     */
    private function simulateImport(array $rows, ?int $limit, array &$summary): void
    {
        $brandSlugs = $this->simulateBrands($rows['brands'], $summary);
        $categorySlugs = $this->simulateCategories($rows['categories'], $summary);
        [$productSlugs, $newProductSlugs] = $this->simulateProducts(
            $rows['products'],
            $brandSlugs,
            $categorySlugs,
            $limit,
            $summary
        );

        $this->simulateImages($rows['product_images'], $productSlugs, $newProductSlugs, $summary);
        $this->simulateVariants($rows['product_variants'], $productSlugs, $newProductSlugs, $summary);
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, int>  $summary
     * @return array<string, true>
     */
    private function simulateBrands(array $rows, array &$summary): array
    {
        $known = [];

        foreach ($rows as $row) {
            $slug = $row['slug'];

            if ($slug === '' || $row['name'] === '') {
                $summary['invalid']++;

                continue;
            }

            $known[$slug] = true;

            if (Brand::where('slug', $slug)->exists()) {
                $summary['brands_existing']++;
            } else {
                $summary['brands_created']++;
            }
        }

        return $known;
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, int>  $summary
     * @return array<string, true>
     */
    private function simulateCategories(array $rows, array &$summary): array
    {
        $known = [];

        foreach ($rows as $row) {
            if ($row['slug'] !== '' && $row['name'] !== '') {
                $known[$row['slug']] = true;
            }
        }

        foreach ($rows as $row) {
            $slug = $row['slug'];
            $parentSlug = $row['parent_slug'];

            if (
                $slug === ''
                || $row['name'] === ''
                || ($parentSlug !== '' && ! isset($known[$parentSlug]) && ! Category::where('slug', $parentSlug)->exists())
            ) {
                $summary['invalid']++;

                continue;
            }

            if (Category::where('slug', $slug)->exists()) {
                $summary['categories_existing']++;
            } else {
                $summary['categories_created']++;
            }
        }

        return $known;
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, true>  $brandSlugs
     * @param  array<string, true>  $categorySlugs
     * @param  array<string, int>  $summary
     * @return array{array<string, true>, array<string, true>}
     */
    private function simulateProducts(
        array $rows,
        array $brandSlugs,
        array $categorySlugs,
        ?int $limit,
        array &$summary
    ): array {
        $accepted = [];
        $new = [];

        foreach ($rows as $row) {
            $summary['products_read']++;

            if (! $this->isValidProductRow($row, $brandSlugs, $categorySlugs)) {
                $summary['invalid']++;

                continue;
            }

            $existing = $this->findExistingProduct($row['slug'], $row['anh']);

            if ($existing !== null) {
                $accepted[$row['slug']] = true;
                $summary['products_existing']++;

                continue;
            }

            if ($limit !== null && $summary['products_created'] >= $limit) {
                break;
            }

            $accepted[$row['slug']] = true;
            $new[$row['slug']] = true;
            $summary['products_created']++;
        }

        return [$accepted, $new];
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, true>  $productSlugs
     * @param  array<string, true>  $newProductSlugs
     * @param  array<string, int>  $summary
     */
    private function simulateImages(
        array $rows,
        array $productSlugs,
        array $newProductSlugs,
        array &$summary
    ): void {
        foreach ($rows as $row) {
            if (! isset($productSlugs[$row['product_slug']])) {
                continue;
            }

            $imageUrl = $this->normalizeUrl($row['image_url']);

            if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
                $summary['invalid']++;

                continue;
            }

            if (isset($newProductSlugs[$row['product_slug']])) {
                $summary['images_created']++;

                continue;
            }

            $product = Product::withTrashed()->where('slug', $row['product_slug'])->first()
                ?? Product::withTrashed()->where('anh', $imageUrl)->first();

            if ($product !== null && $product->productImages()->where('image_url', $imageUrl)->exists()) {
                $summary['images_existing']++;
            } else {
                $summary['images_created']++;
            }
        }
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, true>  $productSlugs
     * @param  array<string, true>  $newProductSlugs
     * @param  array<string, int>  $summary
     */
    private function simulateVariants(
        array $rows,
        array $productSlugs,
        array $newProductSlugs,
        array &$summary
    ): void {
        foreach ($rows as $row) {
            if (! isset($productSlugs[$row['product_slug']])) {
                continue;
            }

            if ($row['name'] === '') {
                $summary['invalid']++;

                continue;
            }

            if (isset($newProductSlugs[$row['product_slug']])) {
                $summary['variants_created']++;

                continue;
            }

            $product = Product::withTrashed()->where('slug', $row['product_slug'])->first();

            if ($product !== null && $product->variants()->where('name', $row['name'])->exists()) {
                $summary['variants_existing']++;
            } else {
                $summary['variants_created']++;
            }
        }
    }

    /**
     * @param  array<string, list<array<string, string>>>  $rows
     * @param  array<string, int>  $summary
     */
    private function importBatch(array $rows, ?int $limit, array &$summary): void
    {
        $brands = $this->importBrands($rows['brands'], $summary);
        $categories = $this->importCategories($rows['categories'], $summary);
        [$products, $createdProductIds] = $this->importProducts(
            $rows['products'],
            $brands,
            $categories,
            $limit,
            $summary
        );

        $this->importImages($rows['product_images'], $products, $summary);
        $this->importVariants($rows['product_variants'], $products, $createdProductIds, $summary);
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, int>  $summary
     * @return array<string, Brand>
     */
    private function importBrands(array $rows, array &$summary): array
    {
        $brands = [];

        foreach ($rows as $row) {
            if ($row['slug'] === '' || $row['name'] === '') {
                $summary['invalid']++;

                continue;
            }

            $brand = Brand::firstOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 'description' => $this->nullable($row['description'])]
            );
            $brands[$row['slug']] = $brand;
            $summary[$brand->wasRecentlyCreated ? 'brands_created' : 'brands_existing']++;
        }

        return $brands;
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, int>  $summary
     * @return array<string, Category>
     */
    private function importCategories(array $rows, array &$summary): array
    {
        $categories = Category::whereIn('slug', array_column($rows, 'slug'))->get()->keyBy('slug')->all();
        $pending = $rows;

        while ($pending !== []) {
            $remaining = [];
            $progress = false;

            foreach ($pending as $row) {
                $slug = $row['slug'];
                $parentSlug = $row['parent_slug'];

                if ($slug === '' || $row['name'] === '') {
                    $summary['invalid']++;

                    continue;
                }

                $parent = $parentSlug === '' ? null : ($categories[$parentSlug] ?? Category::where('slug', $parentSlug)->first());

                if ($parentSlug !== '' && $parent === null) {
                    $remaining[] = $row;

                    continue;
                }

                $category = $categories[$slug] ?? Category::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'parent_id' => $parent?->id,
                        'name' => $row['name'],
                        'icon' => $row['icon'] !== '' ? $row['icon'] : 'fas fa-tag',
                        'description' => $this->nullable($row['description']),
                        'is_new' => filter_var($row['is_new'], FILTER_VALIDATE_BOOL),
                    ]
                );
                $categories[$slug] = $category;
                $summary[$category->wasRecentlyCreated ? 'categories_created' : 'categories_existing']++;
                $progress = true;
            }

            if (! $progress) {
                $summary['invalid'] += count($remaining);

                break;
            }

            $pending = $remaining;
        }

        return $categories;
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, Brand>  $brands
     * @param  array<string, Category>  $categories
     * @param  array<string, int>  $summary
     * @return array{array<string, Product>, array<int, true>}
     */
    private function importProducts(
        array $rows,
        array $brands,
        array $categories,
        ?int $limit,
        array &$summary
    ): array {
        $products = [];
        $createdProductIds = [];

        foreach ($rows as $row) {
            $summary['products_read']++;
            $brandSlug = $row['brand_slug'];

            if (
                ! $this->isValidProductRow($row, array_fill_keys(array_keys($brands), true), array_fill_keys(array_keys($categories), true))
            ) {
                $summary['invalid']++;

                continue;
            }

            $existing = $this->findExistingProduct($row['slug'], $row['anh']);

            if ($existing !== null) {
                $products[$row['slug']] = $existing;
                $summary['products_existing']++;

                continue;
            }

            if ($limit !== null && $summary['products_created'] >= $limit) {
                break;
            }

            $product = Product::create([
                'ten_sp' => $row['ten_sp'],
                'slug' => $this->makeSafeSlug($row['slug']),
                'loai' => $row['loai'],
                'brand_id' => $brandSlug !== '' ? $brands[$brandSlug]->id : null,
                'mo_ta' => $this->nullable($row['mo_ta']),
                'anh' => $this->nullable($this->normalizeUrl($row['anh'])),
                'trang_thai' => in_array($row['trang_thai'], ['con', 'het'], true) ? $row['trang_thai'] : 'con',
                'so_luong' => (int) $row['so_luong'],
                'gia' => (int) $row['gia'],
                'sizes' => $this->decodeSizes($row['sizes']),
            ]);
            $products[$row['slug']] = $product;
            $createdProductIds[$product->id] = true;
            $summary['products_created']++;
        }

        return [$products, $createdProductIds];
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, Product>  $products
     * @param  array<string, int>  $summary
     */
    private function importImages(array $rows, array $products, array &$summary): void
    {
        foreach ($rows as $row) {
            $product = $products[$row['product_slug']] ?? null;

            if ($product === null) {
                continue;
            }

            $imageUrl = $this->normalizeUrl($row['image_url']);

            if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
                $summary['invalid']++;

                continue;
            }

            $image = ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'image_url' => $imageUrl],
                [
                    'is_primary' => filter_var($row['is_primary'], FILTER_VALIDATE_BOOL),
                    'sort_order' => max(0, (int) $row['sort_order']),
                ]
            );
            $summary[$image->wasRecentlyCreated ? 'images_created' : 'images_existing']++;
        }
    }

    /**
     * @param  list<array<string, string>>  $rows
     * @param  array<string, Product>  $products
     * @param  array<int, true>  $createdProductIds
     * @param  array<string, int>  $summary
     */
    private function importVariants(
        array $rows,
        array $products,
        array $createdProductIds,
        array &$summary
    ): void {
        $variantNames = [];

        foreach ($rows as $row) {
            $product = $products[$row['product_slug']] ?? null;

            if ($product === null) {
                continue;
            }

            if ($row['name'] === '') {
                $summary['invalid']++;

                continue;
            }

            $variant = ProductVariant::firstOrCreate([
                'product_id' => $product->id,
                'name' => $row['name'],
            ]);
            $summary[$variant->wasRecentlyCreated ? 'variants_created' : 'variants_existing']++;

            if (isset($createdProductIds[$product->id])) {
                $variantNames[$product->id][] = $row['name'];
            }
        }

        foreach ($variantNames as $productId => $names) {
            Product::whereKey($productId)->update(['sizes' => array_values(array_unique($names))]);
        }
    }

    /**
     * @param  array<string, string>  $row
     * @param  array<string, true>  $brandSlugs
     * @param  array<string, true>  $categorySlugs
     */
    private function isValidProductRow(array $row, array $brandSlugs, array $categorySlugs): bool
    {
        $quantity = filter_var($row['so_luong'], FILTER_VALIDATE_INT);
        $price = filter_var($row['gia'], FILTER_VALIDATE_INT);

        return $row['ten_sp'] !== ''
            && $this->makeSafeSlug($row['slug']) !== ''
            && isset($categorySlugs[$row['loai']])
            && ($row['brand_slug'] === '' || isset($brandSlugs[$row['brand_slug']]))
            && ($row['anh'] === '' || filter_var($this->normalizeUrl($row['anh']), FILTER_VALIDATE_URL) !== false)
            && $quantity !== false
            && $quantity >= 0
            && $price !== false
            && $price >= 1;
    }

    private function findExistingProduct(string $slug, string $imageUrl): ?Product
    {
        $product = Product::withTrashed()->where('slug', $slug)->first();
        $imageUrl = $this->normalizeUrl($imageUrl);

        if ($product !== null || $imageUrl === '') {
            return $product;
        }

        return Product::withTrashed()->where('anh', $imageUrl)->first();
    }

    private function normalizeUrl(string $url): string
    {
        return str_replace(' ', '%20', trim($url));
    }

    /**
     * @return list<string>
     */
    private function decodeSizes(string $sizes): array
    {
        $decoded = json_decode($sizes, true);

        return is_array($decoded)
            ? array_values(array_filter($decoded, static fn ($value): bool => is_string($value) && trim($value) !== ''))
            : [];
    }

    private function nullable(string $value): ?string
    {
        return $value !== '' ? $value : null;
    }

    /**
     * @param  array<string, int>  $summary
     */
    private function renderSummary(array $summary, bool $dryRun): void
    {
        $prefix = $dryRun ? 'Would create' : 'Created';

        $this->table(
            ['Metric', 'Value'],
            [
                ["{$prefix} brands", number_format($summary['brands_created'])],
                ['Existing brands', number_format($summary['brands_existing'])],
                ["{$prefix} categories", number_format($summary['categories_created'])],
                ['Existing categories', number_format($summary['categories_existing'])],
                ['Product rows read', number_format($summary['products_read'])],
                ["{$prefix} products", number_format($summary['products_created'])],
                ['Existing products', number_format($summary['products_existing'])],
                ["{$prefix} images", number_format($summary['images_created'])],
                ['Existing images', number_format($summary['images_existing'])],
                ["{$prefix} variants", number_format($summary['variants_created'])],
                ['Existing variants', number_format($summary['variants_existing'])],
                ['Invalid or unlinked rows', number_format($summary['invalid'])],
            ]
        );
    }
}
