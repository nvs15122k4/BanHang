<?php
use App\Models\Product;
use Illuminate\Support\Facades\DB;

$giayCategories = ['giay-the-thao'];
$thoiTrangCategories = ['thoi-trang-nam', 'thoi-trang-nu', 'tre-em-va-the-thao'];

$giaySizes = ['39', '40', '41', '42', '43', '44'];
$thoiTrangSizes = ['S', 'M', 'L', 'XL', 'XXL'];

$products = Product::whereIn('loai', array_merge($giayCategories, $thoiTrangCategories))->get();

$now = now();
$updatedCount = 0;

foreach ($products as $product) {
    if (in_array($product->loai, $giayCategories)) {
        $sizes = $giaySizes;
    } else {
        $sizes = $thoiTrangSizes;
    }

    $product->sizes = $sizes;
    $product->save();

    foreach ($sizes as $size) {
        DB::table('product_variants')->insertOrIgnore([
            'product_id' => $product->id,
            'name' => $size,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
    
    $updatedCount++;
}

echo "Updated $updatedCount products with sizes.\n";
