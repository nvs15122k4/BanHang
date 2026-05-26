<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportTikiProducts extends Command
{
    protected $signature = 'import:tiki-products
                            {file? : CSV path; defaults to the exported Tiki products file}
                            {--limit= : Maximum number of new products to import}
                            {--quantity=20 : Initial stock quantity for each imported product}
                            {--dry-run : Validate the CSV and report changes without writing data}';

    protected $description = 'Import products from a Tiki CSV export into products and categories';

    private const DEFAULT_FILE = 'app/public/products/tiki_products_20260526_200830.csv';

    /**
     * @var list<string>
     */
    private const REQUIRED_COLUMNS = ['id', 'name', 'price', 'category', 'thumbnail_url'];

    public function handle(): int
    {
        $path = $this->resolvePath($this->argument('file'));

        if (! is_file($path)) {
            $this->error("CSV file not found: {$path}");

            return self::FAILURE;
        }

        $quantity = filter_var($this->option('quantity'), FILTER_VALIDATE_INT);

        if ($quantity === false || $quantity < 0) {
            $this->error('The --quantity option must be an integer greater than or equal to zero.');

            return self::FAILURE;
        }

        $limit = $this->option('limit') !== null
            ? filter_var($this->option('limit'), FILTER_VALIDATE_INT)
            : null;

        if ($limit === false || ($limit !== null && $limit < 1)) {
            $this->error('The --limit option must be a positive integer.');

            return self::FAILURE;
        }

        $file = fopen($path, 'r');

        if ($file === false) {
            $this->error("Unable to read CSV file: {$path}");

            return self::FAILURE;
        }

        $header = fgetcsv($file);

        if (! is_array($header)) {
            fclose($file);
            $this->error('Invalid CSV header. Required columns: '.implode(', ', self::REQUIRED_COLUMNS));

            return self::FAILURE;
        }

        $header = array_map(
            static fn ($column): string => trim((string) $column, "\xEF\xBB\xBF \t\n\r\0\x0B"),
            $header
        );

        if (! $this->hasRequiredColumns($header)) {
            fclose($file);
            $this->error('Invalid CSV header. Required columns: '.implode(', ', self::REQUIRED_COLUMNS));

            return self::FAILURE;
        }

        $columnIndexes = array_flip($header);
        $dryRun = (bool) $this->option('dry-run');
        $summary = [
            'read' => 0,
            'imported' => 0,
            'skipped_existing' => 0,
            'skipped_invalid' => 0,
        ];
        $categories = [];

        $importRows = function () use (
            $file,
            $columnIndexes,
            $quantity,
            $limit,
            $dryRun,
            &$summary,
            &$categories
        ): void {
            while (($row = fgetcsv($file)) !== false) {
                $summary['read']++;

                $data = $this->mapRow($row, $columnIndexes, $quantity);

                if ($data === null) {
                    $summary['skipped_invalid']++;

                    continue;
                }

                if (Product::withTrashed()->where('anh', $data['anh'])->exists()) {
                    $summary['skipped_existing']++;

                    continue;
                }

                $categories[$data['loai']] = $data['category_name'];

                if (! $dryRun) {
                    Category::firstOrCreate(
                        ['slug' => $data['loai']],
                        [
                            'name' => $data['category_name'],
                            'icon' => 'fas fa-tag',
                            'description' => null,
                        ]
                    );

                    Product::create([
                        'ten_sp' => $data['ten_sp'],
                        'loai' => $data['loai'],
                        'mo_ta' => null,
                        'anh' => $data['anh'],
                        'trang_thai' => $data['trang_thai'],
                        'so_luong' => $data['so_luong'],
                        'gia' => $data['gia'],
                    ]);
                }

                $summary['imported']++;

                if ($limit !== null && $summary['imported'] >= $limit) {
                    break;
                }
            }
        };

        if ($dryRun) {
            $importRows();
        } else {
            DB::transaction($importRows);
        }

        fclose($file);

        $action = $dryRun ? 'Would import' : 'Imported';
        $this->info($dryRun ? 'Dry run complete. No data was written.' : 'Tiki product import complete.');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Rows read', number_format($summary['read'])],
                [$action, number_format($summary['imported'])],
                ['Skipped existing image URL', number_format($summary['skipped_existing'])],
                ['Skipped invalid', number_format($summary['skipped_invalid'])],
                ['Categories referenced', number_format(count($categories))],
            ]
        );

        return self::SUCCESS;
    }

    private function resolvePath(?string $path): string
    {
        if (blank($path)) {
            return storage_path(self::DEFAULT_FILE);
        }

        return Str::startsWith($path, ['/']) ? $path : base_path($path);
    }

    /**
     * @param  list<string>  $header
     */
    private function hasRequiredColumns(array $header): bool
    {
        return count(array_diff(self::REQUIRED_COLUMNS, $header)) === 0;
    }

    /**
     * @param  list<string|null>  $row
     * @param  array<string, int>  $columnIndexes
     * @return array{
     *     ten_sp: string,
     *     loai: string,
     *     category_name: string,
     *     anh: string,
     *     trang_thai: string,
     *     so_luong: int,
     *     gia: int
     * }|null
     */
    private function mapRow(array $row, array $columnIndexes, int $quantity): ?array
    {
        $name = trim((string) ($row[$columnIndexes['name']] ?? ''));
        $imageUrl = trim((string) ($row[$columnIndexes['thumbnail_url']] ?? ''));
        $price = filter_var(trim((string) ($row[$columnIndexes['price']] ?? '')), FILTER_VALIDATE_INT);

        if (
            $name === ''
            || $price === false
            || $price < 1
            || filter_var($imageUrl, FILTER_VALIDATE_URL) === false
        ) {
            return null;
        }

        $categoryName = $this->normalizeCategoryName(
            trim((string) ($row[$columnIndexes['category']] ?? ''))
        );

        return [
            'ten_sp' => $name,
            'loai' => Str::slug($categoryName) ?: 'san-pham-khac',
            'category_name' => $categoryName,
            'anh' => $imageUrl,
            'trang_thai' => $quantity > 0 ? 'con' : 'het',
            'so_luong' => $quantity,
            'gia' => $price,
        ];
    }

    private function normalizeCategoryName(string $category): string
    {
        $category = preg_replace('/^Search\s*:\s*/iu', '', $category) ?? $category;
        $category = trim($category);

        return $category !== '' ? Str::ucfirst($category) : 'San pham khac';
    }
}
