<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_returns_public_metadata(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title>Sàn Tím Vi En - Thời trang Việt phong cách</title>', false);
        $response->assertSee('<meta name="robots" content="index, follow">', false);
        $response->assertSee('<link rel="canonical" href="https://santimvien.vn/">', false);
        $response->assertSee('<meta property="og:title" content="Sàn Tím Vi En - Thời trang Việt phong cách">', false);
    }

    public function test_product_page_outputs_dynamic_metadata(): void
    {
        $product = Product::create([
            'ten_sp' => 'Ao So Mi Linen',
            'mo_ta' => 'San pham linen nhe va thoang mat danh cho phong cach hien dai.',
            'anh' => 'https://cdn.example.test/products/linen.jpg',
            'gia' => 450000,
            'so_luong' => 5,
            'trang_thai' => 'con',
        ]);

        $response = $this->get('/products/' . $product->id);

        $response->assertOk();
        $response->assertSee('<title>Ao So Mi Linen - Sàn Tím Vi En</title>', false);
        $response->assertSee('content="San pham linen nhe va thoang mat danh cho phong cach hien dai."', false);
        $response->assertSee('<link rel="canonical" href="https://santimvien.vn/products/' . $product->id . '">', false);
        $response->assertSee('<meta property="og:image" content="https://cdn.example.test/products/linen.jpg">', false);
    }

    public function test_product_filter_is_noindex_while_pagination_is_self_canonical(): void
    {
        $this->get('/products?sort=price_asc')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="https://santimvien.vn/products">', false);

        $this->get('/products?page=2')
            ->assertOk()
            ->assertSee('<meta name="robots" content="index, follow">', false)
            ->assertSee('<link rel="canonical" href="https://santimvien.vn/products?page=2">', false);

        $this->get('/khuyen-mai?sort=newest')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('<link rel="canonical" href="https://santimvien.vn/khuyen-mai">', false);
    }

    public function test_auth_and_private_pages_are_noindex(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        $user = User::factory()->create();

        $this->actingAs($user)->get('/cart')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_robots_file_declares_private_areas_and_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /admin/', $robots);
        $this->assertStringContainsString('Disallow: /checkout', $robots);
        $this->assertStringContainsString('Sitemap: https://santimvien.vn/sitemap.xml', $robots);
    }

    public function test_sitemap_contains_public_urls_and_excludes_soft_deleted_products(): void
    {
        $visibleProduct = Product::create([
            'ten_sp' => 'Visible Product',
            'gia' => 100000,
            'so_luong' => 1,
            'trang_thai' => 'con',
        ]);
        $deletedProduct = Product::create([
            'ten_sp' => 'Deleted Product',
            'gia' => 100000,
            'so_luong' => 1,
            'trang_thai' => 'con',
        ]);
        $deletedProduct->delete();

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('https://santimvien.vn/products/' . $visibleProduct->id, false);
        $response->assertDontSee('https://santimvien.vn/products/' . $deletedProduct->id, false);
        $response->assertDontSee('/cart', false);
        $response->assertDontSee('/admin', false);
    }
}
