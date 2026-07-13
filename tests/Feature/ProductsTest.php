<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Mock CloudinaryService để tránh kết nối thật trong test
        $this->mock(CloudinaryService::class, function ($mock) {
            $mock->shouldReceive('uploadImage')->andReturnUsing(function ($file) {
                return 'https://cdn.example.test/products/'.$file->getClientOriginalName();
            });
        });

        // Tạo admin user cho tests
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    // Thêm SP thành công
    public function test_them_sp_thanh_cong()
    {
        $du_lieu = [
            'ten_sp' => 'iPhone 15',
            'gia' => '25000000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ];

        $response = $this->actingAs($this->admin)->post('/products', $du_lieu);
        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'ten_sp' => 'iPhone 15',
            'gia' => 25000000,
            'so_luong' => 10,
            'trang_thai' => 'con',
        ]);
    }

    public function test_them_hai_san_pham_cung_ten_tao_slug_khong_trung(): void
    {
        $data = [
            'ten_sp' => 'Áo polo nam',
            'gia' => '500000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ];

        $this->actingAs($this->admin)->post('/products', $data)->assertRedirect();
        $this->actingAs($this->admin)->post('/products', $data)->assertRedirect();

        $this->assertDatabaseHas('products', ['slug' => 'ao-polo-nam']);
        $this->assertDatabaseHas('products', ['slug' => 'ao-polo-nam-2']);
    }

    public function test_them_san_pham_voi_thuong_hieu_bien_the_mo_ta_va_nhieu_anh(): void
    {
        $response = $this->actingAs($this->admin)->post('/products', [
            'ten_sp' => 'iPhone 15',
            'brand_name' => 'Apple',
            'mo_ta' => "iPhone 15\n- 128GB đen\n- 256GB xanh",
            'gia' => '25000000',
            'so_luong' => '10',
            'trang_thai' => 'con',
            'anh' => 'https://cdn.example.test/products/iphone-main.jpg',
            'image_urls' => "https://cdn.example.test/products/iphone-side.jpg\nhttps://cdn.example.test/products/iphone-back.jpg",
            'variants_text' => "128GB đen\n256GB xanh\n512GB trắng",
        ]);

        $response->assertRedirect();
        $product = Product::where('ten_sp', 'iPhone 15')->firstOrFail();

        $this->assertDatabaseHas('brands', ['name' => 'Apple', 'slug' => 'apple']);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'brand_id' => $product->brand_id,
            'mo_ta' => "iPhone 15\n- 128GB đen\n- 256GB xanh",
        ]);
        $this->assertDatabaseHas('product_variants', ['product_id' => $product->id, 'name' => '256GB xanh']);
        $this->assertDatabaseCount('product_images', 3);
        $this->assertSame(['128GB đen', '256GB xanh', '512GB trắng'], $product->fresh()->variant_options);
    }

    public function test_them_san_pham_co_the_tao_danh_muc_con_theo_duong_dan(): void
    {
        $parent = Category::create([
            'name' => 'Thời trang nam',
            'slug' => 'thoi-trang-nam',
            'is_new' => false,
        ]);

        $response = $this->actingAs($this->admin)->post('/products', [
            'ten_sp' => 'Áo polo',
            'new_category_name' => 'Áo nam',
            'new_category_parent_id' => $parent->id,
            'gia' => '500000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ]);

        $response->assertRedirect();
        $child = Category::where('name', 'Áo nam')->firstOrFail();
        $product = Product::where('ten_sp', 'Áo polo')->firstOrFail();

        $this->assertSame($parent->id, $child->parent_id);
        $this->assertTrue($child->is_new);
        $this->assertSame($child->slug, $product->loai);
        $this->assertSame('Thời trang nam > Áo nam', Product::getLoaiList()[$child->slug]);
    }

    public function test_admin_xem_danh_muc_se_tat_nhan_moi_va_khong_the_tao_vong_lap(): void
    {
        $parent = Category::create([
            'name' => 'Thời trang nữ',
            'slug' => 'thoi-trang-nu',
            'is_new' => true,
        ]);
        $child = Category::create([
            'name' => 'Áo nữ',
            'slug' => 'ao-nu',
            'parent_id' => $parent->id,
            'is_new' => true,
        ]);

        $this->actingAs($this->admin)
            ->patchJson(route('admin.categories.seen', $parent))
            ->assertOk();
        $this->assertFalse($parent->fresh()->is_new);

        $this->actingAs($this->admin)->put(route('admin.categories.update', $parent), [
            'name' => $parent->name,
            'slug' => $parent->slug,
            'parent_id' => $child->id,
        ])->assertSessionHasErrors('parent_id');
    }

    public function test_sua_slug_danh_muc_giu_lien_ket_san_pham(): void
    {
        $category = Category::create([
            'name' => 'Áo nam',
            'slug' => 'ao-nam',
            'is_new' => false,
        ]);
        $product = Product::create([
            'ten_sp' => 'Áo polo',
            'loai' => $category->slug,
            'gia' => 500000,
            'so_luong' => 10,
            'trang_thai' => 'con',
        ]);

        $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => 'Áo polo nam',
            'slug' => 'ao-polo-nam',
        ])->assertRedirect();

        $this->assertSame('ao-polo-nam', $product->fresh()->loai);
    }

    public function test_gio_hang_luu_bien_the_tu_do_voi_khoa_an_toan(): void
    {
        $product = Product::create([
            'ten_sp' => 'iPhone 15',
            'gia' => 25000000,
            'so_luong' => 10,
            'trang_thai' => 'con',
        ]);
        $product->variants()->create(['name' => '256GB xanh']);

        $this->actingAs($this->admin)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->postJson('/cart/add', [
                'product_id' => $product->id,
                'so_luong' => 1,
                'size' => '256GB xanh',
            ])->assertOk();

        $cartKey = $product->id.'_'.sha1('256GB xanh');
        $this->assertSame('256GB xanh', session('cart')[$cartKey]['size']);
    }

    // Thêm SP thất bại - Trống tên
    public function test_them_sp_that_bai_trong_ten()
    {
        $du_lieu = [
            'ten_sp' => '',
            'gia' => '100000',
            'so_luong' => '5',
            'trang_thai' => 'con',
        ];

        $response = $this->actingAs($this->admin)->post('/products', $du_lieu);
        $response->assertSessionHasErrors(['ten_sp']);
        $this->assertDatabaseCount('products', 0);
    }

    // Thêm SP - Kiểm tra ảnh mặc định
    public function test_them_sp_kiem_tra_anh_mac_dinh()
    {
        $du_lieu = [
            'ten_sp' => 'Samsung Galaxy',
            'gia' => '15000000',
            'so_luong' => '8',
            'trang_thai' => 'con',
        ];

        $response = $this->actingAs($this->admin)->post('/products', $du_lieu);
        $response->assertRedirect();

        $product = Product::where('ten_sp', 'Samsung Galaxy')->first();
        $this->assertNotNull($product);
        $this->assertNull($product->anh);
        $this->assertStringContainsString('default-product.svg', $product->image_url);
    }

    // Thêm SP - Kiểm tra định dạng số
    public function test_them_sp_kiem_tra_dinh_dang_so()
    {
        $du_lieu = [
            'ten_sp' => 'Test Product',
            'gia' => 'abc',
            'so_luong' => 'nhiều',
            'trang_thai' => 'con',
        ];

        $response = $this->actingAs($this->admin)->post('/products', $du_lieu);
        $response->assertSessionHasErrors(['gia', 'so_luong']);
        $this->assertDatabaseCount('products', 0);
    }

    // Thêm SP - Kiểm tra trạng thái
    public function test_them_sp_kiem_tra_trang_thai()
    {
        $du_lieu = [
            'ten_sp' => 'Product Hết Hàng',
            'gia' => '500000',
            'so_luong' => '0',
            'trang_thai' => 'het',
        ];

        $response = $this->actingAs($this->admin)->post('/products', $du_lieu);
        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'ten_sp' => 'Product Hết Hàng',
            'trang_thai' => 'het',
        ]);

        $product = Product::where('ten_sp', 'Product Hết Hàng')->first();
        $this->assertEquals('Hết hàng', $product->trang_thai_status);
    }

    // Sửa SP thành công
    public function test_sua_sp_thanh_cong()
    {
        $create_data = [
            'ten_sp' => 'iPhone 14',
            'gia' => '20000000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ];

        $create_response = $this->actingAs($this->admin)->post('/products', $create_data);
        $create_response->assertRedirect();

        $product = Product::where('ten_sp', 'iPhone 14')->first();
        $this->assertNotNull($product);

        $update_data = [
            'ten_sp' => 'iPhone 14',
            'gia' => '30000000',
            'so_luong' => '5',
            'trang_thai' => 'con',
        ];

        $response = $this->actingAs($this->admin)->put(route('products.update', $product->id), $update_data);
        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'gia' => 30000000,
            'so_luong' => 5,
        ]);
    }

    // Sửa SP - Thay đổi ảnh mới
    public function test_sua_sp_thay_doi_anh_moi()
    {
        $create_data = [
            'ten_sp' => 'iPhone 15',
            'gia' => '25000000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ];

        $create_response = $this->actingAs($this->admin)->post('/products', $create_data);
        $create_response->assertRedirect();

        $product = Product::where('ten_sp', 'iPhone 15')->first();
        $this->assertNotNull($product);

        $new_image = UploadedFile::fake()->image('iphone15_new.png');

        $update_data = [
            '_method' => 'PUT',
            'ten_sp' => 'iPhone 15',
            'gia' => '25000000',
            'so_luong' => '10',
            'trang_thai' => 'con',
            'anh_file' => $new_image,
        ];

        // Use POST with _method spoofing for file uploads
        $response = $this->actingAs($this->admin)->post(route('products.update', $product->id), $update_data);
        $response->assertRedirect();

        $updated_product = Product::find($product->id);
        $this->assertNotNull($updated_product->anh);
    }

    // Sửa SP - Nhập số lượng âm
    public function test_sua_sp_nhap_so_luong_am()
    {
        $create_data = [
            'ten_sp' => 'Test Product',
            'gia' => '100000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ];

        $create_response = $this->actingAs($this->admin)->post('/products', $create_data);
        $create_response->assertRedirect();

        $product = Product::where('ten_sp', 'Test Product')->first();
        $this->assertNotNull($product);

        $update_data = [
            'ten_sp' => 'Test Product',
            'gia' => '100000',
            'so_luong' => '-5',
            'trang_thai' => 'con',
        ];

        $response = $this->actingAs($this->admin)->put(route('products.update', $product->id), $update_data);
        $response->assertSessionHasErrors(['so_luong']);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'so_luong' => 10,
        ]);
    }

    // Sửa SP - Kiểm tra ghi đè mô tả
    public function test_sua_sp_kiem_tra_ghi_de_mo_ta()
    {
        $create_data = [
            'ten_sp' => 'Test Product',
            'mo_ta' => 'Mô tả cũ',
            'gia' => '100000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ];

        $create_response = $this->actingAs($this->admin)->post('/products', $create_data);
        $create_response->assertRedirect();

        $product = Product::where('ten_sp', 'Test Product')->first();
        $this->assertNotNull($product);

        $update_data = [
            'ten_sp' => 'Test Product',
            'mo_ta' => 'Mô tả đã cập nhật',
            'gia' => '100000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ];

        $response = $this->actingAs($this->admin)->put(route('products.update', $product->id), $update_data);
        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'mo_ta' => 'Mô tả đã cập nhật',
        ]);
    }

    // Sửa SP - Kiểm tra tính toàn vẹn
    public function test_sua_sp_kiem_tra_tinh_toan_ven()
    {
        $create_data = [
            'ten_sp' => 'Test Product',
            'mo_ta' => 'Mô tả gốc',
            'gia' => '100000',
            'so_luong' => '10',
            'trang_thai' => 'con',
        ];

        $create_response = $this->actingAs($this->admin)->post('/products', $create_data);
        $create_response->assertRedirect();

        $product = Product::where('ten_sp', 'Test Product')->first();
        $this->assertNotNull($product);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'ten_sp' => 'Test Product',
            'mo_ta' => 'Mô tả gốc',
            'gia' => 100000,
            'so_luong' => 10,
            'trang_thai' => 'con',
        ]);
    }

    // Xóa SP thành công
    public function test_xoa_sp_thanh_cong()
    {
        $create_data = [
            'ten_sp' => 'Product To Delete',
            'gia' => '100000',
            'so_luong' => '5',
            'trang_thai' => 'con',
        ];

        $create_response = $this->actingAs($this->admin)->post('/products', $create_data);
        $create_response->assertRedirect();

        $product = Product::where('ten_sp', 'Product To Delete')->first();
        $this->assertNotNull($product);

        $product_id = $product->id;

        $response = $this->actingAs($this->admin)->delete(route('products.destroy', $product_id));
        $response->assertRedirect();

        $this->assertSoftDeleted('products', [
            'id' => $product_id,
        ]);
    }
}
