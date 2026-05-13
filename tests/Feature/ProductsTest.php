<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\GoogleDriveService;
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

        // Mock GoogleDriveService để tránh kết nối thật trong test
        $this->mock(GoogleDriveService::class, function ($mock) {
            $mock->shouldReceive('uploadImage')->andReturnUsing(function ($file) {
                return 'https://drive.google.com/uc?export=view&id=fake_test_id_'.$file->getClientOriginalName();
            });
            $mock->shouldReceive('deleteFile')->andReturn(null);
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

        $this->assertDatabaseMissing('products', [
            'id' => $product_id,
        ]);
    }
}
