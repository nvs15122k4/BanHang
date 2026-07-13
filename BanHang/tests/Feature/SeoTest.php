<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Review;
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

        $response = $this->get('/san-pham/'.$product->slug);

        $response->assertOk();
        $response->assertSee('<title>Ao So Mi Linen - Sàn Tím Vi En</title>', false);
        $response->assertSee('content="San pham linen nhe va thoang mat danh cho phong cach hien dai."', false);
        $response->assertSee('<link rel="canonical" href="https://santimvien.vn/san-pham/'.$product->slug.'">', false);
        $response->assertSee('<meta property="og:image" content="https://cdn.example.test/products/linen.jpg">', false);
        $response->assertSee('<meta property="product:price:amount" content="450000">', false);

        $productSchema = $this->getJsonLdByType($response->getContent(), 'Product');
        $breadcrumbSchema = $this->getJsonLdByType($response->getContent(), 'BreadcrumbList');

        $this->assertSame('Ao So Mi Linen', $productSchema['name']);
        $this->assertSame(['https://cdn.example.test/products/linen.jpg'], $productSchema['image']);
        $this->assertSame('450000', $productSchema['offers']['price']);
        $this->assertSame('https://schema.org/InStock', $productSchema['offers']['availability']);
        $this->assertArrayNotHasKey('aggregateRating', $productSchema);
        $this->assertSame('Sản phẩm', $breadcrumbSchema['itemListElement'][1]['name']);
        $this->assertSame('https://santimvien.vn/san-pham/'.$product->slug, $breadcrumbSchema['itemListElement'][2]['item']);
    }

    public function test_product_json_ld_reports_out_of_stock_and_fallback_image(): void
    {
        $product = Product::create([
            'ten_sp' => 'San Pham Tam Het',
            'gia' => 120000,
            'so_luong' => 0,
            'trang_thai' => 'het',
        ]);

        $response = $this->get('/san-pham/'.$product->slug);

        $response->assertOk();
        $response->assertSee('SẢN PHẨM TẠM HẾT HÀNG', false);
        $response->assertSee('default-product.svg', false);

        $productSchema = $this->getJsonLdByType($response->getContent(), 'Product');

        $this->assertSame('https://schema.org/OutOfStock', $productSchema['offers']['availability']);
        $this->assertStringContainsString('default-product.svg', $productSchema['image'][0]);
    }

    public function test_only_approved_reviews_are_exposed_in_product_schema(): void
    {
        $product = Product::create([
            'ten_sp' => 'Ao Khoac Review',
            'gia' => 680000,
            'so_luong' => 3,
            'trang_thai' => 'con',
        ]);
        $approvedUser = User::factory()->create(['name' => 'Approved Customer']);
        $pendingUser = User::factory()->create(['name' => 'Pending Customer']);
        $rejectedUser = User::factory()->create(['name' => 'Rejected Customer']);

        Review::create([
            'product_id' => $product->id,
            'user_id' => $approvedUser->id,
            'rating' => 4,
            'comment' => 'Danh gia cong khai',
            'trang_thai' => 'approved',
        ]);
        Review::create([
            'product_id' => $product->id,
            'user_id' => $pendingUser->id,
            'rating' => 1,
            'comment' => 'Danh gia cho duyet',
            'trang_thai' => 'pending',
        ]);
        Review::create([
            'product_id' => $product->id,
            'user_id' => $rejectedUser->id,
            'rating' => 2,
            'comment' => 'Danh gia bi tu choi',
            'trang_thai' => 'rejected',
        ]);

        $response = $this->get('/san-pham/'.$product->slug);
        $productSchema = $this->getJsonLdByType($response->getContent(), 'Product');

        $this->assertSame('4.0', $productSchema['aggregateRating']['ratingValue']);
        $this->assertSame('1', $productSchema['aggregateRating']['reviewCount']);
        $this->assertCount(1, $productSchema['review']);
        $this->assertSame('Approved Customer', $productSchema['review'][0]['author']['name']);
        $this->assertSame('Danh gia cong khai', $productSchema['review'][0]['reviewBody']);
        $response->assertDontSee('Danh gia cho duyet', false);
        $response->assertDontSee('Danh gia bi tu choi', false);
    }

    public function test_promotional_price_matches_html_og_and_product_schema(): void
    {
        $product = Product::create([
            'ten_sp' => 'Ao Giam Gia',
            'gia' => 1000000,
            'so_luong' => 4,
            'trang_thai' => 'con',
        ]);
        Promotion::create([
            'ten' => 'Giam hai muoi phan tram',
            'loai_km' => 'percent',
            'gia_tri' => 20,
            'ngay_bat_dau' => now()->subHour(),
            'ngay_ket_thuc' => now()->addHour(),
            'pham_vi' => 'all',
            'trang_thai' => 'active',
        ]);

        $response = $this->get('/san-pham/'.$product->slug);

        $response->assertOk();
        $response->assertSee('1,000,000đ', false);
        $response->assertSee('800,000đ', false);
        $response->assertSee('<meta property="product:price:amount" content="800000">', false);

        $productSchema = $this->getJsonLdByType($response->getContent(), 'Product');

        $this->assertSame('800000', $productSchema['offers']['price']);
    }

    public function test_fixed_promotional_price_matches_displayed_product_price(): void
    {
        $product = Product::create([
            'ten_sp' => 'Quan Giam Gia Co Dinh',
            'gia' => 750000,
            'so_luong' => 2,
            'trang_thai' => 'con',
        ]);
        Promotion::create([
            'ten' => 'Giam mot tram nghin',
            'loai_km' => 'fixed',
            'gia_tri' => 100000,
            'ngay_bat_dau' => now()->subHour(),
            'ngay_ket_thuc' => now()->addHour(),
            'pham_vi' => 'all',
            'trang_thai' => 'active',
        ]);

        $response = $this->get('/san-pham/'.$product->slug);
        $productSchema = $this->getJsonLdByType($response->getContent(), 'Product');

        $response->assertOk();
        $response->assertSee('750,000đ', false);
        $response->assertSee('650,000đ', false);
        $this->assertSame('650000', $productSchema['offers']['price']);
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

    public function test_product_slugs_are_unique_and_remain_stable_after_update(): void
    {
        $first = Product::create([
            'ten_sp' => 'Áo Thun Tím',
            'gia' => 100000,
            'so_luong' => 1,
            'trang_thai' => 'con',
        ]);
        $second = Product::create([
            'ten_sp' => 'Áo Thun Tím',
            'gia' => 120000,
            'so_luong' => 2,
            'trang_thai' => 'con',
        ]);

        $this->assertSame('ao-thun-tim', $first->slug);
        $this->assertSame('ao-thun-tim-2', $second->slug);

        $first->update(['ten_sp' => 'Tên Sản Phẩm Mới', 'gia' => 150000]);

        $this->assertSame('ao-thun-tim', $first->fresh()->slug);
    }

    public function test_legacy_product_url_redirects_permanently_to_slug_page(): void
    {
        $product = Product::create([
            'ten_sp' => 'Ao Redirect',
            'gia' => 100000,
            'so_luong' => 1,
            'trang_thai' => 'con',
        ]);

        $this->get('/products/'.$product->id)
            ->assertRedirect('/san-pham/'.$product->slug)
            ->assertStatus(301);
    }

    public function test_category_landing_page_is_canonical_and_old_filter_redirects(): void
    {
        $category = Category::create([
            'name' => 'Thời trang nữ',
            'slug' => 'women',
            'description' => 'Các mẫu thời trang nữ hiện đại.',
        ]);
        $matching = Product::create([
            'ten_sp' => 'Vay Nu',
            'loai' => $category->slug,
            'gia' => 200000,
            'so_luong' => 1,
            'trang_thai' => 'con',
        ]);
        Product::create([
            'ten_sp' => 'Ao Nam',
            'loai' => 'men',
            'gia' => 200000,
            'so_luong' => 1,
            'trang_thai' => 'con',
        ]);

        $this->get('/products?loai_filter=women')
            ->assertStatus(301)
            ->assertRedirect('/danh-muc/women');

        $response = $this->get('/danh-muc/women');

        $response
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://santimvien.vn/danh-muc/women">', false)
            ->assertSee('Các mẫu thời trang nữ hiện đại.', false)
            ->assertSee($matching->ten_sp, false)
            ->assertDontSee('Ao Nam', false);

        $breadcrumbSchema = $this->getJsonLdByType($response->getContent(), 'BreadcrumbList');
        $this->assertSame('https://santimvien.vn/danh-muc/women', $breadcrumbSchema['itemListElement'][1]['item']);
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

    public function test_public_content_pages_render_metadata_and_internal_links(): void
    {
        $this->get('/huong-dan/chon-size')
            ->assertOk()
            ->assertSee('<title>Hướng dẫn chọn size quần áo - Sàn Tím Vi En</title>', false)
            ->assertSee('<link rel="canonical" href="https://santimvien.vn/huong-dan/chon-size">', false)
            ->assertSee('Bảng size tham khảo đang dùng', false);

        $this->get('/chinh-sach/thanh-toan')
            ->assertOk()
            ->assertSee('Chuyển khoản VietQR', false)
            ->assertSee('Thanh toán khi nhận hàng (COD)', false);

        $this->get('/ho-tro/huong-dan-mua-hang')
            ->assertOk()
            ->assertSee('/huong-dan/chon-size', false)
            ->assertSee('/chinh-sach/thanh-toan', false);

        $this->get('/ho-tro/cau-hoi-thuong-gap')
            ->assertOk()
            ->assertSee('Làm sao chọn đúng size?', false);
    }

    public function test_blog_listing_and_articles_have_real_urls_and_article_schema(): void
    {
        $this->get('/blog')
            ->assertOk()
            ->assertSee('/blog/cach-chon-size-quan-ao-khi-mua-online', false)
            ->assertSee('/blog/cach-phoi-ao-thun-don-gian-hang-ngay', false)
            ->assertSee('/blog/cach-bao-quan-trang-phuc-ben-mau', false);

        $response = $this->get('/blog/cach-chon-size-quan-ao-khi-mua-online');

        $response
            ->assertOk()
            ->assertSee('<link rel="canonical" href="https://santimvien.vn/blog/cach-chon-size-quan-ao-khi-mua-online">', false)
            ->assertSee('/huong-dan/chon-size', false);

        $articleSchema = $this->getJsonLdByType($response->getContent(), 'BlogPosting');
        $this->assertSame('Cách chọn size quần áo khi mua online', $articleSchema['headline']);
        $this->assertSame('2026-05-24', $articleSchema['datePublished']);
    }

    public function test_unconfirmed_policy_and_contact_pages_are_noindex(): void
    {
        foreach ([
            '/contact',
            '/chinh-sach/giao-hang',
            '/chinh-sach/doi-tra',
            '/chinh-sach/bao-mat',
            '/chinh-sach/dieu-khoan',
        ] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('<meta name="robots" content="noindex, follow">', false);
        }
    }

    public function test_product_page_links_to_verified_support_content_without_unconfirmed_promises(): void
    {
        $product = Product::create([
            'ten_sp' => 'Ao Co Size',
            'gia' => 260000,
            'so_luong' => 4,
            'trang_thai' => 'con',
            'sizes' => ['S', 'M'],
        ]);

        $this->get('/san-pham/'.$product->slug)
            ->assertOk()
            ->assertSee('/huong-dan/chon-size', false)
            ->assertSee('/chinh-sach/giao-hang', false)
            ->assertSee('/chinh-sach/doi-tra', false)
            ->assertDontSee('Đổi trả dễ dàng trong 14 ngày', false)
            ->assertDontSee('Giao hàng toàn quốc 2-3 ngày', false);
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

        $category = Category::create([
            'name' => 'Thời trang nam',
            'slug' => 'men',
        ]);
        $visibleProduct->update(['loai' => $category->slug]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('https://santimvien.vn/san-pham/'.$visibleProduct->slug, false);
        $response->assertDontSee('https://santimvien.vn/products/'.$visibleProduct->id, false);
        $response->assertDontSee('https://santimvien.vn/san-pham/'.$deletedProduct->slug, false);
        $response->assertSee('https://santimvien.vn/danh-muc/'.$category->slug, false);
        $response->assertDontSee('/cart', false);
        $response->assertDontSee('/admin', false);
        $response->assertSee('https://santimvien.vn/huong-dan/chon-size', false);
        $response->assertSee('https://santimvien.vn/chinh-sach/thanh-toan', false);
        $response->assertSee('https://santimvien.vn/blog/cach-chon-size-quan-ao-khi-mua-online', false);
        $response->assertDontSee('https://santimvien.vn/contact', false);
        $response->assertDontSee('https://santimvien.vn/chinh-sach/giao-hang', false);
        $response->assertDontSee('https://santimvien.vn/chinh-sach/doi-tra', false);
    }

    private function getJsonLdByType(string $html, string $type): array
    {
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

        foreach ($matches[1] as $json) {
            $schema = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            if (($schema['@type'] ?? null) === $type) {
                return $schema;
            }
        }

        $this->fail('Missing JSON-LD schema type: '.$type);
    }
}
