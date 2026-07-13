<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = [
    'hero_1' => '/home/nvs1512/.gemini/antigravity-cli/brain/a3d0700a-2ae3-4606-bc95-6bb2b39ec40d/hero_1_1782184136039.jpg',
    'hero_2' => '/home/nvs1512/.gemini/antigravity-cli/brain/a3d0700a-2ae3-4606-bc95-6bb2b39ec40d/hero_2_1782184146658.jpg',
    'hero_3' => '/home/nvs1512/.gemini/antigravity-cli/brain/a3d0700a-2ae3-4606-bc95-6bb2b39ec40d/hero_3_1782184160812.jpg',
    'cat_women' => '/home/nvs1512/.gemini/antigravity-cli/brain/a3d0700a-2ae3-4606-bc95-6bb2b39ec40d/cat_women_1782184197878.jpg',
    'cat_men' => '/home/nvs1512/.gemini/antigravity-cli/brain/a3d0700a-2ae3-4606-bc95-6bb2b39ec40d/cat_men_1782184208190.jpg',
    'cat_kids' => '/home/nvs1512/.gemini/antigravity-cli/brain/a3d0700a-2ae3-4606-bc95-6bb2b39ec40d/cat_kids_1782184219407.jpg',
    'about_hero' => '/home/nvs1512/.gemini/antigravity-cli/brain/a3d0700a-2ae3-4606-bc95-6bb2b39ec40d/about_hero_1782184232246.jpg',
    'avatar_1' => '/home/nvs1512/.gemini/antigravity-cli/brain/a3d0700a-2ae3-4606-bc95-6bb2b39ec40d/avatar_1_1782184243042.jpg',
];

$cloudinary = new Cloudinary\Cloudinary([
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],
    'url' => [
        'secure' => true,
    ],
]);

$urls = [];
foreach ($files as $name => $path) {
    if (file_exists($path)) {
        try {
            $result = $cloudinary->uploadApi()->upload($path, [
                'folder' => 'santimvien/assets',
                'resource_type' => 'image',
                'transformation' => [
                    'quality' => 'auto',
                    'fetch_format' => 'auto',
                ],
            ]);
            $urls[$name] = $result['secure_url'];
            echo "$name: " . $urls[$name] . "\n";
        } catch (\Exception $e) {
            echo "Failed $name: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Missing $path\n";
    }
}
file_put_contents('uploaded_urls.json', json_encode($urls, JSON_PRETTY_PRINT));
