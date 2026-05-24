<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use XMLWriter;

class SitemapController extends Controller
{
    private const BASE_URL = 'https://santimvien.vn';

    public function index(): Response
    {
        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ([
            '/',
            '/products',
            '/khuyen-mai',
            '/about',
            '/blog',
            '/blog/cach-chon-size-quan-ao-khi-mua-online',
            '/blog/cach-phoi-ao-thun-don-gian-hang-ngay',
            '/blog/cach-bao-quan-trang-phuc-ben-mau',
            '/chinh-sach/thanh-toan',
            '/ho-tro/cau-hoi-thuong-gap',
            '/ho-tro/huong-dan-mua-hang',
            '/huong-dan/chon-size',
        ] as $path) {
            $this->writeUrl($xml, self::BASE_URL.$path);
        }

        Product::query()
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($products) use ($xml): void {
                foreach ($products as $product) {
                    $this->writeUrl(
                        $xml,
                        self::BASE_URL.'/san-pham/'.$product->slug,
                        $product->updated_at?->toDateString()
                    );
                }
            });

        Category::query()
            ->whereHas('products')
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($categories) use ($xml): void {
                foreach ($categories as $category) {
                    $this->writeUrl(
                        $xml,
                        self::BASE_URL.'/danh-muc/'.$category->slug,
                        $category->updated_at?->toDateString()
                    );
                }
            });

        $xml->endElement();
        $xml->endDocument();

        return response($xml->outputMemory(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    private function writeUrl(XMLWriter $xml, string $location, ?string $lastModified = null): void
    {
        $xml->startElement('url');
        $xml->writeElement('loc', $location);

        if ($lastModified !== null) {
            $xml->writeElement('lastmod', $lastModified);
        }

        $xml->endElement();
    }
}
