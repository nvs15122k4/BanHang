<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;
use XMLWriter;

class SitemapController extends Controller
{
    private const BASE_URL = 'https://santimvien.vn';

    public function index(): Response
    {
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach (['/', '/products', '/khuyen-mai', '/about', '/blog', '/contact'] as $path) {
            $this->writeUrl($xml, self::BASE_URL . $path);
        }

        Product::query()
            ->select(['id', 'updated_at'])
            ->orderBy('id')
            ->chunkById(500, function ($products) use ($xml): void {
                foreach ($products as $product) {
                    $this->writeUrl(
                        $xml,
                        self::BASE_URL . '/products/' . $product->id,
                        $product->updated_at?->toDateString()
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
